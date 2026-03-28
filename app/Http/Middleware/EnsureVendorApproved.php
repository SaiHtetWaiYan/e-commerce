<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isVendor()) {
            abort(403);
        }

        if (! $user->vendorProfile?->is_verified) {
            abort(403, 'Your vendor account is not approved yet.');
        }

        return $next($request);
    }
}
