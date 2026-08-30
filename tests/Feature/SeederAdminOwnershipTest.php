<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('makes admin@example.com the owner of the Default team', function () {
    app(TeamSeeder::class)->run();
    app(RolesSeeder::class)->run();
    app(UserSeeder::class)->run();

    $admin = User::where('email', 'admin@example.com')->firstOrFail();
    $team = Team::where('name', 'Default')->firstOrFail();

    expect($team->user_id)->toBe($admin->id);
    expect($admin->current_team_id)->toBe($team->id);
});

it('leaves no throwaway owner@example.com placeholder user', function () {
    app(TeamSeeder::class)->run();
    app(RolesSeeder::class)->run();
    app(UserSeeder::class)->run();

    expect(User::where('email', 'owner@example.com')->exists())->toBeFalse();
});
