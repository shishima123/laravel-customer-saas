<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery\MockInterface;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{
//    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function languageRoute($language)
    {
        return route('language', ['lang' => $language]);
    }

    public function loginRoute()
    {
        return route('login');
    }

    public function test_user_can_change_language()
    {
        $this
            ->get($this->languageRoute('en'))
            ->assertCookie('locale', 'en');

        $this
            ->get($this->languageRoute('ja'))
            ->assertCookie('locale', 'ja');
    }

    public function test_user_cannot_change_language_if_language_not_correct()
    {
        $this
            ->get($this->languageRoute('bz'))
            ->assertStatus(400);
    }

    public function test_language_has_changed_if_cookie_has_locale()
    {
        $this
            ->withCookies([
                'locale' => 'ja',
            ])
            ->get($this->loginRoute());
        $this->assertEquals('ja', $this->app->translator->getLocale());
    }
}
