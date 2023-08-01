<?php

namespace App\Http\Controllers;

use App\Enums\PlanType;
use App\Enums\Role;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected const PAYMENT_CHECKOUT_FAIL = 'message.payment.payment_checkout_fail';
    protected const PAYMENT_CANCEL_FAILED = 'message.payment.payment_cancel_failed';
    protected const PAYMENT_CANCEL_SUCCESS = 'message.payment.payment_cancel_success';

    public function __construct(
        public PaymentService  $paymentService,
        public CustomerService $customerService
    ) {
        $this->middleware('role:' . Role::USER->value)->except('cancel', 'getInvoiceDetail', 'invoiceDownload');
        $this->middleware('current.auth.customer')->only(['cancel']);
    }

    public function index()
    {
        return view('payment.index');
    }

    public function create(Request $request)
    {
        if ($request->plan !== PlanType::PREMIUM->value) {
            return redirect()->route('customers.index');
        }

        $intent = auth()->user()->userable->createSetupIntent(['payment_method_types' => ['card']]);
        return view('payment.create', compact('intent'));
    }

    public function store(Request $request)
    {
        try {
            $this->paymentService->storeSubscription($request);
            return response()->json(['message' => __('message.payment.payment_checkout_success')]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => __(self::PAYMENT_CHECKOUT_FAIL)], 400);
        }
    }

    public function resumeWithNewCardForm()
    {
        $intent = auth()->user()->userable->createSetupIntent(['payment_method_types' => ['card']]);
        return view('payment.resume', compact('intent'));
    }

    public function resume(Request $request)
    {
        try {
            $this->paymentService->resumeSubscription($request);

            if ($request->ajax()) {
                return response()->json(['message' => __('message.payment.payment_resume_success')]);
            }
            return redirect()->back()->with('success', __('message.payment.payment_resume_success'));
        } catch (\Exception $e) {
            report($e);

            if ($request->ajax()) {
                return response()->json(['message' => __(self::PAYMENT_CHECKOUT_FAIL)], 400);
            }
            return redirect()->back()->with('error', __('message.payment.payment_resume_failed'));
        }
    }

    public function cancel(Request $request, Customer $customer)
    {
        try {
            $this->paymentService->cancelSubscription($customer);
            if ($request->ajax()) {
                return response()->json(['message' => __(self::PAYMENT_CANCEL_SUCCESS)]);
            }
            if ($request->redirect_to_profile) {
                return redirect()
                    ->route('customers.show', $customer)
                    ->with('success', __(self::PAYMENT_CANCEL_SUCCESS));
            }
            return redirect()->back()->with('success', __(self::PAYMENT_CANCEL_SUCCESS));
        } catch (\Exception $e) {
            report($e);

            if ($request->ajax()) {
                return response()->json(['message' => __(self::PAYMENT_CANCEL_FAILED)], 400);
            }
            return redirect()->back()->with('error', __(self::PAYMENT_CANCEL_FAILED));
        }
    }
}
