<?php

namespace App\Listeners;

use App\Enums\PremiumStatus;
use Carbon\Carbon;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Cashier;
use Stripe\Subscription as StripeSubscription;

class StripeHandledListener
{
    /**
     * Handle received Stripe webhooks.
     *
     * @param WebhookHandled $event
     * @return void
     */
    public function handle(WebhookHandled $event)
    {
        $payload = $event->payload;
        switch ($payload['type']) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $customer = $this->getUserByStripeId($payload['data']['object']['customer']);
                if ($customer) {
                    if (!isset($payload['data']['object']['status'])) {
                        return;
                    }

                    // save next_cycle_date to database
                    if ($payload['data']['object']['status'] == StripeSubscription::STATUS_ACTIVE) {
                        $nextCycleDate = $payload['data']['object']['current_period_end'];
                        $customer->next_cycle_date = Carbon::createFromTimeStamp($nextCycleDate);
                        $customer->is_premium = PremiumStatus::PREMIUM;
                    } elseif ($payload['data']['object']['status'] == StripeSubscription::STATUS_PAST_DUE) {
                        // change the next_cycle_date back to the previous date
                        $nextCycleDate = $payload['data']['object']['current_period_start'];
                        $customer->next_cycle_date = Carbon::createFromTimeStamp($nextCycleDate);
                    }
                    $customer->save();
                }
                break;
            case 'customer.subscription.deleted':
                $customer = $this->getUserByStripeId($payload['data']['object']['customer']);
                if ($customer) {
                    $customer->is_premium = PremiumStatus::FREE;
                    $customer->save();
                }
                break;
            default:
        }
    }

    protected function getUserByStripeId($stripeId)
    {
        return Cashier::findBillable($stripeId);
    }
}
