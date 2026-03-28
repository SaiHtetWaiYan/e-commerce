<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->string('status', 'all');
        $search = trim((string) $request->string('q'));
        $position = trim((string) $request->string('position'));

        $bannersQuery = Banner::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('link', 'like', "%{$search}%");
                });
            })
            ->when($position !== '', fn ($query) => $query->where('position', $position))
            ->orderBy('sort_order')
            ->latest('id');

        match ($status) {
            'active' => $bannersQuery->active(),
            'scheduled' => $bannersQuery->whereNotNull('starts_at')->where('starts_at', '>', now()),
            'expired' => $bannersQuery->whereNotNull('expires_at')->where('expires_at', '<', now()),
            default => null,
        };

        return view('admin.banners.index', [
            'banners' => $bannersQuery->paginate(15)->withQueryString(),
            'status' => $status,
            'position' => $position,
            'positions' => $this->positions(),
            'statusCounts' => [
                'all' => Banner::query()->count(),
                'active' => Banner::query()->active()->count(),
                'scheduled' => Banner::query()->whereNotNull('starts_at')->where('starts_at', '>', now())->count(),
                'expired' => Banner::query()->whereNotNull('expires_at')->where('expires_at', '<', now())->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create', [
            'banner' => new Banner(),
            'positions' => $this->positions(),
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $banner = Banner::query()->create([
            'title' => $validated['title'],
            'image' => $request->file('image')->store('banners', 'public'),
            'link' => $validated['link'] ?? null,
            'position' => $validated['position'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return to_route('admin.banners.edit', $banner)
            ->with('status', 'Banner created successfully.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
            'positions' => $this->positions(),
        ]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $banner->image;

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($banner->image);
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        $banner->update([
            'title' => $validated['title'],
            'image' => $imagePath,
            'link' => $validated['link'] ?? null,
            'position' => $validated['position'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return back()->with('status', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->deleteStoredImage($banner->image);
        $banner->delete();

        return to_route('admin.banners.index')
            ->with('status', 'Banner deleted successfully.');
    }

    /**
     * @return array<string, string>
     */
    protected function positions(): array
    {
        return [
            'hero' => 'Hero',
            'sidebar' => 'Sidebar',
            'footer' => 'Footer',
            'category' => 'Category',
        ];
    }

    protected function deleteStoredImage(?string $path): void
    {
        if ($path === null || $path === '' || str_starts_with($path, 'http') || str_starts_with($path, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
