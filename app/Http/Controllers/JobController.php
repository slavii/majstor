<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobPhotoRequest;
use App\Http\Requests\JobRequest;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(private JobService $service) {}

    public function index(Request $request)
    {
        $jobs = $this->service->list($request->user(), $request->only(['status', 'search', 'client_id']));
        $statuses = Job::STATUSES;

        return view('jobs.index', compact('jobs', 'statuses'));
    }

    public function create(Request $request)
    {
        $clients = $request->user()->clients()->orderBy('name')->get();

        return view('jobs.create', compact('clients'));
    }

    public function store(JobRequest $request)
    {
        $job = $this->service->create($request->user(), $request->validated());

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Задачата е създадена.');
    }

    public function show(Job $job)
    {
        $this->authorize('view', $job);

        $job->load(['client', 'photos', 'comments.user', 'statusHistory.user']);

        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job, Request $request)
    {
        $this->authorize('update', $job);

        $clients = $request->user()->clients()->orderBy('name')->get();

        return view('jobs.edit', compact('job', 'clients'));
    }

    public function update(JobRequest $request, Job $job)
    {
        $this->authorize('update', $job);

        $this->service->update($job, $request->validated(), $request->user());

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Задачата е обновена.');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $this->service->delete($job);

        return redirect()->route('jobs.index')
            ->with('success', 'Задачата е изтрита.');
    }

    public function uploadPhotos(JobPhotoRequest $request, Job $job)
    {
        $this->authorize('update', $job);

        $this->service->uploadPhotos($job, $request->file('photos'));

        return back()->with('success', 'Снимките са качени.');
    }

    public function deletePhoto(Job $job, int $photoId)
    {
        $this->authorize('update', $job);

        $this->service->deletePhoto($job, $photoId);

        return back()->with('success', 'Снимката е изтрита.');
    }

    public function addComment(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $request->validate(['body' => 'required|string|max:2000']);

        $this->service->addComment($job, $request->user(), $request->input('body'));

        return back()->with('success', 'Коментарът е добавен.');
    }
}
