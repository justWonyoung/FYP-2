<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\FinancialController;
use App\Models\PurchaseRequest;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::get('/admin/dashboard', function () {
        $pendingApprovals = PurchaseRequest::where('finance_status', 'approved')
                            ->where('approval_status', 'pending')
                            ->latest()->get();
        return view('admin.dashboard', compact('pendingApprovals'));
    })->name('admin.dashboard');

    Route::get('/pr/admin-show/{id}', [PurchaseRequestController::class, 'showAdminApprove'])->name('pr.admin.show');
    Route::post('/pr/admin-approve/{id}', [PurchaseRequestController::class, 'adminApprove'])->name('pr.admin.approve');

    // Staff Routes
    Route::get('/staff/dashboard', function () {
        $requests = PurchaseRequest::latest()->get();
        return view('staff.dashboard', compact('requests'));
    })->name('staff.dashboard');

    Route::get('/pr/create', [PurchaseRequestController::class, 'create'])->name('pr.create');
    Route::post('/pr/store', [PurchaseRequestController::class, 'store'])->name('pr.store');

    // Inventory & Receiving Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/receiving/create', [InventoryController::class, 'createReceiving'])->name('receiving.create');
    Route::post('/receiving/store', [InventoryController::class, 'storeReceiving'])->name('receiving.store');

    // Production & Waste Routes
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/production/store', [ProductionController::class, 'store'])->name('production.store');
    Route::get('/production/record/{id}', [ProductionController::class, 'showRecordForm'])->name('production.record.show');
    Route::post('/production/record/{id}', [ProductionController::class, 'storeRecordUsage'])->name('production.record.store');

    // Finance & Expenses Routes
    Route::get('/finance/dashboard', function () {
        $pendingReviews = PurchaseRequest::where('finance_status', 'pending')->latest()->get();
        return view('finance.dashboard', compact('pendingReviews'));
    })->name('finance.dashboard');

    Route::get('/pr/finance-show/{id}', [PurchaseRequestController::class, 'showFinanceReview'])->name('pr.finance.show');
    Route::post('/pr/finance-review/{id}', [PurchaseRequestController::class, 'financeReview'])->name('pr.finance.review');

    Route::get('/finance/expenses', [FinancialController::class, 'index'])->name('finance.expenses.index');
    Route::post('/finance/expenses/store', [FinancialController::class, 'storeExpense'])->name('finance.expenses.store');
    Route::get('/finance/report', [FinancialController::class, 'report'])->name('finance.report');

    // Logout
    Route::get('/logout-custom', function () {
        Auth::logout();
        return redirect('/login');
    });
});