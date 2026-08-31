<?php

namespace Liberu\Foundation\Organizations\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Foundation\Organizations\Models\Team;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'user_id' => config('auth.providers.users.model')::factory(),
            'personal_team' => true,
        ];
    }
}
