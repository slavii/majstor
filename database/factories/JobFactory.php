<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    public function definition(): array
    {
        $titles = [
            'Ремонт на покрив', 'Топлоизолация', 'Смяна на бойлер', 'Подмяна на тръби',
            'Монтаж на климатик', 'Боядисване на стая', 'Електрическа инсталация',
            'Ремонт на баня', 'Подмяна на дограма', 'Поправка на ВиК', 'Шпакловка и боядисване',
            'Монтаж на радиатори', 'Изграждане на гипсокартон', 'Ремонт на врата',
        ];

        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'title' => fake()->randomElement($titles),
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(['new', 'scheduled', 'in_progress', 'completed', 'cancelled']),
            'scheduled_date' => fake()->optional(0.8)->dateTimeBetween('-1 week', '+2 weeks'),
            'estimated_price' => fake()->optional(0.6)->randomFloat(2, 50, 5000),
            'actual_price' => fake()->optional(0.3)->randomFloat(2, 50, 5000),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function forToday(): static
    {
        return $this->state(fn () => [
            'scheduled_date' => now()->toDateString(),
            'status' => fake()->randomElement(['new', 'scheduled', 'in_progress']),
        ]);
    }
}
