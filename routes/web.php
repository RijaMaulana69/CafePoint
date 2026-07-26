<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Categories\Index as CategoryIndex;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/kategori', CategoryIndex::class)
    ->middleware(['auth'])
    ->name('kategori');

require __DIR__.'/auth.php';