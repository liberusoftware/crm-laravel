<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition(): array
    {
        return ['name' => fake()->words(2, true), 'url' => 'https://hooks.example.com/endpoint', 'events' => ['contact.created'], 'secret' => fake()->sha256(), 'team_id' => Team::factory(), 'is_active' => true];
    }
}
