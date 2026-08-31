<?php

declare(strict_types=1);

namespace App\Services\Zernio;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class ZernioClient
{
    public function request(): PendingRequest
    {
        $apiKey = (string) config('services.zernio.api_key');
        if ($apiKey === '') {
            throw new ZernioException('Zernio is not configured.');
        }

        return Http::baseUrl(rtrim((string) config('services.zernio.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->withToken($apiKey)
            ->timeout(max(1, (int) config('services.zernio.timeout', 30)))
            ->retry(max(0, (int) config('services.zernio.retries', 2)), 200, throw: false);
    }

    /** @return array<string, mixed> */
    public function createPost(array $payload): array
    {
        return $this->send('post', '/posts', $payload);
    }

    /** @return array<string, mixed> */
    public function createProfile(string $name, ?string $description = null): array
    {
        return $this->send('post', '/profiles', array_filter([
            'name' => $name,
            'description' => $description,
        ], static fn (mixed $value): bool => $value !== null));
    }

    /** @return array<string, mixed> */
    public function getPost(string $postId): array
    {
        return $this->send('get', '/posts/'.rawurlencode($postId));
    }

    /** @return array<string, mixed> */
    public function listPosts(array $query = []): array
    {
        return $this->send('get', '/posts', $query);
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function updatePost(string $postId, array $payload): array
    {
        return $this->send('patch', '/posts/'.rawurlencode($postId), $payload);
    }

    /** @return array<string, mixed> */
    public function validatePost(array $payload): array
    {
        return $this->send('post', '/posts/validate', $payload);
    }

    /** @return array<string, mixed> */
    public function listAccounts(?string $profileId = null): array
    {
        return $this->send('get', '/accounts', array_filter(['profileId' => $profileId]));
    }

    /** @return array<string, mixed> */
    public function getConnectUrl(string $platform, string $profileId, ?string $redirectUrl = null): array
    {
        return $this->send('get', '/connect/'.rawurlencode($platform), array_filter([
            'profileId' => $profileId,
            'redirect_url' => $redirectUrl,
        ]));
    }

    /** @return array<string, mixed> */
    public function getAnalytics(array $query = []): array
    {
        return $this->send('get', '/analytics', $query);
    }

    /** @return array<string, mixed> */
    public function getInboxAnalytics(array $query = []): array
    {
        return $this->send('get', '/inbox/analytics', $query);
    }

    /** @return array<string, mixed> */
    public function getCampaignAnalytics(string $campaignId, array $query = []): array
    {
        return $this->send('get', '/ads/campaigns/'.rawurlencode($campaignId).'/analytics', $query);
    }

    /** @return array<string, mixed> */
    public function getAdAnalytics(string $adId, array $query = []): array
    {
        return $this->send('get', '/ads/'.rawurlencode($adId).'/analytics', $query);
    }

    /** @return array<string, mixed> */
    public function listAds(array $query = []): array
    {
        return $this->send('get', '/ads', $query);
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function createCampaign(array $payload): array
    {
        return $this->send('post', '/ads/campaigns', $payload);
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function updateCampaign(string $campaignId, array $payload): array
    {
        return $this->send('patch', '/ads/campaigns/'.rawurlencode($campaignId), $payload);
    }

    /** @return array<string, mixed> */
    public function listConversations(array $query = []): array
    {
        return $this->send('get', '/inbox/conversations', $query);
    }

    /** @return array<string, mixed> */
    public function getConversation(string $conversationId, array $query = []): array
    {
        return $this->send('get', '/inbox/conversations/'.rawurlencode($conversationId), [], $query);
    }

    /** @return array<string, mixed> */
    public function listComments(array $query = []): array
    {
        return $this->send('get', '/inbox/comments', $query);
    }

    /** @return array<string, mixed> */
    public function listReviews(array $query = []): array
    {
        return $this->send('get', '/inbox/reviews', $query);
    }

    /** @return array<string, mixed> */
    public function replyToComment(string $commentId, string $accountId, string $message, ?string $profileId = null): array
    {
        return $this->send('post', '/inbox/comments/'.rawurlencode($commentId).'/reply', [
            'accountId' => $accountId,
            'message' => $message,
        ], array_filter(['profileId' => $profileId]));
    }

    /** @return array<string, mixed> */
    public function sendInboxMessage(string $conversationId, string $accountId, string $message, ?string $profileId = null): array
    {
        return $this->send(
            'post',
            '/inbox/conversations/'.rawurlencode($conversationId).'/messages',
            [
                'accountId' => $accountId,
                'message' => $message,
            ],
            array_filter(['profileId' => $profileId]),
        );
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    private function send(string $method, string $uri, array $payload = [], array $query = []): array
    {
        if ($query !== []) {
            $uri .= '?'.http_build_query($query);
        }

        $response = $method === 'get'
            ? $this->request()->get($uri, $payload)
            : $this->request()->{$method}($uri, $payload);

        return $this->parse($response, $uri);
    }

    /** @return array<string, mixed> */
    private function parse(Response $response, string $uri): array
    {
        if ($response->successful()) {
            $json = $response->json();

            return is_array($json) ? $json : [];
        }

        $message = (string) ($response->json('error') ?? $response->reason());
        throw new ZernioException("Zernio request failed for {$uri}: {$message}", $response->status());
    }
}
