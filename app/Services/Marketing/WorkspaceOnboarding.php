<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\Team;
use App\Models\User;
use App\Services\Zernio\ZernioTenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class WorkspaceOnboarding
{
    public function __construct(private readonly WorkspaceAccess $access) {}

    public function save(User $user, Team $team, array $input): void
    {
        abort_unless($this->access->canManage($user, $team), 403);
        $data = Validator::make($input, [
            'account_name' => ['required', 'string', 'max:255'],
            'account_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'team_name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'timezone'],
            'brand_name' => ['required', 'string', 'max:180'],
            'brand_website' => ['nullable', 'url:https', 'max:2048'],
            'audience' => ['nullable', 'string', 'max:2000'],
            'tone' => ['required', 'in:professional,friendly,informative,bold'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', Rule::in(ZernioTenantService::PLATFORMS)],
            'approval_required' => ['required', 'boolean'],
        ])->validate();
        DB::transaction(function () use ($user, $team, $data): void {
            $emailChanged = $user->getAttribute('email') !== $data['account_email'];
            $user->forceFill(['name' => $data['account_name'], 'email' => $data['account_email'], ...($emailChanged ? ['email_verified_at' => null] : [])])->save();
            $preferences = array_diff_key($data, array_flip(['account_name', 'account_email', 'team_name']));
            $team->forceFill(['name' => $data['team_name'], 'marketing_preferences' => $preferences, 'setup_completed_at' => now()])->save();
        });
    }
}
