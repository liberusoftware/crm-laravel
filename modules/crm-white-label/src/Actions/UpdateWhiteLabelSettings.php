<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WhiteLabel\Events\WhiteLabelSettingsUpdated;
use Liberu\CRM\WhiteLabel\Models\WhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Services\WhiteLabelAudit;
use Liberu\CRM\WhiteLabel\Services\WhiteLabelPolicy;

final class UpdateWhiteLabelSettings
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, int $actorId, array $attributes, ?int $expectedVersion = null): WhiteLabelSettings
    {
        if (! app(WhiteLabelPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'You cannot manage white-label settings for this team.']);
        }

        $data = validator($attributes, [
            'brand_name' => ['nullable', 'string', 'max:255'],
            'custom_domain' => ['nullable', 'string', 'max:255', 'regex:/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i'],
            'theme' => ['required', 'string', 'max:100'],
            'email_settings' => ['nullable', 'array'],
            'application_settings' => ['nullable', 'array'],
            'client_experience' => ['nullable', 'array'],
            'provider' => ['required', 'string', 'max:100'],
            'show_platform_attribution' => ['required', 'boolean'],
        ])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data, $expectedVersion): WhiteLabelSettings {
            $settings = WhiteLabelSettings::query()->lockForUpdate()->firstOrNew(['team_id' => $teamId]);
            if ($expectedVersion !== null && $settings->exists && $settings->version !== $expectedVersion) {
                throw ValidationException::withMessages(['version' => 'The white-label settings changed since they were loaded.']);
            }

            $before = $settings->getOriginal();
            $settings->fill($data);
            $settings->version = $settings->exists ? $settings->version + 1 : 1;
            $settings->save();
            DB::afterCommit(fn (): bool => event(new WhiteLabelSettingsUpdated($settings->fresh(), $actorId)) === null);
            app(WhiteLabelAudit::class)->record($teamId, $actorId, 'white_label_settings_updated', ['fields' => array_keys(array_diff_assoc($settings->getAttributes(), $before))]);

            return $settings->fresh();
        });
    }
}
