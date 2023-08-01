<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class CheckUserChangedPassword
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isFirstLogin()) {
            // Response for Api
            if ($request->wantsJson()) {
                return $this->errorResponse(__('message.notify.error.account_change_password_first'));
            }

            return redirect()->route('first-login-get');
        }

        return $next($request);
    }
}
