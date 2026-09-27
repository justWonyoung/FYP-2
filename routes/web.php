<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\PurchaseRequestController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Custom logout
Route::get('/logout-custom', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('users', UserController::class);
    });

    // Staff Routes
    Route::middleware(['role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', function () {
            return view('staff.dashboard');
        })->name('dashboard');
    });

    // Finance Routes
    Route::middleware(['role:finance,admin'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', function () {
            return view('finance.dashboard');
        })->name('dashboard');

        Route::get('/expenses', [FinancialController::class, 'expenses'])->name('expenses');
        Route::get('/report', [FinancialController::class, 'report'])->name('report');
    });

    // Direct Access Links matching Sidebar Navigation
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/pr/create', [PurchaseRequestController::class, 'create'])->name('pr.create');
});