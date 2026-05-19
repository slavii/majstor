<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobStatusHistory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class JobService
{
    public function list(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $user->jobs()
            ->with('client')
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$s}%"));
            }))
            ->when($filters['client_id'] ?? null, fn ($q, $id) => $q->where('client_id', $id))
            ->latest('scheduled_date')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(User $user, array $data): Job
    {
        $job = $user->jobs()->create($data);

        JobStatusHistory::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'old_status' => null,
            'new_status' => $job->status,
        ]);

        return $job;
    }

    public function update(Job $job, array $data, User $user): Job
    {
        $oldStatus = $job->status;
        $job->update($data);

        if ($oldStatus !== $job->status) {
            JobStatusHistory::create([
                'job_id' => $job->id,
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $job->status,
            ]);
        }

        return $job->fresh();
    }

    public function delete(Job $job): void
    {
        $job->photos->each(fn ($photo) => Storage::delete($photo->path));
        $job->delete();
    }

    public function uploadPhotos(Job $job, array $files, string $category = 'general'): void
    {
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $path = $file->store("jobs/{$job->id}", 'public');
            $job->photos()->create(['path' => $path, 'category' => $category]);
        }
    }

    public function deletePhoto(Job $job, int $photoId): void
    {
        $photo = $job->photos()->findOrFail($photoId);
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
    }

    public function addComment(Job $job, User $user, string $body): void
    {
        $job->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }

    public function todayJobs(User $user): Collection
    {
        return $user->jobs()
            ->with('client')
            ->whereDate('scheduled_date', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('scheduled_date')
            ->get();
    }

    public function upcomingJobs(User $user, int $limit = 5): Collection
    {
        return $user->jobs()
            ->with('client')
            ->where('scheduled_date', '>', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('scheduled_date')
            ->limit($limit)
            ->get();
    }

    public function unfinishedJobs(User $user): int
    {
        return $user->jobs()
            ->whereIn('status', ['new', 'scheduled', 'in_progress'])
            ->count();
    }
}
