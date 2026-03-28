<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $userStatus = $user->status instanceof UserStatus
            ? $user->status
            : UserStatus::tryFrom((string) $user->status);

        if ($userStatus !== UserStatus::Active) {
            abort(403, 'Your account is not active.');
        }

        if (! in_array($user->role?->value ?? (string) $user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
