
<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Suppliers\Index as SupplierIndex;
use App\Livewire\Customers\Index as CustomerIndex;

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

Route::get('/supplier', SupplierIndex::class)
    ->middleware(['auth'])
    ->name('supplier');

Route::get('/customer', CustomerIndex::class)
    ->middleware(['auth'])
    ->name('customer');

require __DIR__.'/auth.php';