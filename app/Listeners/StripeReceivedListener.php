<?php

namespace App\Listeners;

use App\Models\Payment;
use App\Notifications\FailSubscriptionNotification;
use App\Notifications\SubscriptionNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Events\WebhookReceived;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Cashier;

class StripeReceivedListener
{
    /**
     * Handle received Stripe webhooks.
     *
     * @param \Laravel\Cashier\Events\WebhookReceived $event
     * @return Response
     */
    public function handle(WebhookReceived $event): Response
    {
        $payload = $event->payload;

        $method = 'handle' . Str::studly(str_replace('.', '_', $payload['type']));

        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        }

        return $this->missingMethod();
    }

    /**
     * Handle charge succeeded
     *
     * @param array $payload
     * @return Response
     */
    public function handleChargeSucceeded(array $payload): Response
    {
        try {
            $customer = $this->getUserByStripeId($payload['data']['object']['customer']);
            if ($customer) {
                $customer->notify(new SubscriptionNotification());
            }

            $this->savePayment($payload, $customer);
        } catch (\Throwable $th) {
            report($th);
            return new Response($th->getMessage(), 422);
        }

        return $this->successMethod();
    }

    /**
     * Handle charge failed
     *
     * @param array $payload
     * @return Response
     */
    public function handleChargeFailed(array $payload): Response
    {
        try {
            $customer = $this->getUserByStripeId($payload['data']['object']['customer']);

            if ($customer) {
                $customer->notify(new FailSubscriptionNotification());
            }

            $this->savePayment($payload, $customer);
        } catch (\Throwable $th) {
            report($th);
            return new Response($th->getMessage(), 422);
        }

        return $this->successMethod();
    }

    /**
     * Handle charge refunded
     *
     * @param array $payload
     * @return Response
     */
    public function handleChargeRefunded(array $payload): Response
    {
        try {
            $customer = $this->getUserByStripeId($payload['data']['object']['customer']);
            $this->savePayment($payload, $customer);
        } catch (\Throwable $th) {
            report($th);
            return new Response($th->getMessage(), 422);
        }
        return $this->successMethod();
    }

    protected function savePayment($payload, $customer): void
    {
        if ($customer) {
            $dataPayment = $this->generateDataPayment($payload, $customer);
            Payment::create($dataPayment);
        }
    }

    /**
     * Create data for table payment
     *
     * @param array $payload
     * @param $customer
     * @return array
     */
    protected function generateDataPayment(array $payload, $customer): array
    {
        // create data
        $object = $payload['data']['object'];

        return [
            'id' => $object['id'],
            'customer_id' => $customer->id,
            'type' => $payload['type'],
            'amount' => $object['amount'],
            'currency' => $object['currency'],
            'captured' => $object['captured'],
            'charge_date' => Carbon::parse($object['created'])->format('Y-m-d H:i:s'),
            'stripe_id' => $object['customer'],
            'description' => $object['description'],
            'failure_code' => $object['failure_code'],
            'failure_message' => $object['failure_message'],
            'invoice_id' => $object['invoice'],
            'paid' => $object['paid'],
            'payment_method' => $object['payment_method'],
            'status' => $object['status'],
            'amount_refunded' => $object['amount_refunded']
        ];
    }

    protected function successMethod(): Response
    {
        return new Response('Webhook Handled', 200);
    }

    protected function missingMethod(): Response
    {
        return new Response;
    }

    protected function getUserByStripeId($stripeId)
    {
        return Cashier::findBillable($stripeId);
    }
}
