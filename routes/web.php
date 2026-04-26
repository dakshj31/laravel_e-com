<?php

use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\OrderDetails;
use App\Livewire\Customer\Profile;
use App\Livewire\HomePage;
use App\Livewire\Orders;
use App\Livewire\ProductListing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');

Route::get('products', ProductListing::class)->name('products.index');

// Route::get('/products', function () {
//     dd('ROUTE IS WORKING');
// });

Route::get('/test', fn() => 'WORKING');

Route::middleware('auth:customer')->group(function() {

    Route::get('/my-account', Dashboard::class)->name('customer.dashboard');
    Route::get('/my-account/orders', Orders::class)->name('customer.orders');
    Route::get('/my-account/orders/{id}', OrderDetails::class)->name('customer.orders.show');
    Route::get('/my-account/profile', Profile::class)->name('customer.profile');

    //logout
    Route::post('/logout', function() {
        auth('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

require __DIR__.'/settings.php';
