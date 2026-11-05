<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\OAuthConfiguration;
use App\Models\SocialMediaPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class DirectPostPublisher
{
    public const PLATFORMS = ['facebook', 'instagram', 'linkedin', 'twitter'];

    public function connection(int $teamId, string $platform): OAuthConfiguration
    {
        $connections = OAuthConfiguration::query()->where('team_id', $teamId)->where('service_name', $platform)->where('is_active', true)->limit(2)->get();
        if ($connections->count() > 1) {
            throw new RuntimeException('Keep one active standalone connection per channel. Disable the unused '.$platform.' connection in Marketing connections.');
        }
        $connection = $connections->first();
        if (! $connection || ! $connection->getAttribute('access_token') || ($connection->getAttribute('token_expires_at')?->isPast())) {
            throw new RuntimeException('Reconnect the workspace '.$platform.' account before publishing.');
        }
        if ($platform !== 'twitter' && ! data_get($connection->getAttribute('additional_settings'), 'publishing_id')) {
            throw new RuntimeException('Choose a publishing destination for '.$platform.'.');
        }

        return $connection;
    }

    public function configureDestination(OAuthConfiguration $connection, string $target): void
    {
        $settings = $connection->getAttribute('additional_settings') ?? [];
        if (in_array($connection->service_name, ['facebook', 'instagram'], true)) {
            $version = (string) app(PlatformConfiguration::class)->credential('meta_api_version', config('services.facebook.graph_version'));
            if (! preg_match('/^v[0-9]+\.[0-9]+$/', $version) || ! preg_match('/^[0-9]+$/', $target)) {
                throw new RuntimeException('A supported Meta version and numeric publishing destination are required.');
            }
            $request = Http::withToken((string) $connection->getAttribute('access_token'))->acceptJson()->timeout(20);
            $me = $request->get('https://graph.facebook.com/'.$version.'/me', ['fields' => 'id']);
            if ($connection->service_name === 'facebook' && $me->successful() && (string) $me->json('id') === $target) {
                $connection->update(['additional_settings' => array_replace($settings, ['publishing_id' => $target])]);

                return;
            }
            $after = null;
            $selected = null;
            for ($page = 0; $page < 10 && $selected === null; $page++) {
                $response = $request->get('https://graph.facebook.com/'.$version.'/me/accounts', array_filter(['fields' => 'id,name,access_token,instagram_business_account', 'limit' => 100, 'after' => $after]));
                if (! $response->successful()) {
                    throw new RuntimeException('Reconnect the Meta account to choose a Page or Instagram professional account.');
                }
                foreach ((array) $response->json('data') as $candidate) {
                    if (! is_array($candidate)) {
                        continue;
                    }
                    $id = $connection->service_name === 'instagram' ? data_get($candidate, 'instagram_business_account.id') : ($candidate['id'] ?? null);
                    if ((string) $id === $target && filled($candidate['access_token'] ?? null)) {
                        $selected = $candidate;
                        break;
                    }
                }
                $after = $response->json('paging.cursors.after');
                if (! $response->json('paging.next') || ! is_string($after)) {
                    break;
                }
            }
            if ($selected === null) {
                throw new RuntimeException('This destination is not managed by the connected Meta account.');
            }
            $connection->update(['access_token' => $selected['access_token'], 'additional_settings' => array_replace($settings, ['publishing_id' => $target, 'page_id' => $selected['id']])]);

            return;
        }
        if ($connection->service_name === 'linkedin' && ! preg_match('/^urn:li:(person|organization):[a-zA-Z0-9_-]+$/', $target)) {
            throw new RuntimeException('Use a LinkedIn person or organization author URN.');
        }
        $connection->update(['additional_settings' => array_replace($settings, ['publishing_id' => $target])]);
    }

    public function publish(SocialMediaPost $post, string $platform): string
    {
        abort_unless((bool) config('services.zernio.direct_api_enabled', true), 503, 'Standalone API access is disabled by the platform operator.');
        if (! in_array($platform, self::PLATFORMS, true)) {
            throw new RuntimeException('Direct publishing is not available for '.$platform.'. Choose a supported channel or ask your administrator to enable Zernio.');
        }
        $connection = $this->connection((int) $post->getAttribute('team_id'), $platform);
        $request = Http::withToken($connection->getAttribute('access_token'))->acceptJson()->timeout(30);
        $target = (string) data_get($connection->getAttribute('additional_settings'), 'publishing_id');
        $image = filled($post->getAttribute('image_path')) ? Storage::disk('public')->url($post->getAttribute('image_path')) : null;
        $content = (string) $post->getAttribute('content');
        if (filled($post->getAttribute('video_url'))) {
            throw new RuntimeException('Direct video publishing is unavailable in this composer. Use Zernio for video posts.');
        }
        if ($platform === 'twitter') {
            if ($image) {
                throw new RuntimeException('X media uploads require a media upload integration. Use a text post or Zernio.');
            }
            $response = $request->post('https://api.x.com/2/tweets', ['text' => $content.(filled($post->link) ? "\n".$post->link : '')]);
        } elseif ($platform === 'linkedin') {
            if ($image) {
                throw new RuntimeException('LinkedIn images must be uploaded and registered first. Use a text post or Zernio.');
            }
            $version = (string) app(PlatformConfiguration::class)->credential('linkedin_api_version', config('services.linkedin.api_version', ''));
            if (! preg_match('/^[0-9]{6}$/', $version)) {
                throw new RuntimeException('Your administrator must configure a supported LinkedIn API version.');
            }
            $response = $request->withHeaders(['LinkedIn-Version' => $version, 'X-Restli-Protocol-Version' => '2.0.0'])->post('https://api.linkedin.com/rest/posts', [
                'author' => $target, 'commentary' => $content.(filled($post->link) ? "\n".$post->link : ''), 'visibility' => 'PUBLIC',
                'distribution' => ['feedDistribution' => 'MAIN_FEED', 'targetEntities' => [], 'thirdPartyDistributionChannels' => []],
                'lifecycleState' => 'PUBLISHED', 'isReshareDisabledByAuthor' => false,
            ]);
        } else {
            $version = (string) app(PlatformConfiguration::class)->credential('meta_api_version', config('services.facebook.graph_version'));
            if (! preg_match('/^v[0-9]+\.[0-9]+$/', $version)) {
                throw new RuntimeException('Your administrator must configure a supported Meta Graph API version.');
            }
            $base = 'https://graph.facebook.com/'.$version.'/'.rawurlencode($target);
            if ($platform === 'instagram') {
                if (! $image) {
                    throw new RuntimeException('Instagram requires an image.');
                }
                $container = $request->post($base.'/media', ['image_url' => $image, 'caption' => $content]);
                if (! $container->successful() || ! is_string($container->json('id'))) {
                    throw new RuntimeException('Instagram did not accept the image container.');
                }
                $response = $request->post($base.'/media_publish', ['creation_id' => $container->json('id')]);
            } else {
                $response = $request->post($base.($image ? '/photos' : '/feed'), $image
                    ? ['url' => $image, 'caption' => $content.(filled($post->link) ? "\n".$post->link : '')]
                    : array_filter(['message' => $content, 'link' => $post->link]));
            }
        }
        if (! $response->successful()) {
            throw new RuntimeException('The '.$platform.' API refused publication (HTTP '.$response->status().'). Reconnect or check account permissions.');
        }
        $id = $response->json('id') ?? $response->json('data.id') ?? $response->header('x-restli-id');
        if (! is_string($id) || $id === '') {
            throw new RuntimeException('The '.$platform.' API did not return a publication identifier.');
        }

        return $id;
    }
}
