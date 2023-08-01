<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class Role
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = auth()->user();
        if ($user->hasRole($role)) {
            return $next($request);
        }

        // Response for Api
        if ($request->wantsJson()) {
            return $this->errorResponse(__('message.notify.error.forbidden'));
        }

        if ($user->isAdmin()) {
            return redirect()->route('customers.index');
        }

        return redirect()->route('customers.profile');
    }
}
