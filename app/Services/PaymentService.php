<?php

namespace App\Services;

use App\Notifications\SubscriptionNotification;
use App\Notifications\UnsubscriptionNotification;
use App\Repositories\PaymentRepository;

class PaymentService
{
    public function __construct(
        public PaymentRepository $paymentRepo
    ) {
    }

    public function storeSubscription($request)
    {
        $customer = auth()->user()->userable;
        if (!$customer) {
            throw new \UnexpectedValueException(__('message.payment.customer_not_found'));
        }

        if ($customer->isPremium()) {
            throw new \UnexpectedValueException(__('message.payment.customer_is_premium'));
        }
        // create new subscription
        $customer->newSubscription('premium', config('services.stripe.product'))->create($request->token);
        return true;
    }

    public function resumeSubscription($request)
    {
        $customer = auth()->user()->userable;
        if (!$customer) {
            throw new \UnexpectedValueException(__('message.payment.customer_not_found'));
        }

        if ($customer->isFree() || !$customer->onGracePeriod()) {
            throw new \UnexpectedValueException(__('message.payment.customer_is_not_on_grace_period'));
        }

        $customer->subscription('premium')->resume();
        if (!empty($request->token)) {
            $customer->updateDefaultPaymentMethod($request->token);
        }
        $customer->notify(new SubscriptionNotification());
        return true;
    }

    public function cancelSubscription($customer)
    {
        if ($customer->isFree()) {
            throw new \UnexpectedValueException(__('message.payment.customer_is_free'));
        }

        if ($customer->hasIncompletePayment()) {
            $customer->subscription('premium')->cancelNow();
        } else {
            $customer->subscription('premium')->cancel();
        }
        $customer->notify(new UnsubscriptionNotification());
        return true;
    }
}
