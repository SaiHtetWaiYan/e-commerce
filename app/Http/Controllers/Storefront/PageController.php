<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function termsOfService(): View
    {
        return view('storefront.pages.terms');
    }

    public function privacyPolicy(): View
    {
        return view('storefront.pages.privacy');
    }
}
