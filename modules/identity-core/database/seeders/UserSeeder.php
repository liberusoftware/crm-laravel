<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Role as SpatieRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function setPermissionsTeamId;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = (string) (config('app.seed_admin_password') ?: Str::random(64));
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ],
        );

        $team = Team::firstOrFail();
        $adminUser->teams()->syncWithoutDetaching([$team->id]);
        $adminUser->forceFill(['current_team_id' => $team->id])->save();

        $role = SpatieRole::where('name', Role::SuperAdmin->value)->firstOrFail();
        setPermissionsTeamId(null);
        $adminUser->assignRole($role);

        if (app()->environment('local') && $adminUser->wasRecentlyCreated && blank(config('app.seed_admin_password'))) {
            $this->command?->info("Admin password: {$adminPassword}");
        }
    }
}
