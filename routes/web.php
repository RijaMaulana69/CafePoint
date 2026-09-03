
<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Products\Index as ProductIndex;

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

Route::get('/produk', ProductIndex::class)
    ->middleware(['auth'])
    ->name('produk');

require __DIR__.'/auth.php';