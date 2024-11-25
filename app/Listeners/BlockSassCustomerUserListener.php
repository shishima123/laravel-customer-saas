<?php

namespace App\Listeners;

use App\Events\BlockSassCustomerUserEvent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BlockSassCustomerUserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Cancel current subscription when mark remove user
     *
     * @param \App\Events\BlockSassCustomerUserEvent $event
     * @return bool
     */
    public function handle(BlockSassCustomerUserEvent $event): bool
    {
        if ($event->user->isInActive()) {
            $customer = $event->user->userable;
            if ($customer && $customer->isPremium()) {
                $customer->subscription('premium')->cancel();
            }
        }
        return true;
    }
}
