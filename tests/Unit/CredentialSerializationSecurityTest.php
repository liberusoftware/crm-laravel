<?php

namespace Tests\Unit;

use App\Models\AdvertisingAccount;
use App\Models\ConnectedAccount;
use App\Models\OAuthConfiguration;
use App\Models\SsoConnection;
use Tests\TestCase;

class CredentialSerializationSecurityTest extends TestCase
{
    public function test_serialization_hides_credentials_without_disabling_server_side_access(): void
    {
        $cases = [
            [ConnectedAccount::class, ['token', 'secret', 'refresh_token']],
            [OAuthConfiguration::class, ['client_secret', 'access_token', 'refresh_token']],
            [SsoConnection::class, ['client_secret']],
            [AdvertisingAccount::class, ['access_token', 'refresh_token']],
        ];

        foreach ($cases as [$class, $fields]) {
            $model = new $class();
            $model->forceFill(['name' => 'Example connection']);
            foreach ($fields as $field) {
                $model->setAttribute($field, 'private-'.$field);
            }

            $serialized = $model->toArray();
            $this->assertSame('Example connection', $serialized['name']);
            foreach ($fields as $field) {
                $this->assertArrayNotHasKey($field, $serialized);
                $this->assertStringNotContainsString('private-'.$field, $model->toJson());
                $this->assertSame('private-'.$field, $model->getAttribute($field));
            }
        }
    }
}
