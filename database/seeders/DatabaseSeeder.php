<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientCommunication;
use App\Models\Job;
use App\Models\JobComment;
use App\Models\JobStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Красимир Илиев',
            'email' => 'demo@majstor.bg',
            'password' => bcrypt('password'),
        ]);

        $clients = Client::factory(15)->create(['user_id' => $user->id]);

        $comments = [
            'Говорих с клиента, утре ще мина.',
            'Материалите са поръчани.',
            'Трябва да се вземе допълнително 2 чувала лепило.',
            'Клиентът потвърди цената.',
            'Готово е, чакам плащане.',
            'Ще се наложи да дойда и утре — не стигна времето.',
            'Снимках преди и след ремонта.',
        ];

        foreach ($clients as $client) {
            $jobCount = fake()->numberBetween(1, 3);
            $jobs = Job::factory($jobCount)->create([
                'user_id' => $user->id,
                'client_id' => $client->id,
            ]);

            foreach ($jobs as $job) {
                JobStatusHistory::create([
                    'job_id' => $job->id,
                    'user_id' => $user->id,
                    'old_status' => null,
                    'new_status' => 'new',
                    'created_at' => $job->created_at,
                ]);

                if (in_array($job->status, ['scheduled', 'in_progress', 'completed'])) {
                    JobStatusHistory::create([
                        'job_id' => $job->id,
                        'user_id' => $user->id,
                        'old_status' => 'new',
                        'new_status' => 'scheduled',
                        'created_at' => $job->created_at->addHours(rand(1, 24)),
                    ]);
                }

                if (in_array($job->status, ['in_progress', 'completed'])) {
                    JobStatusHistory::create([
                        'job_id' => $job->id,
                        'user_id' => $user->id,
                        'old_status' => 'scheduled',
                        'new_status' => 'in_progress',
                        'created_at' => $job->created_at->addDays(rand(1, 3)),
                    ]);

                    JobComment::create([
                        'job_id' => $job->id,
                        'user_id' => $user->id,
                        'body' => fake()->randomElement($comments),
                        'created_at' => $job->created_at->addDays(rand(1, 3)),
                    ]);
                }

                if ($job->status === 'completed') {
                    JobStatusHistory::create([
                        'job_id' => $job->id,
                        'user_id' => $user->id,
                        'old_status' => 'in_progress',
                        'new_status' => 'completed',
                        'created_at' => $job->created_at->addDays(rand(3, 7)),
                    ]);

                    if (fake()->boolean(60)) {
                        JobComment::create([
                            'job_id' => $job->id,
                            'user_id' => $user->id,
                            'body' => fake()->randomElement(['Готово, клиентът е доволен.', 'Завършено и платено.', 'Всичко е ок, чисто и подредено.']),
                            'created_at' => $job->created_at->addDays(rand(3, 7)),
                        ]);
                    }
                }
            }
        }

        // Today's jobs with checklists
        $todayJobs = Job::factory(3)->forToday()->create([
            'user_id' => $user->id,
            'client_id' => fn () => $clients->random()->id,
        ]);

        $sampleChecklists = [
            [['text' => 'Оглед на обекта', 'done' => true], ['text' => 'Измерване', 'done' => true], ['text' => 'Закупуване на материали', 'done' => false], ['text' => 'Изпълнение на ремонта', 'done' => false], ['text' => 'Почистване', 'done' => false]],
            [['text' => 'Спиране на водата', 'done' => true], ['text' => 'Демонтаж на старото', 'done' => false], ['text' => 'Монтаж на новото', 'done' => false], ['text' => 'Тест за течове', 'done' => false]],
            [['text' => 'Проверка на ел. инсталация', 'done' => true], ['text' => 'Монтаж на табло', 'done' => true], ['text' => 'Свързване на автомати', 'done' => false], ['text' => 'Тестване', 'done' => false]],
        ];

        foreach ($todayJobs as $i => $job) {
            $job->update([
                'checklist' => $sampleChecklists[$i] ?? $sampleChecklists[0],
                'internal_notes' => fake()->randomElement([
                    'Ключът е при съседката на 3-ти етаж.',
                    'Паркирането е отзад.',
                    null,
                ]),
            ]);
        }

        // Overdue jobs
        Job::factory(2)->overdue()->create([
            'user_id' => $user->id,
            'client_id' => fn () => $clients->random()->id,
        ]);

        // Communication log samples
        $commSamples = [
            ['type' => 'call', 'direction' => 'outbound', 'summary' => 'Обадих се да потвърдя часа за утре. Клиентът е съгласен.'],
            ['type' => 'call', 'direction' => 'inbound', 'summary' => 'Клиентът се обади — пита кога ще дойда.'],
            ['type' => 'viber', 'direction' => 'outbound', 'summary' => 'Изпратих снимка на предложения материал.'],
            ['type' => 'viber', 'direction' => 'inbound', 'summary' => 'Прати снимка на проблема.'],
            ['type' => 'in_person', 'direction' => 'outbound', 'summary' => 'Оглед на място. Договорихме цена и дата.'],
            ['type' => 'sms', 'direction' => 'outbound', 'summary' => 'Изпратих SMS за потвърждение на адреса.'],
        ];

        foreach ($clients->take(8) as $client) {
            $commCount = fake()->numberBetween(1, 3);
            $clientJobs = $client->jobs;
            for ($i = 0; $i < $commCount; $i++) {
                $sample = fake()->randomElement($commSamples);
                ClientCommunication::create([
                    'client_id' => $client->id,
                    'user_id' => $user->id,
                    'job_id' => $clientJobs->count() ? $clientJobs->random()->id : null,
                    'type' => $sample['type'],
                    'direction' => $sample['direction'],
                    'summary' => $sample['summary'],
                    'created_at' => fake()->dateTimeBetween('-7 days', 'now'),
                ]);
            }
        }
    }
}
