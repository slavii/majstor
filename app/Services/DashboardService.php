<?php

namespace App\Services;

use App\Models\User;

class DashboardService
{
    public function __construct(
        private JobService $jobService,
        private ClientService $clientService,
    ) {}

    public function getData(User $user): array
    {
        return [
            'todayJobs' => $this->jobService->todayJobs($user),
            'upcomingJobs' => $this->jobService->upcomingJobs($user),
            'unfinishedCount' => $this->jobService->unfinishedJobs($user),
            'recentClients' => $this->clientService->recent($user),
            'totalClients' => $user->clients()->count(),
            'totalJobs' => $user->jobs()->count(),
            'completedJobs' => $user->jobs()->where('status', 'completed')->count(),
        ];
    }
}
