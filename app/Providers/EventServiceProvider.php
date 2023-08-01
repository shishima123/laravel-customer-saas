<?php

namespace App\Providers;

use App\Events\BlockSassCustomerUserEvent;
use App\Events\CreateUserEvent;
use App\Events\UpdateCompanyEvent;
use App\Events\SyncMasterDataEvent;
use App\Events\UpdateSassCustomerUserEvent;
use App\Events\UpdateUserEvent;
use App\Listeners\BlockSassCustomerUserListener;
use App\Events\CreateCompanyEvent;
use App\Events\CreateSassCustomerUserEvent;
use App\Listeners\CreateCompanyListener;
use App\Listeners\CreateSassCustomerUserListener;
use App\Listeners\CreateUserListener;
use App\Listeners\StripeHandledListener;
use App\Listeners\StripeReceivedListener;
use App\Listeners\UpdateCompanyListener;
use App\Listeners\SyncMasterDataListener;
use App\Listeners\UpdateSassCustomerUserListener;
use App\Listeners\UpdateUserListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BlockSassCustomerUserEvent::class => [
            BlockSassCustomerUserListener::class,
        ],
        CreateSassCustomerUserEvent::class => [
            CreateSassCustomerUserListener::class,
        ],
        UpdateSassCustomerUserEvent::class => [
            UpdateSassCustomerUserListener::class,
        ],
        CreateCompanyEvent::class => [
            CreateCompanyListener::class,
        ],
        UpdateCompanyEvent::class => [
            UpdateCompanyListener::class,
        ],
        CreateUserEvent::class => [
            CreateUserListener::class,
        ],
        UpdateUserEvent::class => [
            UpdateUserListener::class,
        ],
        WebhookReceived::class => [
            StripeReceivedListener::class,
        ],
        WebhookHandled::class => [
            StripeHandledListener::class,
        ],
        SyncMasterDataEvent::class => [
            SyncMasterDataListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
