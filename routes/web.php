
<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Suppliers\Index as SupplierIndex;
use App\Livewire\Customers\Index as CustomerIndex;
use App\Livewire\Pos\Index as PosIndex;


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

Route::get('/pos', PosIndex::class)
    ->middleware(['auth'])
    ->name('pos');

require __DIR__.'/auth.php';