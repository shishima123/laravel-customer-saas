<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\City;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\ActiveStatus;

class AdminTest extends TestCase
{
//    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $header;
    protected $faker;
    protected $customer;
    protected $company;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();;

        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $citySeeder = new CitySeeder();
        $citySeeder->run();

        $this->company = Company::factory()->create();

        $this->customer = Customer::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'userable_id' => $this->customer->id,
            'userable_type' => 'App\Models\Customer',
            'is_changed_password' => true,
            'is_changed_info' => true,
        ]);

        $this->header = [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];
    }

    public function tearDown(): void
    {
        User::query()->forceDelete();
        Company::query()->forceDelete();
        Customer::query()->forceDelete();
        Address::query()->forceDelete();
        City::query()->forceDelete();
        parent::tearDown();
    }

    public function userIndexRoute($param = []): string
    {
        return route('customers.index', $param);
    }

    public function customerStoreRoute(): string
    {
        return route('customers.store');
    }

    public function markRemoveRoute($param = []): string
    {
        return route('customers.update-status', $param);
    }

    public function loginRoute(): string
    {
        return route('login');
    }

    public function showCustomerRoute($customer): string
    {
        return route('customers.show', $customer);
    }

    public function updateCustomerRoute($customer): string
    {
        return route('customers.update', $customer);
    }

    public function paymentIndexRoute()
    {
        return route('payments.index');
    }

    public function paymentCreateRoute()
    {
        return route('payments.create');
    }

    public function paymentStoreRoute()
    {
        return route('payments.store');
    }

    public function paymentResumeRoute($customer)
    {
        return route('payments.resume', ['customer' => $customer]);
    }

    public function paymentCancelRoute($customer)
    {
        return route('payments.cancel', ['customer' => $customer]);
    }

    public function invoiceDetailRoute($customer, $invoice)
    {
        return route('customers.invoice.detail', ['customer' => $customer, 'invoice' => $invoice]);
    }

    public function test_tc_pw_3_3_email_validation()
    {
        $dataEmpty = [
            'email' => '',
            'company_name' => '',
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataEmpty)
            ->assertJsonPath('errors.email.0', __('validation.required', ['attribute' => strtolower(__('message.user.email'))]))
            ->assertJsonPath('errors.company_name.0', __('validation.required', ['attribute' => strtolower(__('message.user.company_name'))]))
            ->assertStatus(422);

        $dataExist = [
            'email' => $this->customer->email,
            'company_name' => '123',
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataExist)
            ->assertJsonPath('errors.email.0', __('validation.unique', ['attribute' => strtolower(__('message.user.email'))]))
            ->assertStatus(422);

        $dataEmailWrong = [
            'email' => '123123',
            'company_name' => $this->faker->company,
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataEmailWrong)
            ->assertJsonPath('errors.email.0', __('validation.email', ['attribute' => strtolower(__('message.user.email'))]))
            ->assertStatus(422);

        $dataCorrect = [
            'email' => $this->faker->email,
            'company_name' => $this->faker->company,
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataCorrect)
            ->assertOk();
    }

    public function test_tc_pw_3_4_full_name_validation()
    {
        $dataInCorrect = [
            'email' => $this->faker->email,
            'name' => 123,
            'company_name' => $this->faker->company,
        ];

        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataInCorrect)
            ->assertJsonPath('errors.name.0', __('validation.string', ['attribute' => strtolower(__('message.user.user_name'))]));

        $dataCorrect = [
            'email' => $this->faker->email,
            'name' => '   ' . $this->faker->name . '   ',
            'company_name' => $this->faker->company,
        ];

        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataCorrect)
            ->assertOk();
    }

    public function test_tc_pw_3_5_phone_number_validation()
    {
        $dataInCorrect = [
            'email' => $this->faker->email,
            'phone_number' => '123',
            'company_name' => $this->faker->company,
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataInCorrect)
            ->assertJsonPath('errors.phone_number.0', __('validation.rules.invalid', ['attribute' => strtolower(__('message.user.phone'))]));
    }

    public function test_tc_pw_3_6_company_name_validation()
    {
        $dataCompanyEmpty = [
            'email' => $this->faker->email,
            'company_name' => '',
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataCompanyEmpty)
            ->assertJsonPath('errors.company_name.0', __('validation.required', ['attribute' => strtolower(__('message.user.company_name'))]));

        $dataCorrect = [
            'email' => $this->faker->email,
            'company_name' => '   ' . $this->faker->company . '   ',
        ];

        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataCorrect)
            ->assertOk();
    }

    public function test_tc_pw_3_7_add_user_successfully()
    {
        $dataCorrect = [
            'email' => $this->faker->email,
            'company_name' => $this->faker->company,
        ];
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->post($this->customerStoreRoute(), $dataCorrect)
            ->assertOk();
    }

    public function test_tc_pw_4_2_email_validation()
    {
        $dataEmpty = [
            'email' => '',
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataEmpty)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('email');

        $dataEmailWrong = [
            'email' => '123123',
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataEmailWrong)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('email');

        $email = $this->faker->email;
        $dataCorrect = [
            'email' => $email,
            'add1' => $this->faker->address,
            'company_name' => $this->faker->company,
        ];

        $this
            ->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataCorrect)
            ->assertRedirect($this->showCustomerRoute($this->customer));
        $this->user->refresh();
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_tc_pw_4_3_full_name_validation()
    {

        $dataInvalid = [
            'name' => Str::random(101)
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('name');
    }

    public function test_tc_pw_4_4_phone_number_validation()
    {
        $dataInvalid = [
            'phone_number' => '123'
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('phone_number');
    }

    public function test_tc_pw_4_5_phone_number_validation()
    {
        $dataInvalid = [
            'company_name' => Str::random(256)
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('company_name');
    }

    public function test_tc_pw_4_6_address_validation()
    {
        $dataInvalid = [
            'add1' => Str::random(256)
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('add1');
    }

    public function test_tc_pw_4_7_city_validation()
    {
        $dataInvalid = [
            'city' => 10000
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('city');
    }

    public function test_tc_pw_4_8_province_validation()
    {
        $dataInvalid = [
            'state' => Str::random(200)
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('state');
    }

    public function test_tc_pw_4_9_zip_code_validation()
    {
        $dataInvalid = [
            'zipcode' => 'abc'
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHasErrors('zipcode');
    }

    public function test_tc_pw_4_10_successful_update_user()
    {
        $dataInvalid = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber,
            'company_name' => $this->faker->company,
            'add1' => $this->faker->streetAddress,
            'city' => 1,
            'state' => 'Hokkaido',
            'zipcode' => '012-3456',
            'billing_contact_email' => $this->faker->email,
        ];
        $this->actingAs($this->admin)
            ->from($this->showCustomerRoute($this->customer))
            ->put($this->updateCustomerRoute($this->customer), $dataInvalid)
            ->assertRedirect($this->showCustomerRoute($this->customer))
            ->assertSessionHas('success');
    }

    public function test_tc_pw_5_1_successful_search_user()
    {
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->getJson($this->userIndexRoute(
                [
                    'start' => 0,
                    'length' => 10,
                    'search[value]' => $this->customer->email,
                    'search[regex]' => false,
                    'page' => 1,
                ]
            ))
            ->assertJsonFragment([
                'email' => $this->customer->email,
            ]);
    }

    public function test_tc_pw_5_2_test_when_searching_is_not_successful()
    {
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->getJson($this->userIndexRoute(
                [
                    'start' => 0,
                    'length' => 10,
                    'search[value]' => 'valid_data',
                    'search[regex]' => false,
                    'page' => 1,
                ]
            ))
            ->assertJson(['data' => []]);
    }

    public function test_tc_pw_5_4_test_when_entering_spaces_in_the_prefix_and_suffix_of_the_entered_data()
    {
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->withHeaders($this->header)
            ->getJson($this->userIndexRoute(
                [
                    'start' => 0,
                    'length' => 10,
                    'search[value]' => '    ' . $this->customer->email . '    ',
                    'search[regex]' => false,
                    'page' => 1,
                ]
            ))
            ->assertJsonFragment([
                'email' => $this->customer->email,
            ]);
    }

    public function test_tc_pw_6_1_turn_on_mark_removed()
    {
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->post($this->markRemoveRoute(
                ['user' => $this->user->id]
            ), [
                'status' => ActiveStatus::INACTIVE->value
            ])->assertOk();

        $this->assertDatabaseHas('users', [
            'email' => $this->user->email,
            'status' => ActiveStatus::INACTIVE->value
        ]);

        $this->user->refresh();
        Auth::logout();

        $this->followingRedirects()
            ->from($this->loginRoute())
            ->post($this->loginRoute(), [
                'email' => $this->user->email,
                'password' => '123456789',
            ])
            ->assertSeeText(__('message.notify.error.account_disabled'));
    }

    public function test_tc_pw_6_2_turn_off_mark_removed()
    {
        RecaptchaV3::shouldReceive('verify')
            ->andReturn(1.0);

        $userDisabled = User::factory()->create(
            [
                'role' => 'user',
                'status' => ActiveStatus::INACTIVE
            ]
        );
        $this->actingAs($this->admin)
            ->from($this->userIndexRoute())
            ->post($this->markRemoveRoute(
                ['user' => $userDisabled->id]
            ), [
                'status' => ActiveStatus::ACTIVE->value
            ])->assertOk();

        $this->assertDatabaseHas('users', [
            'email' => $userDisabled->email,
            'status' => ActiveStatus::ACTIVE->value
        ]);

        Auth::logout();

        $this->followingRedirects()
            ->from($this->loginRoute())
            ->post($this->loginRoute(), [
                'email' => $userDisabled->email,
                'password' => '123456789',
                'g-recaptcha-response' => '1',
            ]);
        $this->assertAuthenticatedAs($userDisabled);
    }

    public function test_tc_pw_6_3_successful_cancel_user_subscription()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->header)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();

        $this
            ->actingAs($this->user)
            ->from($this->updateCustomerRoute($this->customer))
            ->withHeaders($this->header)
            ->post($this->paymentCancelRoute($this->customer->id))
            ->assertOk();
    }
}
