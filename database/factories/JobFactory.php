<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    private static array $jobTemplates = [
        ['title' => 'Ремонт на покрив — теч', 'desc' => 'Тече от ъгъла на покрива при силен дъжд. Трябва оглед и запечатване.', 'price' => [300, 1200]],
        ['title' => 'Топлоизолация на фасада', 'desc' => 'Фасада ~80 кв.м., 5 см стиропор + мазилка. Скеле необходимо.', 'price' => [3000, 8000]],
        ['title' => 'Смяна на бойлер 80л', 'desc' => 'Стар бойлер тече, клиентът вече е купил нов Tesy 80л.', 'price' => [80, 150]],
        ['title' => 'Подмяна на смесител в баня', 'desc' => 'Капе от смесителя. Клиентът иска нов — Grohe или подобен.', 'price' => [50, 120]],
        ['title' => 'Монтаж на климатик', 'desc' => 'Нов инверторен климатик, стая ~25 кв.м. Монтаж на 3-ти етаж.', 'price' => [200, 400]],
        ['title' => 'Боядисване на хол и коридор', 'desc' => 'Около 60 кв.м. стени. Латекс, цвят по избор. Шпакловка където трябва.', 'price' => [400, 900]],
        ['title' => 'Електрическо табло — подмяна', 'desc' => 'Старо табло с керамични бушони. Нужна е пълна подмяна с автомати.', 'price' => [200, 500]],
        ['title' => 'Ремонт на баня — цялостен', 'desc' => 'Демонтаж на стари плочки, нова хидроизолация, нови плочки и санитария.', 'price' => [2000, 5000]],
        ['title' => 'Подмяна на дограма — 3 прозореца', 'desc' => 'Стара дървена дограма. PVC двоен стъклопакет. Размери за уточняване.', 'price' => [1500, 3000]],
        ['title' => 'Отпушване на канал', 'desc' => 'Запушен канал в кухнята, водата не се оттича.', 'price' => [50, 150]],
        ['title' => 'Шпакловка на тавани', 'desc' => '2 стаи, тавани с пукнатини. Нужна е шпакловка и грунд.', 'price' => [200, 500]],
        ['title' => 'Монтаж на радиатори', 'desc' => '3 алуминиеви радиатора. Тръбна разводка вече е готова.', 'price' => [150, 350]],
        ['title' => 'Гипсокартон — преградна стена', 'desc' => 'Преграждане на голяма стая. ~6 м дължина, 2.6 м височина.', 'price' => [400, 800]],
        ['title' => 'Ремонт на входна врата', 'desc' => 'Ключалката заяжда, пантите хлабави. Може да се наложи смяна.', 'price' => [50, 200]],
        ['title' => 'Подова настилка — ламинат', 'desc' => 'Полагане на ламинат в хол, ~30 кв.м. Материалът е на клиента.', 'price' => [300, 600]],
        ['title' => 'Оглед и оценка на имот', 'desc' => 'Клиентът иска оглед преди покупка. Да се провери покрив, ВиК, ел.', 'price' => [50, 100]],
    ];

    public function definition(): array
    {
        $template = fake()->randomElement(self::$jobTemplates);

        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'title' => $template['title'],
            'description' => $template['desc'],
            'status' => fake()->randomElement(['new', 'scheduled', 'in_progress', 'completed', 'cancelled']),
            'scheduled_date' => fake()->optional(0.85)->dateTimeBetween('-2 weeks', '+3 weeks'),
            'estimated_price' => fake()->optional(0.7)->numberBetween($template['price'][0], $template['price'][1]),
            'actual_price' => fake()->optional(0.2)->numberBetween($template['price'][0], $template['price'][1]),
            'notes' => fake()->optional(0.2)->randomElement([
                'Клиентът е много зает, търси го само след 18:00.',
                'Нужен е помощник за скелето.',
                'Материалите са за сметка на клиента.',
                'Платено авансово 50%.',
                'Трябва ми ъглошлайф и перфоратор.',
            ]),
        ];
    }

    public function forToday(): static
    {
        return $this->state(fn () => [
            'scheduled_date' => now()->toDateString(),
            'status' => fake()->randomElement(['scheduled', 'in_progress']),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'scheduled_date' => fake()->dateTimeBetween('-5 days', '-1 day'),
            'status' => fake()->randomElement(['new', 'scheduled']),
        ]);
    }
}
