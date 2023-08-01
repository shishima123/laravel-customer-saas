<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PaymentService;
use Exception;
use Laravel\Cashier\SubscriptionItem;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentTest extends TestCase
{
//    use RefreshDatabase;
    protected const VALID_PASSWORD = '123456789';
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

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
        $this->company = Company::factory()->create();
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
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

    public function firstLoginGet(): string
    {
        return route('first-login-get');
    }

    public function firstLoginPost(): string
    {
        return route('first-login-post');
    }

    public function languageRoute($language)
    {
        return route('language', ['lang' => $language]);
    }

    public function test_admin_cannot_access_index_create_store_resume_method()
    {
        $this->actingAs($this->admin)
            ->get($this->paymentIndexRoute())
            ->assertRedirect($this->userIndexRoute());
        $this->actingAs($this->admin)
            ->get($this->paymentCreateRoute())
            ->assertRedirect($this->userIndexRoute());
        $this->actingAs($this->admin)
            ->get($this->paymentStoreRoute())
            ->assertRedirect($this->userIndexRoute());
        $this->actingAs($this->admin)
            ->post($this->paymentResumeRoute($this->customer))
            ->assertRedirect($this->userIndexRoute());
        $this->actingAs($this->admin)
            ->withHeaders($this->ajaxHeader)
            ->get($this->paymentCreateRoute())
            ->assertStatus(400);
    }

    public function paymentIndexRoute()
    {
        return route('payments.index');
    }

    public function userIndexRoute($param = []): string
    {
        return route('customers.index', $param);
    }

    public function paymentCreateRoute($plan = null)
    {
        return route('payments.create', $plan);
    }

    public function paymentStoreRoute()
    {
        return route('payments.store');
    }

    public function paymentResumeRoute($customer)
    {
        return route('payments.resume', ['customer' => $customer]);
    }

    public function customerRoute()
    {
        return route('customers.index');
    }

    public function paymentResumeNewCardRoute()
    {
        return route('payments.resume-with-new-card');
    }

    public function paymentCancelRoute($customer, array $params = [])
    {
        return route('payments.cancel', array_merge(['customer' => $customer], $params));
    }

    public function showCustomerRoute($customer): string
    {
        return route('customers.show', $customer);
    }

    public function getInvoiceDetailRoute($customer, $invoice)
    {
        return route('customers.invoice.detail', ['customer' => $customer, 'invoice' => $invoice]);
    }

    public function test_user_can_access_payment_index()
    {
        $this
            ->actingAs($this->user)
            ->get($this->paymentIndexRoute())
            ->assertViewIs('payment.index');
    }

    public function test_user_can_access_payment_create()
    {
        $this
            ->actingAs($this->user)
            ->get($this->paymentCreateRoute(['plan' => 'premium']))
            ->assertViewIs('payment.create');
    }

    public function test_user_can_access_resume_new_card_form()
    {
        $this
            ->actingAs($this->user)
            ->get($this->paymentResumeNewCardRoute())
            ->assertViewIs('payment.resume');
    }

    public function invoiceDownloadRoute($customer, $invoice)
    {
        return route('customers.invoice.download', ['customer' => $customer, 'invoice' => $invoice]);
    }

    public function test_user_free_cannot_cancel()
    {
        $this
            ->actingAs($this->user)
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentCancelRoute($this->customer->id))
            ->assertStatus(400);
    }

    public function test_user_free_cannot_cancel_redirect()
    {
        $this
            ->actingAs($this->user)
            ->from($this->showCustomerRoute($this->customer))
            ->post($this->paymentCancelRoute($this->customer->id))
            ->assertRedirect($this->showCustomerRoute($this->customer));
    }

    public function test_user_cannot_cancel_other_user_subscription()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $this
            ->actingAs($this->otherUser)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $this
            ->actingAs($this->user)
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentCancelRoute($this->otherCustomer->id))
            ->assertStatus(400);
    }

    public function test_user_can_cancel_subscription()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $this
            ->actingAs($this->user)
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentCancelRoute($this->customer->id))
            ->assertOk();
    }

    public function test_user_can_cancel_subscription_has_incomplete_payment()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $subscription = $this->customer->subscriptions->first();
        $subscription->stripe_status = 'past_due';
        $subscription->save();
        $subscription->fresh();
        $this
            ->actingAs($this->user)
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentCancelRoute($this->customer->id))
            ->assertOk();
    }

    public function test_user_will_redirect_to_profile_when_cancel_subscription()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $this
            ->actingAs($this->user)
            ->withHeaders($this->browserHeader)
            ->post($this->paymentCancelRoute($this->customer->id, ['redirect_to_profile' => true]))
            ->assertRedirect(route('customers.show', $this->customer));
    }

    public function test_user_will_redirect_back_when_cancel_subscription()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $this
            ->actingAs($this->user)
            ->from(route('customers.show', $this->customer))
            ->withHeaders($this->browserHeader)
            ->post($this->paymentCancelRoute($this->customer->id))
            ->assertRedirect(route('customers.show', $this->customer));
    }

    public function test_user_can_get_invoice_detail()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentCreateRoute())
            ->withHeaders($this->ajaxHeader)
            ->post($this->paymentStoreRoute(), [
                'token' => 'pm_card_visa'
            ])
            ->assertOk();
        $customer = Customer::find($this->customer->id);
        $invoice = $customer->invoices()->first();
        $this->get($this->getInvoiceDetailRoute($customer->id, $invoice->id))
            ->assertOk();
    }

    public function test_user_cannot_get_other_user_invoice_detail()
    {
        $this->actingAs($this->otherUser)
            ->get($this->getInvoiceDetailRoute($this->customer->id, 'invoice_id'))
            ->assertRedirect(route('customers.profile'));
    }

    public function test_user_cannot_download_other_user_invoice()
    {
        $this->actingAs($this->otherUser)
            ->get($this->invoiceDownloadRoute($this->customer->id, 'invoice_id'))
            ->assertRedirect(route('customers.profile'));
    }

    public function test_user_cannot_access_checkout_page_if_missing_params()
    {
        $this
            ->actingAs($this->user)
            ->from($this->paymentIndexRoute())
            ->get($this->paymentCreateRoute(), [
                'plan' => ''
            ])
            ->assertRedirect($this->customerRoute());
    }

