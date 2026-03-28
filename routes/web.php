<?php

use App\Enums\UserRole;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CampaignController as StorefrontCampaignController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\SearchController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\VendorStoreController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', function (LoginRequest $request): RedirectResponse {
        $credentials = $request->safe()->only(['email', 'password']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $defaultRoute = match (Auth::user()->role) {
                UserRole::Admin => route('admin.dashboard'),
                UserRole::Vendor => route('vendor.dashboard'),
                UserRole::DeliveryAgent => route('delivery.dashboard'),
                default => route('storefront.home'),
            };

            return redirect()->intended($defaultRoute);
        }

        return back()
            ->withErrors(['email' => 'The provided credentials are incorrect.'])
            ->onlyInput('email');
    })->middleware('throttle:auth-login')->name('login.attempt');

    Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])->name('auth.social.callback');

    Route::get('/register', [RegisterController::class, 'showCustomerForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'registerCustomer'])->name('register.store');
    Route::get('/vendor/register', [RegisterController::class, 'showVendorForm'])->name('vendor.register');
    Route::post('/vendor/register', [RegisterController::class, 'registerVendor'])->name('vendor.register.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->middleware('throttle:auth-password')->name('password.email');
    Route::get('/reset-password', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', function (Request $request): RedirectResponse {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Email Verification
Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request): RedirectResponse {
        $request->fulfill();

        return redirect()->route('storefront.home')->with('status', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request): RedirectResponse {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware('storefront')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('storefront.home');
    Route::get('/campaigns', [StorefrontCampaignController::class, 'index'])->name('storefront.campaigns.index');
    Route::get('/campaigns/{campaign:slug}', [StorefrontCampaignController::class, 'show'])->name('storefront.campaigns.show');

    Route::get('/products', [ProductController::class, 'index'])->name('storefront.products.index');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('storefront.products.show');
    Route::get('/store/{slug}', [VendorStoreController::class, 'show'])->name('storefront.vendor.show');

    Route::get('/shop', function (Request $request): RedirectResponse {
        return redirect()->route('storefront.products.index', $request->query(), 301);
    });
    Route::get('/products/{slug}', function (Request $request, string $slug): RedirectResponse {
        return redirect()->route('storefront.products.show', array_merge(['slug' => $slug], $request->query()), 301);
    });
    Route::get('/shop/{slug}', function (Request $request, string $slug): RedirectResponse {
        return redirect()->route('storefront.vendor.show', array_merge(['slug' => $slug], $request->query()), 301);
    });
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('storefront.categories.show');
    Route::get('/search', [SearchController::class, 'index'])->name('storefront.search.index');

    // Static Pages
    Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('storefront.pages.terms');
    Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('storefront.pages.privacy');

    Route::prefix('cart')->name('storefront.cart.')->group(function (): void {
        Route::get('/', [CartController::class, 'index'])->name('index');

        Route::middleware(['auth', 'role:customer'])->group(function (): void {
            Route::post('/items', [CartController::class, 'add'])->name('add');
            Route::patch('/items/{item}', [CartController::class, 'update'])->name('update');
            Route::delete('/items/{item}', [CartController::class, 'destroy'])->name('destroy');
            Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon');
        });
    });

    Route::middleware(['auth', 'role:customer'])->prefix('checkout')->name('storefront.checkout.')->group(function (): void {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->middleware('throttle:checkout-submit')->name('store');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    });
});
