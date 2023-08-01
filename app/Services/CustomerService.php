<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\UserChangeInfo;
use App\Models\Address;
use App\Models\Customer;
use App\Notifications\CreateCustomerUserNotification;
use App\Repositories\AddressRepository;
use App\Repositories\CityRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Traits\RenderIdNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerService
{
    use RenderIdNumberTrait;

    public function __construct(
        public UserRepository     $userRepo,
        public CityRepository     $cityRepo,
        public AddressRepository  $addressRepo,
        public CustomerRepository $customerRepo,
        public PaymentRepository  $paymentRepo,
        public CompanyRepository  $companyRepo
    ) {
    }

    public function createCustomer(array $data, bool $isCreatedByClient = false)
    {
        return DB::transaction(function () use ($data, $isCreatedByClient) {
            $companyInput = [
                'name' => $data['company_name']
            ];
            $company = $this->companyRepo->create($companyInput);

            $customerInput = [
                'email' => $data['email'],
                'name' => $data['name'] ?? '',
                'phone_number' => $data['phone_number'] ?? '',
                'company_id' => $company->id,
                'user_number' => $this->renderNumber(app(Customer::class), 'user_number'),
            ];
            $customer = $this->customerRepo->create($customerInput);

            $password = Str::random(8);
            $userInput = [
                'email' => $data['email'],
                'password' => Hash::make($password),
                'role' => Role::USER,
                'userable_id' => $customer->id,
                'userable_type' => 'App\Models\Customer',
            ];
            $user = $this->userRepo->create($userInput);

            // If the user is created by the admin, the password will be attached
            $passwordAttachment = null;
            if ($isCreatedByClient) {
                $passwordAttachment = $password;
            }

            $user->notify(new CreateCustomerUserNotification($passwordAttachment));
            return $customer;
        });
    }

    public function updateCustomer($customer, $request)
    {
        DB::transaction(function () use ($customer, $request) {
            $customer->load(['company.address', 'user']);

            $address = $customer->company->address ?? app(Address::class);
            $addressInput = $request->only('add1', 'state', 'zipcode', 'city_id');
            if (isset($addressInput['add1']) && !empty($addressInput['add1'])) {
                $address = $this->addressRepo->updateOrCreate(['id' => $address->id], $addressInput);
            }

            $company = $customer->company;
            $companyInput = [
                'name' => $request->company_name,
                'address_id' => $address->id,
            ];
            $company->fill($companyInput)->save();

            $customerInput = $request->only('name', 'email', 'phone_number', 'city_id', 'billing_contact_email');
            $customer->fill($customerInput)->save();

            $userInput = ['is_changed_info' => UserChangeInfo::CHANGED];
            if ($request->email) {
                $userInput['email'] = $request->email;
            }
            $customer->user->fill($userInput)->save();

            // Update customer's email and name on stripe system
            if ($customer->isStripeCustomer()) {
                $customer->updateStripeCustomer(['email' => $request->email, 'name' => $request->name]);
            }
            return true;
        });
    }

    public function updateStatus($request, $user)
    {
        DB::transaction(function () use ($request, $user) {
            $user->status = $request->status;
            $user->save();
        });
        return true;
    }
}
