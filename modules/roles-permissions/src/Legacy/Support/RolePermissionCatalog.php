<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

final class RolePermissionCatalog
{
    private const PRESETS = [
        'sales' => ['label' => 'Sales', 'resources' => ['contact', 'company', 'lead', 'deal', 'opportunity', 'task', 'note']],
        'support' => ['label' => 'Support', 'resources' => ['contact', 'company', 'message', 'task', 'note', 'knowledge_base_article']],
        'social_media' => ['label' => 'Social media', 'resources' => ['social_media_post', 'marketing_campaign', 'task']],
        'advertising' => ['label' => 'Advertising', 'resources' => ['advertising_account', 'campaign', 'ad_set', 'ad', 'marketing_campaign', 'dashboard_widget']],
    ];

    public static function options(bool $platform = false): array
    {
        $query = Permission::query()->where('guard_name', 'web');
        if (! $platform) {
            $resources = [];
            foreach (['core', 'advertising', 'staff'] as $group) {
                $resources = [...$resources, ...config('permissions.groups.'.$group, [])];
            }
            $names = [];
            foreach (array_diff([...$resources, 'territory'], ['call_setting', 'whatsapp_number']) as $resource) {
                foreach (config('permissions.actions', []) as $action) {
                    $names[] = $action.'_'.$resource;
                }
            }
            $query->whereIn('name', $names);
        }

        return $query->orderBy('name')->pluck('name')->mapWithKeys(fn (string $name): array => [$name => self::label($name)])->sort()->all();
    }

    public static function presets(): array
    {
        return array_map(fn (array $preset): string => $preset['label'], self::PRESETS);
    }

    public static function preset(string $key): array
    {
        $names = [];
        foreach (self::PRESETS[$key]['resources'] ?? [] as $resource) {
            foreach (['view', 'create', 'update'] as $action) {
                $names[] = $action.'_'.$resource;
            }
        }

        return array_values(array_intersect($names, array_keys(self::options())));
    }

    private static function label(string $name): string
    {
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            if (str_starts_with($name, $action.'_')) {
                $resource = substr($name, strlen($action) + 1);

                return Str::headline($resource).' · '.match ($action) {
                    'view' => 'View', 'create' => 'Create', 'update' => 'Edit', 'delete' => 'Delete',
                };
            }
        }

        return Str::headline($name);
    }
}
