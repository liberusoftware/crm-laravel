<?php

declare(strict_types=1);

namespace Tests\Feature\WhiteLabel;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WhiteLabel\Actions\UpdateWhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource;
use Liberu\CRM\WhiteLabel\Models\WhiteLabelSettings;
use Tests\TestCase;

final class WhiteLabelModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_white_label_resource_exposes_read_and_edit_surfaces(): void
    {
        self::assertSame(['index', 'edit'], array_keys(WhiteLabelSettingsResource::getPages()));
    }

    public function test_owner_can_update_settings_and_audit_is_redacted_and_versioned(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $settings = app(UpdateWhiteLabelSettings::class)->execute($team->id, $owner->id, ['brand_name' => 'Acme', 'custom_domain' => 'crm.acme.example', 'theme' => 'dark', 'provider' => 'platform', 'show_platform_attribution' => false, 'email_settings' => ['api_key' => 'secret']]);

        self::assertSame(1, $settings->version);
        self::assertFalse($settings->show_platform_attribution);
        self::assertSame('secret', $settings->fresh()->email_settings['api_key']);
        $audit = $settings->getConnection()->table('crm_white_label_audits')->where('team_id', $team->id)->first();
        self::assertSame('white_label_settings_updated', $audit->event);
        self::assertStringNotContainsString('secret', (string) $audit->changes);
    }

    public function test_non_manager_and_stale_writer_are_rejected(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $action = app(UpdateWhiteLabelSettings::class);

        $this->expectException(ValidationException::class);
        $action->execute($team->id, $stranger->id, ['theme' => 'dark', 'provider' => 'platform', 'show_platform_attribution' => true]);

        $settings = $action->execute($team->id, $owner->id, ['theme' => 'dark', 'provider' => 'platform', 'show_platform_attribution' => true]);
        $this->expectException(ValidationException::class);
        $action->execute($team->id, $owner->id, ['theme' => 'default', 'provider' => 'platform', 'show_platform_attribution' => true], $settings->version - 1);
    }

    public function test_settings_query_cannot_cross_team_boundary(): void
    {
        $first = WhiteLabelSettings::query()->create(['team_id' => 101, 'theme' => 'dark', 'provider' => 'platform']);
        WhiteLabelSettings::query()->create(['team_id' => 202, 'theme' => 'default', 'provider' => 'platform']);

        self::assertSame($first->id, WhiteLabelSettings::query()->where('team_id', 101)->firstOrFail()->id);
        self::assertNull(WhiteLabelSettings::query()->where('team_id', 303)->first());
    }

    public function test_settings_update_cannot_override_tenant_or_version_fields(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $settings = app(UpdateWhiteLabelSettings::class)->execute($team->id, $owner->id, [
            'theme' => 'dark',
            'provider' => 'platform',
            'show_platform_attribution' => true,
            'team_id' => 999,
            'version' => 99,
        ]);

        self::assertSame($team->id, $settings->team_id);
        self::assertSame(1, $settings->version);
    }
}
