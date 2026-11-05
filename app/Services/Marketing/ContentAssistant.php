<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\Team;
use App\Models\User;
use App\Services\Zernio\ZernioTenantService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

final class ContentAssistant
{
    public function __construct(private readonly PlatformConfiguration $configuration, private readonly WorkspaceAccess $access) {}

    public function generate(User $user, Team $team, array $input): array
    {
        $this->access->authorize($user, $team, 'create_social_media_post');
        $data = Validator::make($input, [
            'purpose' => ['required', 'in:post,advertisement'], 'brief' => ['required', 'string', 'max:5000'],
            'platform' => ['required', 'string', 'in:'.implode(',', ZernioTenantService::PLATFORMS)],
        ])->validate();
        abort_unless((bool) data_get($this->configuration->settings()?->getAttribute('options'), 'ai_enabled'), 422, 'Your administrator has not enabled content assistance.');
        try {
            $response = Http::withToken((string) $this->configuration->credential('ai_api_key'))->acceptJson()->timeout(45)
                ->post($this->configuration->credential('ai_endpoint'), $data + ['brand' => $team->getAttribute('marketing_preferences') ?? [], 'require_human_review' => true]);
            if (! $response->successful()) {
                throw new RuntimeException('The content service is unavailable. Your draft has not changed.');
            }

            return Validator::make((array) $response->json(), [
                'content' => ['required', 'string', 'max:65535'], 'headline' => ['nullable', 'string', 'max:255'],
                'call_to_action' => ['nullable', 'string', 'max:255'],
            ])->validate();
        } catch (ConnectionException $exception) {
            report($exception);
            throw new RuntimeException('The content service timed out. Your draft has not changed.', previous: $exception);
        }
    }
}
