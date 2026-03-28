<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->applyMarketplaceSettings();
    }

    public function boot(): void
    {
        View::composer('components.storefront.navbar', function ($view) {
            $navCategories = collect();
            if (Schema::hasTable('categories')) {
                $navCategories = Cache::remember('nav_categories', 3600, function () {
                    return Category::query()
                        ->active()
                        ->whereNull('parent_id')
                        ->with(['children' => fn ($query) => $query->active()->orderBy('sort_order')])
                        ->orderBy('sort_order')
                        ->limit(10)
                        ->get();
                });
            }

            $view->with('navCategories', $navCategories);
        });
    }

    protected function applyMarketplaceSettings(): void
    {
        try {
            if (! Schema::hasTable('app_settings')) {
                return;
            }

            config()->set(AppSetting::marketplaceOverrides());
        } catch (\Throwable) {
            //
        }
    }
}
