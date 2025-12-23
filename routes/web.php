<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\CartonCostingController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Auth::routes();

Route::post('/theme/update', [App\Http\Controllers\ThemeController::class, 'update'])->name('theme.update');

Route::middleware(['auth', \App\Http\Middleware\CheckUserRights::class])->group(function () {
    // AJAX and Specific Routes before resource routes to avoid 404/collision
    Route::get('/payments/get-outstanding-bills', [PaymentController::class, 'getOutstandingBills'])->name('payments.get_outstanding_bills');
    Route::get('/items/get-by-customer/{customer_id}', [ItemController::class, 'getByCustomer'])->name('items.get_by_customer');
    Route::get('/users/rights', [\App\Http\Controllers\UserController::class, 'rights'])->name('users.rights');
    Route::post('/users/{user}/rights', [\App\Http\Controllers\UserController::class, 'updateRights'])->name('users.update_rights');

    Route::resource('customers', CustomerController::class);
    Route::resource('payment-parties', \App\Http\Controllers\PaymentPartyController::class)->names('payment_parties');
    Route::resource('items', ItemController::class);
    Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::resource('bills', BillController::class);
    Route::get('/bills/{bill}/pdf', [BillController::class, 'downloadPdf'])->name('bills.pdf');
    Route::resource('payments', PaymentController::class);
    Route::resource('users', \App\Http\Controllers\UserController::class);

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('company', [CompanySettingController::class, 'edit'])->name('company.edit');
        Route::put('company', [CompanySettingController::class, 'update'])->name('company.update');
        Route::get('audits', [\App\Http\Controllers\AuditController::class, 'index'])->name('audits.index');
    });

    Route::prefix('personal-accounts')->name('personal_accounts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PersonalAccountController::class, 'index'])->name('index');
        Route::post('/commissions', [\App\Http\Controllers\PersonalAccountController::class, 'storeCommission'])->name('commissions.store');
        Route::post('/payments', [\App\Http\Controllers\PersonalAccountController::class, 'storePayment'])->name('payments.store');
        Route::get('/{user}/pdf', [\App\Http\Controllers\PersonalAccountController::class, 'statementPdf'])->name('statement.pdf');
        Route::get('/{user}/csv', [\App\Http\Controllers\PersonalAccountController::class, 'statementCsv'])->name('statement.csv');
        Route::get('/{user}', [\App\Http\Controllers\PersonalAccountController::class, 'show'])->name('statement');
    });

    Route::prefix('salman-commission')->name('salman_commissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SalmanCommissionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\SalmanCommissionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\SalmanCommissionController::class, 'store'])->name('store');
        Route::get('/get-bills', [\App\Http\Controllers\SalmanCommissionController::class, 'getCustomerBills'])->name('get_bills');
        Route::get('/{commission}', [\App\Http\Controllers\SalmanCommissionController::class, 'show'])->name('show');
        Route::get('/{commission}/pdf', [\App\Http\Controllers\SalmanCommissionController::class, 'downloadPdf'])->name('pdf');
        Route::delete('/{commission}', [\App\Http\Controllers\SalmanCommissionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('carton-costing')->name('carton_costing.')->group(function () {
        Route::get('/', [CartonCostingController::class, 'index'])->name('index');
        Route::post('/calculate', [CartonCostingController::class, 'calculate'])->name('calculate');
        Route::post('/store', [CartonCostingController::class, 'store'])->name('store');
        Route::get('/report', [CartonCostingController::class, 'report'])->name('report');
        Route::get('/{cartonCosting}/edit', [CartonCostingController::class, 'edit'])->name('edit');
        Route::get('/{cartonCosting}/print', [CartonCostingController::class, 'print'])->name('print');
        Route::delete('/{cartonCosting}', [CartonCostingController::class, 'destroy'])->name('destroy');
    });

    Route::get('/reports/outstanding-payments', [ReportController::class, 'outstandingPayments'])->name('reports.outstanding_payments');
    Route::get('/reports/outstanding-payments/pdf', [ReportController::class, 'outstandingPaymentsPdf'])->name('reports.outstanding_payments_pdf');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales_pdf');
    Route::get('/reports/customer-statement', [ReportController::class, 'customerStatement'])->name('reports.customer_statement');
    Route::get('/reports/customer-statement/pdf', [ReportController::class, 'customerStatementPdf'])->name('reports.customer_statement_pdf');
    Route::get('/reports/cash-statement', [ReportController::class, 'cashStatement'])->name('reports.cash_statement');
    Route::get('/reports/cash-statement/pdf', [ReportController::class, 'cashStatementPdf'])->name('reports.cash_statement_pdf');

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
});
