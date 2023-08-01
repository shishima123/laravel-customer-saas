<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivated
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
        $user = Auth::user();
        $response = $next($request);
        if ($user) {
            if ($user->isAdmin() || $user->isActive()) {
                return $response;
            }

            // Response for Api
            if ($request->wantsJson()) {
                $user->tokens()->delete();
                return $this->errorResponse(__('message.notify.error.account_disabled'));
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', __('message.notify.error.account_disabled'));
        }
        return $response;
    }
}
