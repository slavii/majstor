<?php

use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);

    // Jobs
    Route::resource('jobs', JobController::class);
    Route::post('/jobs/{job}/photos', [JobController::class, 'uploadPhotos'])->name('jobs.photos.upload');
    Route::delete('/jobs/{job}/photos/{photo}', [JobController::class, 'deletePhoto'])->name('jobs.photos.delete');
    Route::post('/jobs/{job}/comments', [JobController::class, 'addComment'])->name('jobs.comments.store');

    // AI Assistant
    Route::get('/ai', [AIAssistantController::class, 'index'])->name('ai.index');
    Route::post('/ai', [AIAssistantController::class, 'query'])->name('ai.query');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
