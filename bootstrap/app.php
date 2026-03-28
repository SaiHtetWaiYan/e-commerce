<?php

use App\Http\Middleware\CartSessionMerge;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureVendorApproved;
use App\Http\Middleware\RedirectNonCustomers;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/customer.php'));
            Route::middleware('web')->group(base_path('routes/vendor.php'));
            Route::middleware('web')->group(base_path('routes/admin.php'));
            Route::middleware('web')->group(base_path('routes/delivery.php'));
            Route::middleware('web')->prefix('api')->name('api.')->group(base_path('routes/api.php'));

            RateLimiter::for('auth-login', function (Request $request): Limit {
                $email = (string) $request->input('email');

                return Limit::perMinute(5)->by($email.$request->ip());
            });

            RateLimiter::for('auth-password', function (Request $request): Limit {
                return Limit::perMinute(3)->by($request->ip());
            });

            RateLimiter::for('api-cart', function (Request $request): Limit {
                return Limit::perMinute(60)->by((string) ($request->user()?->id ?? $request->ip()));
            });

            RateLimiter::for('api-search', function (Request $request): Limit {
                return Limit::perMinute(120)->by((string) $request->ip());
            });

            RateLimiter::for('api-wishlist', function (Request $request): Limit {
                return Limit::perMinute(30)->by((string) ($request->user()?->id ?? $request->ip()));
            });

            RateLimiter::for('checkout-submit', function (Request $request): Limit {
                return Limit::perMinute(10)->by((string) ($request->user()?->id ?? $request->ip()));
            });

            RateLimiter::for('payment-webhook', function (Request $request): Limit {
                return Limit::perMinute(120)->by((string) $request->ip());
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/payments/webhook',
            'api/stripe/webhook',
        ]);

        $middleware->alias([
            'role' => EnsureRole::class,
            'vendor.approved' => EnsureVendorApproved::class,
            'cart.merge' => CartSessionMerge::class,
            'storefront' => RedirectNonCustomers::class,
        ]);

        $middleware->appendToGroup('web', [
            CartSessionMerge::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
