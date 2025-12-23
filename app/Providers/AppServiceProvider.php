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
