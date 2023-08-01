<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CurrentAuthenticatedCustomer
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     * Customers only see their resources
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $customer = $request->customer;

        if ($customer == 'profile' || $request->route()->named(['customers.profile'])) {
            $customer = $user->userable;
        } elseif (Str::isUuid($customer)) {
            $customer = Customer::findOrFail($customer);
        }
        if ($user->isUser() && $customer->isNot($user->userable)) {
            // Response for Api
            if ($request->wantsJson()) {
                return $this->errorResponse(__('message.notify.error.forbidden'));
            }

            return redirect()->route('customers.profile');
        }

        return $next($request);
    }
}
