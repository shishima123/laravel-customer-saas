<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\FirstLoginRequest;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\CityRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected const CUSTOMER_INDEX_ROUTE = 'customers.index';
    protected const CUSTOMER_PROFILE_ROUTE = 'customers.profile';
    protected const CUSTOMER_SHOW_ROUTE = 'customers.show';
    protected const NOTIFY_ERROR_UPDATE = 'message.notify.error.update';
    protected const CUSTOMER_UPDATE_VIEW = 'customer.update';

    public function __construct(
        public UserRepository     $userRepo,
        public CityRepository     $cityRepo,
        public CustomerRepository $customerRepo,
        public PaymentRepository  $paymentRepo,
        public CustomerService    $customerService
    ) {
        $this->middleware('role:'. Role::ADMIN->value)->only(['index', 'show', 'store']);
        $this->middleware('current.auth.customer')->only(
            [
                'profile',
                'update',
                'changePasswordGet',
                'changePasswordPost',
                'paymentHistory',
                'getInvoiceDetail',
                'invoiceDownload'
            ]
        );
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

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->isUser()) {
            if (session()->has('is_changed_password') && session()->get('is_changed_password')) {
                $request->session()->forget('is_changed_password');
                return redirect()->route(self::CUSTOMER_PROFILE_ROUTE)->with([
                    'success' => __('message.notify.success.setup_account'),
                    'success_content' => __('message.notify.success.content_setup_account_successfully')
                ]);
            }
            return redirect()->route(self::CUSTOMER_PROFILE_ROUTE);
        }
        if ($request->ajax()) {
            return response()->json($this->customerRepo->list($request->all()));
        }
        return view('customer.index');
    }

    public function show(Customer $customer)
    {
        $cities = $this->cityRepo->all();
        $subpage = '_profile';
        return view(self::CUSTOMER_UPDATE_VIEW, compact('customer', 'cities', 'subpage'));
    }

    public function profile()
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route(self::CUSTOMER_INDEX_ROUTE);
        }
        $customer = auth()->user()->userable;
        $cities = $this->cityRepo->all();
        $subpage = '_profile';
        return view(self::CUSTOMER_UPDATE_VIEW, compact('customer', 'cities', 'subpage'));
    }

    public function store(CustomerRequest $request)
    {
        try {
            $customer = $this->customerService->createCustomer($request->all());
            return response()->json([
                'message' => __('message.notify.success.create'),
                'url' => route(self::CUSTOMER_SHOW_ROUTE, $customer)
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => __('message.notify.error.create')], 400);
        }
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        try {
            $this->customerService->updateCustomer($customer, $request);
            if (auth()->user()->isAdmin()) {
                return redirect()->route(self::CUSTOMER_SHOW_ROUTE, [$customer])
                    ->with('success', __('message.notify.success.edited_account_information_successfully'));
            }
            return redirect()->route(self::CUSTOMER_PROFILE_ROUTE)
                ->with('success', __('message.notify.success.edited_account_information_successfully'));
        } catch (\Exception $e) {
            report($e);
            if (auth()->user()->isAdmin()) {
                return redirect()->route(self::CUSTOMER_SHOW_ROUTE, [$customer])
                    ->with('error', __(self::NOTIFY_ERROR_UPDATE));
            }
            return redirect()->route(self::CUSTOMER_PROFILE_ROUTE)->with('error', __(self::NOTIFY_ERROR_UPDATE));
        }
    }

    public function updateStatus(ChangeStatusRequest $request, User $user)
    {
        $successTxt = $request->status ? __('message.notify.success.remove') : __('message.notify.success.update');
        try {
            $this->customerService->updateStatus($request, $user);
            return response()->json(['success' => $successTxt]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => __(self::NOTIFY_ERROR_UPDATE)], 400);
        }
    }

    public function changePasswordGet(Customer $customer)
    {
        $subpage = '_account';
        return view(self::CUSTOMER_UPDATE_VIEW, compact('customer', 'subpage'));
    }

    public function changePasswordPost(ChangePasswordRequest $request, Customer $customer)
    {
        $rs = $this->userRepo->updatePassword($request, $customer->user);
        if ($rs) {
            return redirect()->back()
                ->with('success', __('message.notify.success.change_pass'));
        }
        return redirect()->back()->with('error', __(self::NOTIFY_ERROR_UPDATE));
    }

    public function paymentHistory(Request $request, Customer $customer)
    {
        if ($request->ajax()) {
            return response()->json($this->paymentRepo->getPaymentHistory($request->all(), $customer, true));
        }
        $paymentHistories = $this->paymentRepo->getPaymentHistory($request->all(), $customer, false);
        $subpage = '_payment_history';
        return view(self::CUSTOMER_UPDATE_VIEW, compact('customer', 'subpage', 'paymentHistories'));
    }

    public function unsubscriptionPlanGet()
    {
        $user = auth()->user();
        if ($user->isAdmin() || $user->userable->isFree() || $user->userable->onGracePeriod()) {
            return redirect()->route(self::CUSTOMER_INDEX_ROUTE);
        }
        $noHeader = true;
        return view('customer.unsubscription_plan', compact('noHeader'));
    }

    public function getInvoiceDetail(Customer $customer, $invoice)
    {
        $invoice = $customer->findInvoice($invoice);
        $html = view('payment._payment_detail_modal', compact('customer', 'invoice'))->render();
        return response()->json(['html' => $html]);
    }

    public function invoiceDownload(Customer $customer, $invoice)
    {
        return $customer->downloadInvoice($invoice, [
            'vendor' => config('services.company.name'),
            'product' => 'Premium',
            'street' => config('services.company.add1'),
            'location' => config('services.company.add2'),
            'phone' => config('services.company.phone'),
        ]);
    }
}
