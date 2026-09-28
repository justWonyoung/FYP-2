<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\PurchaseRequestController;



/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return view('welcome');

});



/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Auth::routes();




/*
|--------------------------------------------------------------------------
| HOME REDIRECT
|--------------------------------------------------------------------------
*/

Route::get('/home', function () {


    $user = Auth::user();


    if(!$user){

        return redirect('/login');

    }



    return match($user->role){

        'admin'
            => redirect()->route('admin.dashboard'),


        'staff'
            => redirect()->route('staff.dashboard'),


        'finance'
            => redirect()->route('finance.dashboard'),


        default
            => redirect('/login'),

    };


})
->name('home');





/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/


Route::get('/logout-custom', function(){

    Auth::logout();


    request()
        ->session()
        ->invalidate();


    request()
        ->session()
        ->regenerateToken();


    return redirect('/login');


});







/*
|--------------------------------------------------------------------------
| AUTHENTICATED USERS
|--------------------------------------------------------------------------
*/


Route::middleware(['auth'])->group(function(){






/*
|--------------------------------------------------------------------------
| ADMIN MODULE
|--------------------------------------------------------------------------
*/


Route::middleware(['role:admin'])
->prefix('admin')
->name('admin.')
->group(function(){



    Route::get(
        '/dashboard',
        [DashboardController::class,'admin']
    )
    ->name('dashboard');



    Route::resource(
        'users',
        UserController::class
    );



    // Admin Purchase Approval

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
->group(function(){



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



});









/*
|--------------------------------------------------------------------------
| FINANCE MODULE
|--------------------------------------------------------------------------
*/


Route::middleware(['role:finance,admin'])
->prefix('finance')
->name('finance.')
->group(function(){



    Route::get(
        '/dashboard',
        [DashboardController::class,'finance']
    )
    ->name('dashboard');



    // Expenses

    Route::get(
        '/expenses',
        [FinancialController::class,'expenses']
    )
    ->name('expenses');



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



    // Finance Purchase Review

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









/*
|--------------------------------------------------------------------------
| SHARED OPERATIONAL MODULE
|--------------------------------------------------------------------------
|
| Inventory and Production are shared modules.
| Admin, Staff and Finance can access them.
|
|--------------------------------------------------------------------------
*/


Route::middleware(['auth'])
->group(function(){



    // Inventory

    Route::get(
        '/inventory',
        [InventoryController::class,'index']
    )
    ->name('inventory.index');



    // Add Material

    Route::post(
        '/inventory/store',
        [InventoryController::class,'store']
    )
    ->name('inventory.store');



    // Production

    Route::get(
        '/production',
        [ProductionController::class,'index']
    )
    ->name('production.index');



});



});