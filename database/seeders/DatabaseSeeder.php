<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Job;
use App\Models\JobStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Демо Потребител',
            'email' => 'demo@majstor.bg',
            'password' => bcrypt('password'),
        ]);

        $clients = Client::factory(12)->create(['user_id' => $user->id]);

        foreach ($clients as $client) {
            $jobs = Job::factory(rand(1, 4))->create([
                'user_id' => $user->id,
                'client_id' => $client->id,
            ]);

            foreach ($jobs as $job) {
                JobStatusHistory::create([
                    'job_id' => $job->id,
                    'user_id' => $user->id,
                    'old_status' => null,
                    'new_status' => $job->status,
                ]);
            }
        }

        // A couple of jobs scheduled for today
        Job::factory(3)->forToday()->create([
            'user_id' => $user->id,
            'client_id' => $clients->random()->id,
        ]);
    }
}
