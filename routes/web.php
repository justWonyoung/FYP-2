<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;

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


Route::post('/logout-custom', function(){

    Auth::logout();

    request()->session()->flush();

    request()->session()->invalidate();

    request()->session()->regenerateToken();


    return redirect('/login');

})
->name('logout.custom');







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



    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class,'admin']
    )
    ->name('dashboard');




    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'users',
        UserController::class
    );




    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */


    // Main Reports Dashboard

    Route::get(
        '/reports',
        [ReportController::class,'index']
    )
    ->name('reports');



    // Detailed Inventory Report

    Route::get(
        '/reports/inventory',
        [ReportController::class,'inventory']
    )
    ->name('reports.inventory');



    // Detailed Purchase Report

    Route::get(
        '/reports/purchase',
        [ReportController::class,'purchase']
    )
    ->name('reports.purchase');



    // Detailed Production Report

    Route::get(
        '/reports/production',
        [ReportController::class,'production']
    )
    ->name('reports.production');



    // Detailed Financial Report

    Route::get(
        '/reports/financial',
        [ReportController::class,'financial']
    )
    ->name('reports.financial');




    /*
    |--------------------------------------------------------------------------
    | Purchase Request Approval
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/purchase-requests',
        [PurchaseRequestController::class,'index']
    )
    ->name('purchase.requests');



    Route::get(
        '/purchase-request/{id}/review',
        [PurchaseRequestController::class,'adminReview']
    )
    ->name('purchase.review');



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



    Route::get(
        '/report',
        [FinancialController::class,'report']
    )
    ->name('report');



    Route::get(
        '/purchase-request/{id}/review',
        [PurchaseRequestController::class,'showFinanceReview']
    )
    ->name('purchase.show');



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
*/


Route::middleware(['auth'])
->group(function(){



    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/inventory',
        [InventoryController::class,'index']
    )
    ->name('inventory.index');

    Route::get(
    '/inventory/receive',
    [InventoryController::class,'receive']
    )
    ->name('inventory.receive');

    Route::get(
    '/inventory/pr-details/{id}',
    [InventoryController::class,'getPRDetails']
    )
    ->name('inventory.pr.details');

    Route::get(
    '/inventory/receiving-history',
    [InventoryController::class,'receivingHistory']
    )
    ->name('inventory.receiving.history');

    Route::get(
    '/inventory/report',
    [InventoryController::class,'report']
    )
    ->name('inventory.report');

    Route::get(
    '/production/{id}/log',
    [ProductionController::class,'log']
    )
    ->name('production.log');

    Route::post(
        '/inventory/store',
        [InventoryController::class,'store']
    )
    ->name('inventory.store');




    /*
    |--------------------------------------------------------------------------
    | Production
    |--------------------------------------------------------------------------
    */


    Route::get(
    '/production',
    [ProductionController::class,'index']
)
->name('production.index');

Route::get(
    '/production/report',
    [ProductionController::class,'report']
)
->name('production.report');

Route::get(
    '/production/create',
    [ProductionController::class,'create']
)
->name('production.create');



Route::post(
    '/production/store',
    [ProductionController::class,'store']
)
->name('production.store');

Route::post(
    '/production/{id}/usage',
    [ProductionController::class,'storeUsage']
)
->name('production.usage');


Route::post(
    '/production/{id}/waste',
    [ProductionController::class,'storeWaste']
)
->name('production.waste');

Route::post(
    '/production/{id}/complete',
    [ProductionController::class,'complete']
)
->name('production.complete');

Route::post(
    '/production/{id}/output',
    [ProductionController::class,'updateOutput']
)
->name('production.output');



Route::post(
    '/production/{id}/complete',
    [ProductionController::class,'complete']
)
->name('production.complete');

});



});

use Illuminate\Support\Facades\Mail;


Route::get('/test-email', function(){

    Mail::raw(
        'NEXORA email system is working.',
        function($message){

            $message
            ->to('yeenjpn@gmail.com')
            ->subject('NEXORA Test Email');

        }
    );


    return "Email sent successfully";

});