<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Chat\ChatPage;

// Public route
Route::view('/', 'welcome');

// Auth routes
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Make ChatPage the dashboard view
    Route::get('/dashboard', ChatPage::class)->name('dashboard');

    // Profile page
    Route::view('/profile', 'profile')->name('profile.edit');
});

// Dev-only logout
Route::get('/logout-dev', function () {
    Auth::logout();
    return redirect('/');
});
