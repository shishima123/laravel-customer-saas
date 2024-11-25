<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
//            'g-recaptcha-response' => 'recaptchav3:login,0.5'
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $redirect = redirect()->intended($this->redirectPath());
        if (!$user->isChangedInfo()) {
            $redirect = redirect()->route('customers.setup-information')
                ->with(['success' => __('message.notify.success.sign_up_success')]);
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : $redirect;
    }
}