//    public function test_user_can_download_invoice()
//    {
//        $this
//            ->actingAs($this->user)
//            ->from($this->paymentCreateRoute())
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentStoreRoute(), [
//                'token' => 'pm_card_visa'
//            ])
//            ->assertOk();
//        $customer = Customer::find($this->customer->id);
//        $invoice = $customer->invoices()->first();
//        $this->get($this->invoiceDownloadRoute($customer->id, $invoice->id))
//            ->assertOk();
//    }
//    public function test_user_cannot_checkout_when_throw_exception()
//    {
//        $this->mock(PaymentService::class, function (MockInterface $mock) {
//            $mock->shouldReceive('storeSubscription')->once()->andThrow(Exception::class);
//        });
//        $this
//            ->actingAs($this->user)
//            ->from($this->paymentCreateRoute())
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentStoreRoute(), [
//                'token' => 'pm_card_visa'
//            ])
//            ->assertStatus(400);
//    }
//
//    public function test_user_can_resume_subscription_ajax()
//    {
//        $this
//            ->actingAs($this->user)
//            ->from($this->paymentCreateRoute())
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentStoreRoute(), [
//                'token' => 'pm_card_visa'
//            ])
//            ->assertOk();
//        $this
//            ->actingAs($this->user)
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentCancelRoute($this->customer->id))
//            ->assertOk();
//        $user = User::find($this->user->id);
//        $this
//            ->actingAs($user)
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentResumeRoute($this->customer->id))
//            ->assertOk();
//    }
//
//    public function test_user_can_resume_subscription_browser_request()
//    {
//        $this
//            ->actingAs($this->user)
//            ->from($this->paymentCreateRoute())
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentStoreRoute(), [
//                'token' => 'pm_card_visa'
//            ])
//            ->assertOk();
//        $this
//            ->actingAs($this->user)
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentCancelRoute($this->customer->id))
//            ->assertOk();
//        $user = User::find($this->user->id);
//        $this
//            ->actingAs($user)
//            ->from($this->showCustomerRoute($this->customer->id))
//            ->withHeaders($this->browserHeader)
//            ->post($this->paymentResumeRoute($this->customer->id))
//            ->assertRedirect($this->showCustomerRoute($this->customer->id));
//    }
//
//    public function test_user_can_resume_subscription_throw_exception_ajax()
//    {
//        $this
//            ->actingAs($this->user)
//            ->from($this->paymentCreateRoute())
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentStoreRoute(), [
//                'token' => 'pm_card_visa'
//            ])
//            ->assertOk();
//        $this
//            ->actingAs($this->user)
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentCancelRoute($this->customer->id))
//            ->assertOk();
//        $this->mock(PaymentService::class, function (MockInterface $mock) {
//            $mock->shouldReceive('resumeSubscription')->once()->andThrow(Exception::class);
//        });
//        $user = User::find($this->user->id);
//        $this
//            ->actingAs($user)
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentResumeRoute($this->customer->id))
//            ->assertStatus(400);
//    }
//
//    public function test_user_can_resume_subscription_throw_exception_browser_request()
//    {
//        $this
//            ->actingAs($this->user)
//            ->from($this->paymentCreateRoute())
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentStoreRoute(), [
//                'token' => 'pm_card_visa'
//            ])
//            ->assertOk();
//        $this
//            ->actingAs($this->user)
//            ->withHeaders($this->ajaxHeader)
//            ->post($this->paymentCancelRoute($this->customer->id))
//            ->assertOk();
//        $this->mock(PaymentService::class, function (MockInterface $mock) {
//            $mock->shouldReceive('resumeSubscription')->once()->andThrow(Exception::class);
//        });
//        $user = User::find($this->user->id);
//        $this
//            ->actingAs($user)
//            ->from($this->showCustomerRoute($this->customer->id))
//            ->withHeaders($this->browserHeader)
//            ->post($this->paymentResumeRoute($this->customer->id))
//            ->assertRedirect($this->showCustomerRoute($this->customer->id));
//    }
}
