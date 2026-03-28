<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\ReturnStatus;
use App\Http\Requests\Vendor\ApproveReturnRequest;
use App\Http\Requests\Vendor\RejectReturnRequest;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(public ReturnService $returnService) {}

    public function index(): View
    {
        $vendorId = (int) auth()->id();

        $returns = ReturnRequest::query()
            ->whereHas('items.orderItem', fn ($query) => $query->where('vendor_id', $vendorId))
            ->with(['order', 'user', 'items.orderItem.product'])
            ->latest()
            ->paginate(20);

        return view('vendor.returns.index', compact('returns'));
    }

    public function show(ReturnRequest $returnRequest): View
    {
        $vendorId = (int) auth()->id();

        abort_unless(
            $returnRequest->items()->whereHas('orderItem', fn ($query) => $query->where('vendor_id', $vendorId))->exists(),
            403
        );

        $returnRequest->load([
            'order',
            'user',
            'items' => fn ($query) => $query
                ->whereHas('orderItem', fn ($orderItemQuery) => $orderItemQuery->where('vendor_id', $vendorId))
                ->with(['orderItem.product']),
        ]);

        return view('vendor.returns.show', [
            'returnRequest' => $returnRequest,
            'canProcessReturn' => $this->vendorCanProcessReturn($returnRequest, $vendorId),
        ]);
    }

    public function approve(ApproveReturnRequest $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $this->returnService->approve(
            $returnRequest,
            $request->string('notes')->toString() ?: null,
            $request->filled('refund_amount') ? (float) $request->input('refund_amount') : null,
        );

        return back()->with('status', 'Return approved successfully.');
    }

    public function reject(RejectReturnRequest $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $this->returnService->reject(
            $returnRequest,
            $request->string('notes')->toString(),
        );

        return back()->with('status', 'Return rejected successfully.');
    }

    protected function vendorCanProcessReturn(ReturnRequest $returnRequest, int $vendorId): bool
    {
        $totalItems = $returnRequest->items()->count();
        $vendorOwnedItems = $returnRequest->items()
            ->whereHas('orderItem', fn ($query) => $query->where('vendor_id', $vendorId))
            ->count();

        return $returnRequest->status === ReturnStatus::Pending
            && $totalItems > 0
            && $totalItems === $vendorOwnedItems;
    }
}
