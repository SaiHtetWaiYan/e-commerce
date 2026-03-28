<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Models\Brand;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.brands.index', [
            'brands' => $brands,
        ]);
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $requestedSlug = trim((string) ($validated['slug'] ?? ''));
        $baseSlug = Str::slug($requestedSlug !== '' ? $requestedSlug : (string) $validated['name']);
        $slug = $this->generateUniqueSlug($baseSlug);

        Brand::query()->create([
            'name' => (string) $validated['name'],
            'slug' => $slug,
            'logo' => $validated['logo'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return to_route('admin.brands.index')->with('status', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', [
            'brand' => $brand,
        ]);
    }

    public function update(\App\Http\Requests\Admin\UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validated();

        $requestedSlug = trim((string) ($validated['slug'] ?? ''));
        if ($requestedSlug !== '' && $requestedSlug !== $brand->slug) {
            $baseSlug = Str::slug($requestedSlug);
            $slug = $this->generateUniqueSlug($baseSlug, $brand->id);
        } else {
            $slug = $brand->slug;
            // Only auto-generate if old slug is somehow empty (edge case) or they changed the name and want a new slug
            if ($slug === '') {
                $slug = $this->generateUniqueSlug(Str::slug($validated['name']), $brand->id);
            }
        }

        $brand->update([
            'name' => (string) $validated['name'],
            'slug' => $slug,
            'logo' => $validated['logo'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return to_route('admin.brands.index')->with('status', 'Brand updated successfully.');
    }

    protected function generateUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $normalizedBaseSlug = $baseSlug !== '' ? $baseSlug : 'brand';
        $slug = $normalizedBaseSlug;
        $counter = 2;

        while (Brand::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $normalizedBaseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
