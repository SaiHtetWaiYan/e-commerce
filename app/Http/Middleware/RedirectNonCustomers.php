<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonCustomers
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $dashboard = match ($user->role) {
            UserRole::Admin => route('admin.dashboard'),
            UserRole::Vendor => route('vendor.dashboard'),
            UserRole::DeliveryAgent => route('delivery.dashboard'),
            default => null,
        };

        if ($dashboard !== null) {
            return redirect($dashboard);
        }

        return $next($request);
    }
}
