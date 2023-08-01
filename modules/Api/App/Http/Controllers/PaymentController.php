<?php

namespace Modules\Api\App\Http\Controllers;

use App\Enums\PlanType;
use App\Enums\Role;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function __construct(
        public PaymentService  $paymentService,
        public CustomerService $customerService
    ) {
        $this->middleware('role:' . Role::USER->value)->except('cancel', 'getInvoiceDetail', 'invoiceDownload');
        $this->middleware('current.auth.customer')->only(['cancel']);
    }

    public function getSetupIntent(Request $request)
    {
        if ($request->plan !== PlanType::PREMIUM->value) {
            return $this->errorResponse(__('message.notify.error.plan_not_correct'));
        }

        $intent = auth()->user()->userable->createSetupIntent(['payment_method_types' => ['card']]);
        return $this->successResponse(__('message.notify.success.success'), ['client_secret' => $intent->client_secret]);
    }

    public function store(Request $request)
    {
        try {
            $this->paymentService->storeSubscription($request);
            return $this->successResponse(__('message.payment.payment_checkout_success'));
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__('message.payment.payment_checkout_fail'));
        }
    }

    public function resume(Request $request)
    {
        try {
            $this->paymentService->resumeSubscription($request);
            return $this->successResponse(__('message.payment.payment_resume_success'));
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__('message.payment.payment_resume_failed'));
        }
    }

    public function cancel(Customer $customer)
    {
        try {
            $this->paymentService->cancelSubscription($customer);
            return $this->successResponse(__('message.payment.payment_cancel_success'));
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__('message.payment.payment_cancel_failed'));
        }
    }
}
