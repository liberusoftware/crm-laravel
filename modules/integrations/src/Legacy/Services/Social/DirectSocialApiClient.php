<?php

declare(strict_types=1);

namespace App\Services\Social;

use App\Models\OAuthConfiguration;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class DirectSocialApiClient
{
    /** @return array<string, mixed> */
    public function request(OAuthConfiguration $configuration, string $method, string $endpoint, array $payload = []): array
    {
        abort_unless((bool) config('services.zernio.direct_api_enabled', true), 503, 'Direct social API access is disabled.');
        $token = (string) ($configuration->access_token ?? '');
        $baseUrl = (string) config('services.social.direct_base_urls.'.strtolower((string) $configuration->service_name), '');
        abort_unless($token !== '' && filter_var($baseUrl, FILTER_VALIDATE_URL), 422, 'A direct API token and base URL are required.');

        /** @var Response $response */
        $response = Http::baseUrl(rtrim($baseUrl, '/'))->withToken($token)->acceptJson()->timeout(30)->retry(2, 200, throw: false)->{$method}('/'.ltrim($endpoint, '/'), $payload);
        if (! $response->successful()) {
            throw new RuntimeException('Direct social API request failed with status '.$response->status().'.');
        }
        $data = $response->json();

        return is_array($data) ? $data : [];
    }
}
