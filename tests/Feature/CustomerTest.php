<?php

namespace Tests\Feature;

use App\Enums\ActiveStatus;
use App\Enums\PlanType;
use App\Models\Address;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\CustomerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Cashier\SubscriptionItem;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
//    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $otherUser;
    protected $userFirstLogin;
    protected $ajaxHeader;
    protected $browserHeader;
    protected $faker;
    protected $customer;
    protected $otherCustomer;
    protected $customerFirstLogin;
    protected $company;
    protected const VALID_PASSWORD = '123456789';

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->company = Company::factory()->create();

        $this->customer = Customer::factory()->create([
            'company_id' => $this->company->id
        ]);

        $this->otherCustomer = Customer::factory()->create([
            'company_id' => $this->company->id
        ]);

        $this->customerFirstLogin = Customer::factory()->create([
            'company_id' => $this->company->id
        ]);

        $this->userFirstLogin = User::factory()->create([
            'role' => 'user',
            'userable_id' => $this->customer->id,
            'userable_type' => 'App\Models\Customer',
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'userable_id' => $this->customer->id,
            'userable_type' => 'App\Models\Customer',
            'is_changed_password' => true,
            'is_changed_info' => true,
        ]);

        $this->otherUser = User::factory()->create([
            'role' => 'user',
            'userable_id' => $this->otherCustomer->id,
            'userable_type' => 'App\Models\Customer',
            'is_changed_password' => true,
            'is_changed_info' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->ajaxHeader = [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->browserHeader = [
            'X-Requested-With' => '',
            'Accept' => '*/*',
        ];
    }

    public function tearDown(): void
    {
        User::query()->forceDelete();
        Company::query()->forceDelete();
        Customer::query()->forceDelete();
        Address::query()->forceDelete();
        Subscription::query()->forceDelete();
        SubscriptionItem::query()->forceDelete();
        parent::tearDown();
    }

    public function firstLoginGetRoute(): string
    {
        return route('first-login-get');
    }

    public function firstLoginPostRoute(): string
    {
        return route('first-login-post');
    }

    public function customerIndexRoute($param = [])
    {
        return route('customers.index', $param);
    }

    public function paymentIndexRoute()
    {
        return route('payments.index');
    }

    public function paymentCreateRoute($plan = null)
    {
        return route('payments.create', $plan);
    }

    public function paymentStoreRoute()
    {
        return route('payments.store');
    }

    public function setupInformationGetRoute()
    {
        return route('customers.setup-information');
    }

    public function setupInformationPutRoute($params)
    {
        return route('customers.setup-information-put', $params);
    }

    public function showCustomerRoute($customer): string
    {
        return route('customers.show', $customer);
    }

    public function showCustomerProfileRoute(): string
    {
        return route('customers.profile');
    }

    public function customerStoreRoute(): string
    {
        return route('customers.store');
    }

    public function updateCustomerRoute($customer): string
    {
        return route('customers.update', $customer);
    }

    public function markRemoveRoute($param = []): string
    {
        return route('customers.update-status', $param);
    }

    public function loginRoute(): string
    {
        return route('login');
    }

    public function accountGetRoute($customer): string
    {
        return route('customers.account', $customer);
    }

    public function paymentHistoryRoute($customer, $params = []): string
    {
        return route('customers.payment-history', array_merge(['customer' => $customer->id], $params));
    }

    public function unsubscriptionPlanGetRoute(): string
    {
        return route('customers.unsubscription-plan-get');
    }

    public function paymentResumeRoute($customer)
    {
        return route('payments.resume', ['customer' => $customer]);
    }

    public function mockThrowException($class, $method)
    {
        return $this->mock($class, function (MockInterface $mock) use ($method) {
            $mock->shouldReceive($method)->andThrow(Exception::class);
        });
    }

    public function test_tc_pw_7_2_new_password_validation()
    {
        $password = '';

        $this->actingAs($this->userFirstLogin)
            ->from($this->firstLoginGetRoute())
            ->post($this->firstLoginPostRoute(), [
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_tc_pw_7_3_confirm_password_validation()
    {
        $password = Str::random(8);
        $passwordConfirmation = '';

        $this->actingAs($this->userFirstLogin)
            ->from($this->firstLoginGetRoute())
            ->post($this->firstLoginPostRoute(), [
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ])
            ->assertSessionHasErrors('password_confirmation');
    }

    public function test_tc_pw_7_4_successful_create_password()
    {
        $password = Str::random(8);

        $this
            ->followingRedirects()
            ->actingAs($this->userFirstLogin)
            ->from($this->firstLoginGetRoute())
            ->post($this->firstLoginPostRoute(), [
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSuccessful();

        $this->userFirstLogin->refresh();

        $oldPassword = self::VALID_PASSWORD;
        $this->assertFalse(Hash::check($oldPassword, $this->userFirstLogin->password));
        $this->assertTrue(Hash::check($password, $this->userFirstLogin->password));
    }

    public function test_tc_pw_7_5_create_password_failed()
    {
        $password = Str::random(8);
        $passwordConfirmation = Str::random(8);

        $this->actingAs($this->userFirstLogin)
            ->from($this->firstLoginGetRoute())
            ->post($this->firstLoginPostRoute(), [
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ])
            ->assertSessionHasErrors('password_confirmation');
    }

    public function test_tc_pw_9_1_successful_subscribe_free()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentIndexRoute())
            ->get($this->paymentCreateRoute(), [
                'plan' => PlanType::FREE->value
            ])
            ->assertRedirect($this->customerIndexRoute());
    }

    public function test_tc_pw_9_2_successful_subscribe_premium()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
    }

    public function test_tc_pw_9_3_subscribe_premium_failed()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'invalid_payment'
            ])
            ->assertStatus(400);
    }

    public function test_user_cannot_access_page_first_login_when_password_has_changed()
    {
        $this
            ->actingAs($this->user)
            ->get($this->firstLoginGetRoute())
            ->assertRedirect($this->customerIndexRoute());
    }

    public function test_admin_cannot_access_page_first_login()
    {
        $this
            ->actingAs($this->admin)
            ->get($this->firstLoginGetRoute())
            ->assertRedirect($this->customerIndexRoute());
    }

    public function test_redirect_back_when_update_password_first_login_fail()
    {
        $password = Str::random(8);
        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('updatePasswordFirstLogin')->once()->andReturnFalse();
        });
        $this
            ->actingAs($this->userFirstLogin)
            ->from($this->firstLoginGetRoute())
            ->post($this->firstLoginPostRoute(),
                [
                    'password' => $password,
                    'password_confirmation' => $password,
                ])
            ->assertRedirect($this->firstLoginGetRoute());
    }

    public function test_user_can_access_setup_information_page()
    {
        $this
            ->actingAs($this->user)
            ->get($this->setupInformationGetRoute())
            ->assertViewIs('customer.setup_information');
    }

    public function test_admin_cannot_access_setup_information_page()
    {
        $this
            ->actingAs($this->admin)
            ->get($this->setupInformationGetRoute())
            ->assertRedirect($this->customerIndexRoute());
    }

    public function test_user_can_setup_information()
    {
        $dataInvalid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];

        $this
            ->actingAs($this->user)
            ->from($this->setupInformationGetRoute())
            ->put($this->setupInformationPutRoute($dataInvalid))
            ->assertRedirect($this->paymentIndexRoute());
    }

    public function test_user_cannot_setup_information_when_throw_exception()
    {
        $dataInvalid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];

        $this->mock(CustomerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateCustomer')->once()->andThrow(Exception::class);
        });

        $this
            ->actingAs($this->user)
            ->from($this->setupInformationGetRoute())
            ->put($this->setupInformationPutRoute($dataInvalid))
            ->assertRedirect($this->setupInformationGetRoute());
    }

    public function test_admin_can_access_customer_index_page()
    {
        $this
            ->actingAs($this->admin)
            ->get($this->customerIndexRoute())
            ->assertViewIs('customer.index');
    }

    public function test_user_redirect_profile_with_message_when_first_login()
    {
        $this
            ->actingAs($this->user)
            ->withSession(['is_changed_password' => true])
            ->get($this->customerIndexRoute())
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_user_redirect_to_profile_when_access_customer_index_page()
    {
        $this
            ->actingAs($this->user)
            ->get($this->customerIndexRoute())
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_admin_can_show_customer()
    {
        $this
            ->actingAs($this->admin)
            ->from($this->customerIndexRoute())
            ->get($this->showCustomerRoute($this->customer))
            ->assertViewIs('customer.update');
    }

    public function test_user_can_see_his_customer_information_profile_page()
    {
        $this
            ->actingAs($this->user)
            ->get($this->showCustomerProfileRoute())
            ->assertViewIs('customer.update');
    }

    public function test_admin_redirect_when_access_profile_page()
    {
        $this
            ->actingAs($this->admin)
            ->get($this->showCustomerProfileRoute())
            ->assertRedirect($this->customerIndexRoute());
    }

    public function test_admin_cannot_create_customer_when_throw_exception()
    {
        $this->mockThrowException(CustomerService::class, 'createCustomer');

        $dataCorrect = [
            'email' => $this->faker->email,
            'company_name' => $this->faker->company,
        ];
        $this->actingAs($this->admin)
            ->from($this->customerIndexRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->customerStoreRoute(), $dataCorrect)
            ->assertStatus(400);
    }

    public function test_user_cannot_update_other_user_profile()
    {
        $dataValid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];
        $this->actingAs($this->user)
            ->put($this->updateCustomerRoute($this->otherCustomer), $dataValid)
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_user_can_update_his_profile()
    {
        $dataValid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];
        $this->actingAs($this->user)
            ->put($this->updateCustomerRoute($this->customer), $dataValid)
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_admin_cannot_update_customer_profile_when_throw_exception()
    {
        $dataValid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];

        $this->mockThrowException(CustomerService::class, 'updateCustomer');
        $this->actingAs($this->admin)
            ->put($this->updateCustomerRoute($this->customer), $dataValid)
            ->assertRedirect($this->showCustomerRoute($this->customer));
    }

    public function test_user_cannot_update_his_profile_when_throw_exception()
    {
        $dataValid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];

        $this->mockThrowException(CustomerService::class, 'updateCustomer');
        $this->actingAs($this->user)
            ->put($this->updateCustomerRoute($this->customer), $dataValid)
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_admin_cannot_update_customer_status_when_throw_exception()
    {
        $this->mockThrowException(CustomerService::class, 'updateStatus');

        $this->actingAs($this->admin)
            ->from($this->customerIndexRoute())
            ->post($this->markRemoveRoute(
                ['user' => $this->user->id]
            ), [
                'status' => ActiveStatus::INACTIVE->value
            ])->assertStatus(400);
    }

    public function test_user_and_admin_can_access_customer_account_page()
    {
        $this->actingAs($this->admin)
            ->get($this->accountGetRoute($this->customer))
            ->assertViewIs('customer.update');

        $this->actingAs($this->user)
            ->get($this->accountGetRoute($this->customer))
            ->assertViewIs('customer.update');
    }

    public function test_user_cannot_access_other_customer_account()
    {
        $this->actingAs($this->user)
            ->get($this->accountGetRoute($this->otherCustomer))
            ->assertRedirect(route('customers.profile'));
    }

    public function test_user_and_admin_cannot_change_password_of_customer()
    {
        $data = [
            'current_password' => '123456789',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
        $this->actingAs($this->user)
            ->from($this->accountGetRoute($this->customer))
            ->post($this->accountGetRoute($this->customer), $data)
            ->assertRedirect($this->accountGetRoute($this->customer));

        $this->actingAs($this->admin)
            ->from($this->accountGetRoute($this->customer))
            ->post($this->accountGetRoute($this->customer), $data)
            ->assertRedirect($this->accountGetRoute($this->customer));
    }

    public function test_user_and_admin_cannot_change_password_of_customer_when_throw_exception()
    {
        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('updatePassword')->once()->andReturnFalse();
        });

        $data = [
            'current_password' => '123456789',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
        $this->actingAs($this->user)
            ->from($this->accountGetRoute($this->customer))
            ->post($this->accountGetRoute($this->customer), $data)
            ->assertRedirect($this->accountGetRoute($this->customer));
    }

    public function test_user_cannot_change_password_of_other_customer()
    {
        $data = [
            'current_password' => '123456789',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
        $this->actingAs($this->user)
            ->post($this->accountGetRoute($this->otherCustomer), $data)
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_admin_and_user_can_access_payment_history_page()
    {
        $params = [
            'start' => 0,
            'length' => 10,
            'page' => 1,
        ];
        $this
            ->actingAs($this->user)
            ->get($this->paymentHistoryRoute($this->customer, $params))
            ->assertViewIs('customer.update');

        $this
            ->actingAs($this->admin)
            ->withHeaders($this->ajaxHeader)
            ->get($this->paymentHistoryRoute($this->customer, $params))
            ->assertOk();
    }

    public function test_user_cannot_access_other_user_payment_history_page_ajax()
    {
        $this
            ->actingAs($this->user)
            ->withHeaders($this->ajaxHeader)
            ->get($this->paymentHistoryRoute($this->otherCustomer))
            ->assertStatus(400);
    }

    public function test_user_cannot_access_other_user_payment_history_page_browser_request()
    {
        $this
            ->actingAs($this->user)
            ->get($this->paymentHistoryRoute($this->otherCustomer))
            ->assertRedirect($this->showCustomerProfileRoute());
    }

    public function test_user_can_access_unsubscription_plan_page()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();

        $user = User::find($this->user->id);
        $this
            ->actingAs($user)
            ->get($this->unsubscriptionPlanGetRoute())
            ->assertViewIs('customer.unsubscription_plan');
    }

    public function test_admin_or_user_free_or_user_on_grace_period_cannot_access_unsubscription_plan_page()
    {
        $this
            ->actingAs($this->admin)
            ->get($this->unsubscriptionPlanGetRoute())
            ->assertRedirect($this->customerIndexRoute());

        $this
            ->actingAs($this->user)
            ->get($this->unsubscriptionPlanGetRoute())
            ->assertRedirect($this->customerIndexRoute());

        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();

        $user = User::find($this->user->id);

        $this
            ->actingAs($user)
            ->from($this->showCustomerRoute($this->customer->id))
            ->post($this->paymentResumeRoute($this->customer->id));

        $subscription = $this->customer->subscriptions->first();
        $subscription->ends_at = Carbon::now()->addMonth();
        $subscription->save();
        $subscription->fresh();

        $user = User::find($this->user->id);
        $this
            ->actingAs($user)
            ->withHeaders($this->browserHeader)
            ->get($this->unsubscriptionPlanGetRoute())
            ->assertRedirect($this->customerIndexRoute());
    }
}
