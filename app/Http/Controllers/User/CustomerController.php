<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\FirstLoginRequest;
use App\Repositories\CityRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    protected const CUSTOMER_INDEX_ROUTE = 'customers.index';
    protected const NOTIFY_ERROR_UPDATE = 'message.notify.error.update';

    public function __construct(
        public UserRepository     $userRepo,
        public CityRepository     $cityRepo,
        public CustomerRepository $customerRepo,
        public PaymentRepository  $paymentRepo,
        public CustomerService    $customerService
    ) {

    }

    public function firstLoginGet()
    {
        $user = auth()->user();
        if ($user->isAdmin() || !$user->isFirstLogin()) {
            return redirect()->route(self::CUSTOMER_INDEX_ROUTE);
        }
        if (!session()->has('errors')) {
            request()->session()->flash('success', __('message.notify.success.sign_up_success'));
            request()->session()->flash('success_content', __('message.notify.success.complete_setting'));
        }
        return view('auth.user-first-change-password');
    }

    public function firstLoginPost(FirstLoginRequest $request)
    {
        $rs = $this->userRepo->updatePasswordFirstLogin($request);
        if ($rs) {
            return redirect()->route('customers.setup-information')
                ->with('success', __('message.notify.success.update'));
        }
        return redirect()->back()->with('error', __(self::NOTIFY_ERROR_UPDATE));
    }

    public function setupInformationGet()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route(self::CUSTOMER_INDEX_ROUTE);
        }

        $cities = $this->cityRepo->all();
        return view('customer.setup_information', compact('cities'));
    }

    public function setupInformationPut(CustomerRequest $request)
    {
        $customer = auth()->user()->userable;
        try {
            $this->customerService->updateCustomer($customer, $request);
            $request->session()->put('is_changed_password', true);
            return redirect()->route('payments.index')
                ->with('success', __('message.notify.success.setup_account'));
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('customers.setup-information')->with('error', __(self::NOTIFY_ERROR_UPDATE));
        }
    }
}
