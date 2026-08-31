<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ],
        );

        Team::firstOrCreate([
            'name' => 'Default',
            'personal_team' => false,
            'user_id' => $owner->getKey(),
        ]);
    }
}
