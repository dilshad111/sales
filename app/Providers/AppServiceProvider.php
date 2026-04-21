<?php

namespace App\Providers;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\PaymentParty::observe(\App\Observers\PaymentPartyObserver::class);
        \App\Models\Bill::observe(\App\Observers\BillObserver::class);
        \App\Models\Payment::observe(\App\Observers\PaymentObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Commission::observe(\App\Observers\CommissionObserver::class);
        \App\Models\CommissionPayment::observe(\App\Observers\CommissionPaymentObserver::class);
        \App\Models\TransactionEntry::observe(\App\Observers\TransactionEntryObserver::class);
        \App\Models\Supplier::observe(\App\Observers\SupplierObserver::class);
        \App\Models\Agent::observe(\App\Observers\AgentObserver::class);
        \App\Models\PurchaseInvoice::observe(\App\Observers\PurchaseInvoiceObserver::class);
        \App\Models\Recovery::observe(\App\Observers\RecoveryObserver::class);
        \App\Models\Transaction::observe(\App\Observers\TransactionObserver::class);


        // Register middleware alias fallback
        if (isset($this->app['router'])) {
            $this->app['router']->aliasMiddleware('user.rights', \App\Http\Middleware\CheckUserRights::class);
        }

        View::composer('*', function ($view) {
            $companySetting = Cache::rememberForever('company_setting', function () {
                if (!Schema::hasTable('company_settings')) {
                    return new CompanySetting([
                        'name' => 'ABC PACKAGES',
                        'address' => 'S.I.T.E. Area, Karachi',
                    ]);
                }

                $setting = CompanySetting::first();

                return $setting ?: new CompanySetting([
                    'name' => 'ABC PACKAGES',
                    'address' => 'S.I.T.E. Area, Karachi',
                ]);
            });

            $view->with('companySetting', $companySetting);
        });
    }
}
