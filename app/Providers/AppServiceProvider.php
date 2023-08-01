<?php

namespace App\Providers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use File;
use App;
use Laravel\Cashier\Cashier;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.env') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        Cashier::ignoreMigrations();
        Cashier::keepPastDueSubscriptionsActive();
        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useCustomerModel(Customer::class);
        Paginator::useBootstrap();

        // force https for all image css, js
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Collect all language and send to view, used with multi-language in javascript
        View::composer(['layouts.layout', 'layouts.clients.app'], function ($view) {
            $view->with('translations', collect(File::allFiles(base_path('lang/' . App::getLocale())))
                ->flatMap(function ($file) {
                    return [
                        ($translation = $file->getBasename('.php')) => trans($translation),
                    ];
                })->toJson());
        });

        Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();

            // Delete temp/invoices folder when successful email subscription is sent
            if (is_array($payload) && Str::contains($payload['displayName'], 'SubscriptionNotification')) {
                File::cleanDirectory(public_path('temp/invoices'));
            }
        });
    }
}
