<?php

namespace Tests\Feature;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
//    use RefreshDatabase;

    protected $user;
    protected $userDisabled;
    protected $faker;

    protected const VALID_PASSWORD = '123456789';
    protected const INVALID_PASSWORD = 'invalid';
    protected const INVALID_EMAIL = 'invalidemail@gmailcom';

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        RecaptchaV3::shouldReceive('verify')
            ->andReturn(1.0);

        $this->faker = \Faker\Factory::create();

        $this->user = User::factory()->create([
            'role' => 'user'
        ]);

        $this->userDisabled = User::factory()->create([
            'role' => 'user',
            'status' => ActiveStatus::INACTIVE
        ]);
    }

    public function tearDown(): void
    {
        User::query()->forceDelete();
        parent::tearDown();
    }

    public function loginRoute(): string
    {
        return route('login');
    }

    public function emailRequestGetRoute(): string
    {
        return route('password.request');
    }

    public function registerRoute($params = []): string
    {
        return route('register', $params);
    }

    public function emailRequestPostRoute(): string
    {
        return route('password.email');
    }

    public function resetPasswordGetRoute($token, $email = ''): string
    {
        return route('password.reset', ['token' => $token, 'email' => $email]);
    }

    public function resetPasswordPostRoute(): string
    {
        return route('password.update');
    }

    public function test_tc_pw_1_2_email_validation()
    {
        $dataEmptyEmail = [
            'email' => '',
            'password' => self::VALID_PASSWORD,
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $dataEmptyEmail)
            ->assertRedirect($this->loginRoute())
            ->assertSessionHasErrors('email');

        $dataInvalidEmail = [
            'email' => '123',
            'password' => self::VALID_PASSWORD,
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $dataInvalidEmail)
            ->assertRedirect($this->loginRoute())
            ->assertSessionHasErrors('email');

        $dataEmailNotExists = [
            'email' => 'notexistsemail@gmail.com',
            'password' => self::VALID_PASSWORD,
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $dataEmailNotExists)
            ->assertRedirect($this->loginRoute())
            ->assertSessionHasErrors('email');
    }

    public function test_tc_pw_1_6_password_validation()
    {
        $emailInvalidMin = [
            'email' => $this->user->email,
            'password' => '123',
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $emailInvalidMin)
            ->assertRedirect($this->loginRoute())
            ->assertSessionHasErrors('email');
    }

    public function test_tc_pw_1_8_successful_login()
    {
        $correctData = [
            'email' => $this->user->email,
            'password' => self::VALID_PASSWORD,
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $correctData);

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_tc_pw_1_9_failed_login()
    {
        $correctData = [
            'email' => $this->user->email,
            'password' => self::INVALID_PASSWORD,
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $correctData)
            ->assertRedirect($this->loginRoute())
            ->assertSessionHasErrors('email');;

        $correctData = [
            'email' => self::INVALID_EMAIL,
            'password' => self::VALID_PASSWORD,
            'g-recaptcha-response' => '1',
        ];

        $this->from($this->loginRoute())
            ->post($this->loginRoute(), $correctData)
            ->assertRedirect($this->loginRoute())
            ->assertSessionHasErrors('email');
    }

    public function test_tc_pw_2_1_email_validation()
    {
        $incorrectMailResetData = ['email' => self::INVALID_EMAIL];
        $this->from($this->emailRequestGetRoute())
            ->post($this->emailRequestPostRoute(), $incorrectMailResetData)
            ->assertRedirect($this->emailRequestGetRoute())
            ->assertSessionHasErrors('email');

        $correctMailResetData = ['email' => $this->user->email];
        $this->from($this->emailRequestGetRoute())
            ->post($this->emailRequestPostRoute(), $correctMailResetData);
        Notification::assertSentTo($this->user, ResetPasswordNotification::class);
    }

    public function test_tc_pw_2_2_password_validation()
    {
        $token = Password::broker()->createToken($this->user);

        $password = '123';

        $this
            ->from($this->resetPasswordGetRoute($token))
            ->post($this->resetPasswordPostRoute(), [
                'token' => $token,
                'email' => $this->user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertRedirect($this->resetPasswordGetRoute($token))
            ->assertSessionHasErrors('password');
    }

    public function test_tc_pw_2_3_reset_password_successfully()
    {
        $token = Password::broker()->createToken($this->user);

        $password = Str::random();

        $this
            ->from($this->resetPasswordGetRoute($token))
            ->post($this->resetPasswordPostRoute(), [
                'token' => $token,
                'email' => $this->user->email,
                'password' => $password,
                'password_confirmation' => $password,
                'g-recaptcha-response' => '1',
            ])
            ->assertRedirect($this->loginRoute());

        $this->user->refresh();

        $oldPassword = self::VALID_PASSWORD;
        $this->assertFalse(Hash::check($oldPassword, $this->user->password));
        $this->assertTrue(Hash::check($password, $this->user->password));
    }

    public function test_tc_pw_2_4_reset_password_failed()
    {
        $token = Password::broker()->createToken($this->user);

        $this
            ->from($this->resetPasswordGetRoute($token))
            ->post($this->resetPasswordPostRoute(), [
                'token' => $token,
                'email' => $this->user->email,
                'password' => 'password',
                'password_confirmation' => 'not-same-password',
                'g-recaptcha-response' => '1',
            ])
            ->assertRedirect($this->resetPasswordGetRoute($token))
            ->assertSessionHasErrors('password');
    }

    public function test_tc_pw_2_5_user_not_changed_info_always_redirect_to_set_up_info_page()
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_changed_password' => true
        ]);

        $routes = [
            route('customers.profile'),
            route('payments.index'),
        ];
        foreach ($routes as $route) {
            $this->actingAs($user)
                ->get($route)
                ->assertRedirect(route('customers.setup-information'));
        }
    }

    public function test_user_disabled_cannot_forgot_password()
    {
        $correctMailResetData = ['email' => $this->userDisabled->email];
        $this->from($this->emailRequestGetRoute())
            ->post($this->emailRequestPostRoute(), $correctMailResetData)
            ->assertRedirect($this->emailRequestGetRoute());
    }

    public function test_user_can_register()
    {
        $email = $this->faker->safeEmail;
        $params = [
            'name' => $this->faker->name,
            'email' => $email,
            'company_name' => $this->faker->company,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
        $this->from($this->registerRoute())
            ->post($this->registerRoute($params))
            ->assertRedirect($this->loginRoute());

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_user_can_access_forgot_password_page()
    {
        $token = Password::broker()->createToken($this->user);
        DB::table('password_resets')->insert([
            'email' => $this->user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        $this
            ->get($this->resetPasswordGetRoute($token, $this->user->email))
            ->assertViewIs('auth.passwords.reset');
    }

    public function test_user_cannot_access_forgot_password_page_when_invalid_token()
    {
        $token = Password::broker()->createToken($this->user);
        $this
            ->get($this->resetPasswordGetRoute($token))
            ->assertStatus(419);
    }
}
