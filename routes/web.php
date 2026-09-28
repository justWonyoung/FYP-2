<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\PurchaseRequestController;


// ==============================
// Landing Page
// ==============================

Route::get('/', function () {
    return view('welcome');
});


// ==============================
// Authentication
// ==============================

Auth::routes();


// ==============================
// Role Based Home Redirect
// ==============================

Route::get('/home', function () {

    $user = Auth::user();

    if (!$user) {
        return redirect('/login');
    }


    switch ($user->role) {

        case 'admin':
            return redirect()
                ->route('admin.dashboard');


        case 'staff':
            return redirect()
                ->route('staff.dashboard');


        case 'finance':
            return redirect()
                ->route('finance.dashboard');


        default:
            return redirect('/login');
    }

})->name('home');



// ==============================
// Custom Logout
// ==============================

Route::get('/logout-custom', function () {

    Auth::logout();

    request()
        ->session()
        ->invalidate();

    request()
        ->session()
        ->regenerateToken();


    return redirect('/login');

});



// ==============================
// AUTHENTICATED USERS
// ==============================

Route::middleware(['auth'])->group(function () {



    /*
    |--------------------------------------------------------------------------
    | ADMIN MODULE
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {


            // Dashboard
            Route::get(
                '/dashboard',
                [DashboardController::class, 'admin']
            )
            ->name('dashboard');



            // User Management CRUD
            Route::resource(
                'users',
                UserController::class
            );


            // Purchase Request Approval
            Route::get(
                '/purchase-requests',
                [PurchaseRequestController::class,'index']
            )
            ->name('purchase.requests');


            Route::post(
                '/purchase-request/{id}/approve',
                [PurchaseRequestController::class,'approve']
            )
            ->name('purchase.approve');


            Route::post(
                '/purchase-request/{id}/reject',
                [PurchaseRequestController::class,'reject']
            )
            ->name('purchase.reject');

        });





    /*
    |--------------------------------------------------------------------------
    | STAFF MODULE
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:staff,admin'])
        ->prefix('staff')
        ->name('staff.')
        ->group(function () {


            // Staff Dashboard

            Route::get(
                '/dashboard',
                [DashboardController::class,'staff']
            )
            ->name('dashboard');



            // Create Purchase Request

            Route::get(
                '/pr/create',
                [PurchaseRequestController::class,'create']
            )
            ->name('pr.create');



            Route::post(
                '/pr/store',
                [PurchaseRequestController::class,'store']
            )
            ->name('pr.store');



            // Inventory

            Route::get(
                '/inventory',
                [InventoryController::class,'index']
            )
            ->name('inventory.index');



            // Production

            Route::get(
                '/production',
                [ProductionController::class,'index']
            )
            ->name('production.index');

        });







    /*
    |--------------------------------------------------------------------------
    | FINANCE MODULE
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:finance,admin'])
        ->prefix('finance')
        ->name('finance.')
        ->group(function () {



            // Finance Dashboard

            Route::get(
                '/dashboard',
                [DashboardController::class,'finance']
            )
            ->name('dashboard');



            // Expense List

            Route::get(
                '/expenses',
                [FinancialController::class,'expenses']
            )
            ->name('expenses');



            // Store Expense

            Route::post(
                '/expenses/store',
                [FinancialController::class,'storeExpense']
            )
            ->name('expenses.store');



            // Financial Report

            Route::get(
                '/report',
                [FinancialController::class,'report']
            )
            ->name('report');



            // Purchase Request Review

            Route::get(
                '/purchase-requests',
                [PurchaseRequestController::class,'financeReview']
            )
            ->name('purchase.review');


            Route::post(
                '/purchase-request/{id}/approve',
                [PurchaseRequestController::class,'financeApprove']
            )
            ->name('purchase.approve');


            Route::post(
                '/purchase-request/{id}/reject',
                [PurchaseRequestController::class,'financeReject']
            )
            ->name('purchase.reject');


        });




});