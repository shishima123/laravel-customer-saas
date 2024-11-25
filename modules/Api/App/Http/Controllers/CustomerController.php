<?php

namespace Modules\Api\App\Http\Controllers;

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
use Modules\Api\App\Http\Resources\CustomerResource;
use Modules\Api\App\Http\Resources\InvoiceResource;

class CustomerController extends ApiController
{
    public const NOTIFY_SUCCESS_SUCCESS = 'message.notify.success.success';
    public const NOTIFY_ERROR_UPDATE = 'message.notify.error.update';

    public function __construct(
        public UserRepository     $userRepo,
        public CityRepository     $cityRepo,
        public CustomerRepository $customerRepo,
        public PaymentRepository  $paymentRepo,
        public CustomerService    $customerService
    ) {
        $this->middleware('role:' . Role::ADMIN->value)->only(['index', 'store']);
        $this->middleware('role:' . Role::USER->value)->only(['firstLoginPost', 'setupInformationPut']);
        $this->middleware('current.auth.customer')->only(
            [
                'show',
                'update',
                'changePasswordPost',
                'paymentHistory',
                'getInvoiceDetail',
                'invoiceDownload'
            ]
        );
    }

    public function firstLoginPost(FirstLoginRequest $request)
    {
        $rs = $this->userRepo->updatePasswordFirstLogin($request);
        if ($rs) {
            return $this->successResponse(__('message.notify.success.update'));
        }
        return $this->errorResponse(__(self::NOTIFY_ERROR_UPDATE));
    }

    public function setupInformationPut(CustomerRequest $request)
    {
        $customer = auth()->user()->userable;
        try {
            $this->customerService->updateCustomer($customer, $request);
            return $this->successResponse(__('message.notify.success.setup_account'));
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__(self::NOTIFY_ERROR_UPDATE));
        }
    }

    public function index(Request $request)
    {
        return $this->successResponse(__(self::NOTIFY_SUCCESS_SUCCESS), $this->customerRepo->list($request->all()));
    }

    public function show(Customer $customer)
    {
        return $this->successResponse(__(self::NOTIFY_SUCCESS_SUCCESS), new CustomerResource($customer));
    }

    public function store(CustomerRequest $request)
    {
        try {
            $customer = $this->customerService->createCustomer($request->validated());
            return $this->successResponse(__('message.notify.success.create'), $customer);
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__('message.notify.error.create'));
        }
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        try {
            $this->customerService->updateCustomer($customer, $request);
            return $this->successResponse(__('message.notify.success.edited_account_information_successfully'));
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__(self::NOTIFY_ERROR_UPDATE));
        }
    }

    public function updateStatus(ChangeStatusRequest $request, User $user)
    {
        $successTxt = $request->status ? __('message.notify.success.remove') : __('message.notify.success.update');
        try {
            $this->customerService->updateStatus($request, $user);
            return $this->successResponse($successTxt);
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(__(self::NOTIFY_ERROR_UPDATE));
        }
    }

    public function changePasswordPost(ChangePasswordRequest $request, Customer $customer)
    {
        $rs = $this->userRepo->updatePassword($request, $customer->user);
        if ($rs) {
            return $this->successResponse(__('message.notify.success.change_pass'));
        }
        return $this->errorResponse(__(self::NOTIFY_ERROR_UPDATE));
    }

    public function paymentHistory(Request $request, Customer $customer)
    {
        $dataResponse = $this->paymentRepo->getPaymentHistory($request->all(), $customer, true);
        return $this->successResponse(__(self::NOTIFY_SUCCESS_SUCCESS), $dataResponse);
    }

    public function getInvoiceDetail(Customer $customer, $invoice)
    {
        $invoice = $customer->findInvoice($invoice);
        return $this->successResponse(__('message.notify.success.success'),
            [
                'customer' => new CustomerResource($customer),
                'invoice' => new InvoiceResource($invoice)
            ]
        );
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
