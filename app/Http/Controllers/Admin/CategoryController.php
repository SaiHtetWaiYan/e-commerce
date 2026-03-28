<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with(['parent:id,name'])
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $parentCategories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $requestedSlug = trim((string) ($validated['slug'] ?? ''));
        $baseSlug = Str::slug($requestedSlug !== '' ? $requestedSlug : (string) $validated['name']);
        $slug = $this->generateUniqueSlug($baseSlug);

        Category::query()->create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => (string) $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return to_route('admin.categories.index')->with('status', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::query()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.edit', [
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function update(\App\Http\Requests\Admin\UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $requestedSlug = trim((string) ($validated['slug'] ?? ''));
        if ($requestedSlug !== '' && $requestedSlug !== $category->slug) {
            $baseSlug = Str::slug($requestedSlug);
            $slug = $this->generateUniqueSlug($baseSlug, $category->id);
        } else {
            $slug = $category->slug;
            if ($slug === '') {
                $slug = $this->generateUniqueSlug(Str::slug($validated['name']), $category->id);
            }
        }

        $category->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => (string) $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return to_route('admin.categories.index')->with('status', 'Category updated successfully.');
    }

    protected function generateUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $normalizedBaseSlug = $baseSlug !== '' ? $baseSlug : 'category';
        $slug = $normalizedBaseSlug;
        $counter = 2;

        while (Category::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $normalizedBaseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
