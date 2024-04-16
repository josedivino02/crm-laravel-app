<?php

use App\Enum\Can;
use App\Livewire\Admin;
use App\Livewire\Auth\EmailValidation;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Logout;
use App\Livewire\Auth\Password;
use App\Livewire\Auth\Register;
use App\Livewire\Customers;
use App\Livewire\Opportunities;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('auth.register');
Route::get('/email-validation', EmailValidation::class)->middleware('auth')->name('auth.email-validation');
Route::get('/logout', Logout::class)->name('logout');
Route::get('/password/recovery', Password\Recovery::class)->name('password.recovery');
Route::get('/password/reset', Password\Reset::class)->name('password.reset');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', Welcome::class)->name('dashboard');

    Route::get('/customers', Customers\Index::class)->name('customers');
    Route::get('/customers/{customer}', fn() => 'oi')->name('customers.show');

    Route::get('/opportunities', Opportunities\Index::class)->name('opportunities');

    Route::prefix('/admin')->middleware('can:' . Can::BE_AN_ADMIN->value)->group(function () {
        Route::get('/dashboard', Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/users', Admin\Users\Index::class)->name('admin.users');
    });

});