<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobComment;
use App\Models\JobStatusHistory;
use App\Models\User;
use Illuminate\Support\Str;

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
            'upcomingJobs' => $this->jobService->upcomingJobs($user, 7),
            'unfinishedCount' => $this->jobService->unfinishedJobs($user),
            'recentClients' => $this->clientService->recent($user),
            'totalClients' => $user->clients()->count(),
            'totalJobs' => $user->jobs()->count(),
            'completedJobs' => $user->jobs()->where('status', 'completed')->count(),
            'overdueJobs' => $user->jobs()
                ->whereIn('status', ['new', 'scheduled', 'in_progress'])
                ->where('scheduled_date', '<', today())
                ->with('client')
                ->get(),
            'recentActivity' => $this->getRecentActivity($user),
            'thisWeekRevenue' => $user->jobs()
                ->where('status', 'completed')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('actual_price'),
        ];
    }

    private function getRecentActivity(User $user): array
    {
        $statusChanges = JobStatusHistory::whereHas('job', fn ($q) => $q->where('user_id', $user->id))
            ->with(['job.client', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($h) => [
                'type' => 'status',
                'time' => $h->created_at,
                'text' => $h->job->title,
                'detail' => ($h->old_status ? Job::STATUSES[$h->old_status].' → ' : '').Job::STATUSES[$h->new_status],
                'client' => $h->job->client->name ?? '',
                'url' => route('jobs.show', $h->job_id),
            ]);

        $comments = JobComment::whereHas('job', fn ($q) => $q->where('user_id', $user->id))
            ->with(['job.client', 'user'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($c) => [
                'type' => 'comment',
                'time' => $c->created_at,
                'text' => $c->job->title,
                'detail' => Str::limit($c->body, 60),
                'client' => $c->job->client->name ?? '',
                'url' => route('jobs.show', $c->job_id),
            ]);

        return $statusChanges->merge($comments)
            ->sortByDesc('time')
            ->take(8)
            ->values()
            ->toArray();
    }
}
