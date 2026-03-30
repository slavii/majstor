<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SearchController::class, 'index'])->name('search');
Route::get('/listing/{olxId}', [SearchController::class, 'show'])->name('listing.show');
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
