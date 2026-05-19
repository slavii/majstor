<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        $bgNames = ['Иван Петров', 'Георги Димитров', 'Стоян Стоянов', 'Мария Иванова', 'Петър Николов', 'Димитър Георгиев', 'Николай Тодоров', 'Христо Христов', 'Борис Борисов', 'Ангел Ангелов'];
        $bgCities = ['София', 'Пловдив', 'Варна', 'Бургас', 'Русе', 'Стара Загора', 'Плевен', 'Добрич', 'Сливен', 'Шумен', 'Кичево', 'Банско'];

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement($bgNames),
            'phone' => '08'.fake()->numerify('## ### ###'),
            'email' => fake()->unique()->safeEmail(),
            'address' => 'гр. '.fake()->randomElement($bgCities).', ул. '.fake()->streetName().' '.fake()->buildingNumber(),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
