<?php

namespace App\Services;

use App\Events\MessageReplySent;
use App\Events\NewMessageReceived;
use App\Models\ConnectedAccount;
use App\Models\OAuthConfiguration;
use App\Services\Zernio\ZernioClient;
use App\Services\Zernio\ZernioTenantService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class UnifiedHelpDeskService
{
    protected $cacheTimeout = 300; // 5 minutes

    public function __construct(protected WhatsAppBusinessService $whatsAppService, protected FacebookMessengerService $facebookMessengerService, protected GmailService $gmailService, protected OutlookService $outlookService, protected ImapService $imapService, protected Pop3Service $pop3Service, protected ?ZernioClient $zernioClient = null, protected ?ZernioTenantService $zernioTenants = null) {}

    public function getAllMessages($accountId = null, $useCache = true, ?Model $team = null)
    {
        $cacheKey = 'messages_'.($accountId ?? 'all');

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $messages = collect();
        $errors = collect();

        try {
            $messages = $this->fetchMessagesFromAllPlatforms($accountId, $errors, $team);
        } catch (Throwable $e) {
            Log::error('Critical error fetching unified messages: '.$e->getMessage());
            throw $e;
        }

        if ($errors->isNotEmpty()) {
            Log::warning('Some errors occurred while fetching messages:', $errors->toArray());
        }

        $sortedMessages = $messages->sortByDesc('timestamp');

        if ($useCache) {
            Cache::put($cacheKey, $sortedMessages, $this->cacheTimeout);
        }

        return $sortedMessages;
    }

    protected function fetchMessagesFromAllPlatforms($accountId, &$errors, ?Model $team = null)
    {
        $messages = collect();

        // Fetch messages from each platform in parallel using async operations
        $platforms = [
            'whatsapp' => fn ($config) => $this->whatsAppService->getMessages($config),
            'facebook' => fn ($config) => $this->facebookMessengerService->getUnreadMessages($config),
            'gmail' => fn ($config) => $this->gmailService->getUnreadMessages($config),
            'outlook' => fn ($config) => $this->outlookService->getUnreadMessages($config),
            'microsoft365' => fn ($config) => $this->outlookService->getUnreadMessages($config),
            'imap' => fn ($config) => $this->imapService->getUnreadMessages($config),
            'pop3' => fn ($config) => $this->pop3Service->getUnreadMessages($config),
        ];

        foreach ($platforms as $platform => $fetcher) {
            try {
                $configs = $this->getActiveConfigs($platform, $accountId);

                foreach ($configs as $config) {
                    try {
                        $platformMessages = $fetcher($config)
                            ->map(fn ($msg) => $this->formatMessage($msg, $platform, $config));
                        $messages = $messages->merge($platformMessages);

                        // Dispatch event for new messages
                        foreach ($platformMessages as $message) {
                            Event::dispatch(new NewMessageReceived($message));
                        }
                    } catch (Throwable $e) {
                        $errors->push([
                            'platform' => $platform,
                            'config_id' => $config->id,
                            'error' => $e->getMessage(),
                        ]);
                        Log::error("Error fetching {$platform} messages for config {$config->id}: ".$e->getMessage());

                        continue;
                    }
                }
            } catch (Throwable $e) {
                $errors->push([
                    'platform' => $platform,
                    'error' => $e->getMessage(),
                ]);
                Log::error("Error processing platform {$platform}: ".$e->getMessage());

                continue;
            }
        }

        if ($this->zernioClient !== null && $this->zernioTenants !== null && $team !== null && (string) config('services.zernio.api_key') !== '') {
            try {
                $response = $this->zernioClient->listConversations(array_filter([
                    'profileId' => $this->zernioTenants->ensureProfile($team),
                    'limit' => 100,
                    'sortOrder' => 'desc',
                ]));

                foreach (data_get($response, 'data', []) as $conversation) {
                    if (! is_array($conversation) || ! isset($conversation['id'], $conversation['accountId'])) {
                        continue;
                    }

                    $message = $this->formatZernioConversation($conversation);
                    $messages->push($message);
                    Event::dispatch(new NewMessageReceived($message));
                }
            } catch (Throwable $e) {
                $errors->push(['platform' => 'zernio', 'error' => $e->getMessage()]);
                Log::error('Error processing Zernio inbox: '.$e->getMessage());
            }
        }

        return $messages;
    }

    protected function getActiveConfigs($platform, $accountId = null)
    {
        return OAuthConfiguration::where('service_name', $platform)
            ->where('is_active', true)
            ->when($accountId, fn ($query) => $query->where('id', $accountId))
            ->get();
    }

    public function sendReply($messageId, $content, $channel, string $accountId, ?Model $team = null)
    {
        if ($channel === 'zernio') {
            if ($this->zernioClient === null || $this->zernioTenants === null || $team === null || (string) config('services.zernio.api_key') === '') {
                throw new InvalidArgumentException('Zernio is not configured.');
            }

            $profileId = $this->zernioTenants->ensureProfile($team);
            $result = $this->zernioClient->sendInboxMessage((string) $messageId, $accountId, (string) $content, $profileId);
            Cache::forget('messages_all');
            Event::dispatch(new MessageReplySent($messageId, $content, $channel, $accountId));

            return $result;
        }

        $config = OAuthConfiguration::findOrFail($accountId);

        try {
            $result = match ($channel) {
                'whatsapp' => $this->whatsAppService->sendMessage($messageId, $content, $config),
                'facebook' => $this->facebookMessengerService->sendReply(
                    $messageId,
                    $content,
                    ConnectedAccount::ofType('facebook')->primary()->first()
                ),
                'gmail' => $this->gmailService->sendReply($messageId, $content, $config),
                'outlook', 'microsoft365' => $this->outlookService->sendReply($messageId, $content, $config),
                'imap' => $this->imapService->sendReply($messageId, $content, $config),
                'pop3' => $this->pop3Service->sendReply($messageId, $content, $config),
                default => throw new InvalidArgumentException("Unsupported channel: {$channel}")
            };

            // Clear cache after sending reply
            Cache::forget('messages_'.$accountId);
            Cache::forget('messages_all');

            // Dispatch event for sent reply
            Event::dispatch(new MessageReplySent($messageId, $content, $channel, $accountId));

            return $result;
        } catch (Throwable $e) {
            Log::error("Failed to send reply on {$channel}: ".$e->getMessage());
            throw $e;
        }
    }

    protected function formatMessage(array $message, $channel, $config): array
    {
        return [
            'id' => $message['id'],
            'channel' => $channel,
            'account_id' => $config->id,
            'account_name' => $config->account_name,
            'from' => $message['from'],
            'content' => $message['message'] ?? $message['content'],
            'timestamp' => $this->normalizeTimestamp($message['timestamp'] ?? $message['created_time']),
            'thread_id' => $message['thread_id'] ?? null,
            'attachments' => $message['attachments'] ?? [],
            'status' => $message['status'] ?? 'received',
            'priority' => $this->calculatePriority($message),
            'metadata' => [
                'service_specific_data' => $message,
                'config_id' => $config->id,
                'platform_specific' => $this->getPlatformSpecificData($message, $channel),
            ],
        ];
    }

    protected function normalizeTimestamp($timestamp): Carbon
    {
        if (is_numeric($timestamp)) {
            return Carbon::createFromTimestamp($timestamp);
        }

        return Carbon::parse($timestamp);
    }

    protected function calculatePriority(array $message): string
    {
        // Implement priority calculation logic based on keywords, sender, etc.
        $priority = 'normal';
        $urgentKeywords = ['urgent', 'asap', 'emergency', 'critical'];

        $content = strtolower($message['message'] ?? $message['content'] ?? '');
        if ($content !== '') {
            foreach ($urgentKeywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $priority = 'high';
                    break;
                }
            }
        }

        return $priority;
    }

    protected function getPlatformSpecificData(array $message, $channel): array
    {
        return match ($channel) {
            'whatsapp' => [
                'message_type' => $message['type'] ?? 'text',
                'phone_number' => $message['phone_number'] ?? null,
            ],
            'facebook' => [
                'page_id' => $message['page_id'] ?? null,
                'sender_id' => $message['sender_id'] ?? null,
            ],
            'gmail', 'outlook', 'microsoft365', 'imap', 'pop3' => [
                'subject' => $message['subject'] ?? null,
                'cc' => $message['cc'] ?? [],
                'bcc' => $message['bcc'] ?? [],
            ],
            default => [],
        };
    }

    /** @param array<string, mixed> $conversation @return array<string, mixed> */
    protected function formatZernioConversation(array $conversation): array
    {
        return [
            'id' => $conversation['id'],
            'channel' => 'zernio',
            'account_id' => $conversation['accountId'],
            'account_name' => $conversation['accountUsername'] ?? $conversation['accountId'],
            'from' => [
                'id' => $conversation['participantId'] ?? null,
                'name' => $conversation['participantName'] ?? null,
            ],
            'content' => $conversation['lastMessage'] ?? '',
            'timestamp' => $this->normalizeTimestamp($conversation['updatedTime'] ?? now()->toIso8601String()),
            'thread_id' => $conversation['id'],
            'attachments' => [],
            'status' => $conversation['status'] ?? 'received',
            'priority' => $this->calculatePriority($conversation),
            'metadata' => [
                'service_specific_data' => $conversation,
                'config_id' => $conversation['accountId'],
                'platform_specific' => [
                    'platform' => $conversation['platform'] ?? null,
                    'url' => $conversation['url'] ?? null,
                    'unread_count' => $conversation['unreadCount'] ?? 0,
                ],
            ],
        ];
    }
}
