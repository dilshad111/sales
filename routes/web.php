<?php

use App\Http\Controllers\PurchaseDeliveryChallanController;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CartonCostingController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryChallanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentPartyController;
use App\Http\Controllers\PersonalAccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalmanCommissionController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\ThirdPartyController;
use App\Http\Controllers\ThirdPartyPaymentController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExternalPartyController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FinancialYearController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Auth::routes();

Route::post('/theme/update', [ThemeController::class, 'update'])->name('theme.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::middleware(['auth', \App\Http\Middleware\CheckUserRights::class])->group(function () {
    // AJAX and Specific Routes before resource routes to avoid 404/collision
    Route::get('/payments/get-outstanding-bills', [PaymentController::class, 'getOutstandingBills'])->name('payments.get_outstanding_bills');
    Route::get('/items/get-by-customer/{customer_id}', [ItemController::class, 'getByCustomer'])->name('items.get_by_customer');
    Route::get('/users/rights', [UserController::class, 'rights'])->name('users.rights');
    Route::post('/users/{user}/rights', [UserController::class, 'updateRights'])->name('users.update_rights');

    Route::resource('customers', CustomerController::class);
    Route::resource('payment-parties', PaymentPartyController::class)
        ->names('payment_parties')
        ->parameters(['payment-parties' => 'payment_party']);
    Route::resource('items', ItemController::class);
    Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::resource('bills', BillController::class);
    Route::get('/bills/{bill}/pdf', [BillController::class, 'downloadPdf'])->name('bills.pdf');

    // Delivery Challans
    Route::post('delivery-challans/create-bill', [DeliveryChallanController::class, 'createBill'])->name('delivery_challans.create_bill');
    Route::get('delivery-challans/{deliveryChallan}/print', [DeliveryChallanController::class, 'print'])->name('delivery_challans.print');
    Route::resource('delivery-challans', DeliveryChallanController::class)
        ->names('delivery_challans')
        ->parameters(['delivery-challans' => 'deliveryChallan']);

    Route::get('/payments/{payment}/print', [PaymentController::class, 'print'])->name('payments.print');
    Route::resource('payments', PaymentController::class);
    Route::resource('users', UserController::class);

    Route::resource('accounts', AccountController::class);

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('company', [CompanySettingController::class, 'edit'])->name('company.edit');
        Route::put('company', [CompanySettingController::class, 'update'])->name('company.update');
        Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    });

    Route::prefix('personal-accounts')->name('personal_accounts.')->group(function () {
        Route::get('/', [PersonalAccountController::class, 'index'])->name('index');
        Route::post('/commissions', [PersonalAccountController::class, 'storeCommission'])->name('commissions.store');
        Route::post('/payments', [PersonalAccountController::class, 'storePayment'])->name('payments.store');
        Route::get('/{user}/pdf', [PersonalAccountController::class, 'statementPdf'])->name('statement.pdf');
        Route::get('/{user}/csv', [PersonalAccountController::class, 'statementCsv'])->name('statement.csv');
        Route::get('/{user}', [PersonalAccountController::class, 'show'])->name('statement');
    });



    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('outstanding-payments', [ReportController::class, 'outstandingPayments'])->name('outstanding_payments');
        Route::get('outstanding-payments/pdf', [ReportController::class, 'outstandingPaymentsPdf'])->name('outstanding_payments_pdf');
        Route::get('outstanding-payments/excel', [ReportController::class, 'outstandingPaymentsExcel'])->name('outstanding_payments_excel');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('sales/pdf', [ReportController::class, 'salesPdf'])->name('sales_pdf');
        Route::get('sales/excel', [ReportController::class, 'salesExcel'])->name('sales_excel');
        Route::get('customer-ledger', [ReportController::class, 'customerLedger'])->name('customer_ledger');
        Route::get('customer-statement', [ReportController::class, 'customerStatement'])->name('customer_statement');
        Route::get('customer-statement/pdf', [ReportController::class, 'customerStatementPdf'])->name('customer_statement_pdf');
        Route::get('customer-statement/excel', [ReportController::class, 'customerStatementExcel'])->name('customer_statement_excel');
        Route::get('cash-statement', [ReportController::class, 'cashStatement'])->name('cash_statement');
        Route::get('cash-statement/pdf', [ReportController::class, 'cashStatementPdf'])->name('cash_statement_pdf');
        Route::get('cash-statement/excel', [ReportController::class, 'cashStatementExcel'])->name('cash_statement_excel');
        Route::get('payment-parties', [ReportController::class, 'paymentPartiesList'])->name('payment_parties_list');
        Route::get('payment-parties/{payment_party}/statement', [ReportController::class, 'paymentPartyStatement'])->name('payment_party_statement');
        Route::get('friend-outstanding', function() { return redirect()->route('reports.payment_parties_list'); });

        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        
        // Financial Statements
        Route::get('trial-balance', [\App\Http\Controllers\FinancialReportController::class, 'trialBalance'])->name('trial_balance');
        Route::get('profit-loss', [\App\Http\Controllers\FinancialReportController::class, 'profitLoss'])->name('profit_loss');
        Route::get('balance-sheet', [\App\Http\Controllers\FinancialReportController::class, 'balanceSheet'])->name('balance_sheet');
        
        // Purchase Workflow Reports
        Route::get('purchase-report', [ReportController::class, 'purchaseReport'])->name('purchase_report');
        Route::get('agent-commissions', [ReportController::class, 'agentCommissionReport'])->name('agent_commission_report');
        Route::get('partner-payouts', [ReportController::class, 'directorPayoutReport'])->name('director_payout_report');
        Route::get('cash-ledger', [ReportController::class, 'cashLedger'])->name('cash_ledger');
        Route::get('bank-ledger', [ReportController::class, 'bankLedger'])->name('bank_ledger');
        Route::get('writeoff-ledger', [ReportController::class, 'writeoffLedger'])->name('writeoff_ledger');
        Route::get('supplier-statement', [ReportController::class, 'supplierStatement'])->name('supplier_statement');
    });


    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/carton-costing', [CartonCostingController::class, 'index'])->name('carton_costing.index');
    Route::post('/carton-costing/calculate', [CartonCostingController::class, 'calculate'])->name('carton_costing.calculate');
    Route::post('/carton-costing/store', [CartonCostingController::class, 'store'])->name('carton_costing.store');
    Route::get('/carton-costing/report', [CartonCostingController::class, 'report'])->name('carton_costing.report');
    Route::get('/carton-costing/{cartonCosting}/edit', [CartonCostingController::class, 'edit'])->name('carton_costing.edit');
    Route::get('/carton-costing/{cartonCosting}/print', [CartonCostingController::class, 'print'])->name('carton_costing.print');
    Route::delete('/carton-costing/{cartonCosting}', [CartonCostingController::class, 'destroy'])->name('carton_costing.destroy');

    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/ledger/{account}', [LedgerController::class, 'show'])->name('ledger.show');
    Route::get('/ledger/{account}/pdf', [LedgerController::class, 'pdf'])->name('ledger.pdf');
    
    Route::resource('vouchers', VoucherController::class);
    Route::get('vouchers/{voucher}/print', [VoucherController::class, 'print'])->name('vouchers.print');
    Route::resource('banks', BankController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('agents', AgentController::class);
    Route::resource('purchase-items', PurchaseItemController::class)->names('purchase_items');
    Route::get('purchase-items/by-supplier/{supplier_id}', [PurchaseItemController::class, 'getBySupplier'])->name('purchase_items.by_supplier');
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)->names('purchase_invoices');
    Route::resource('recoveries', RecoveryController::class);
    Route::resource('purchase-delivery-challans', PurchaseDeliveryChallanController::class)->names('purchase_delivery_challans');
    
    Route::post('financial-years/{financialYear}/close', [FinancialYearController::class, 'close'])->name('financial_years.close');
    Route::post('financial-years/{financialYear}/reopen', [FinancialYearController::class, 'reopen'])->name('financial_years.reopen');
    Route::resource('financial-years', FinancialYearController::class)->names('financial_years');

    Route::get('salman-commissions/get-customer-bills', [SalmanCommissionController::class, 'getCustomerBills'])->name('salman_commissions.get_customer_bills');
    Route::get('salman-commissions/{commission}/pdf', [SalmanCommissionController::class, 'downloadPdf'])->name('salman_commissions.export_pdf');
    Route::resource('salman-commissions', SalmanCommissionController::class)
        ->names('salman_commissions')
        ->parameters(['salman-commissions' => 'commission']);
});
