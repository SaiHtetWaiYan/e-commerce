<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveReturnRequest;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(public ReturnService $returnService) {}

    public function index(): View
    {
        $returns = ReturnRequest::query()
            ->with(['order', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }

    public function show(ReturnRequest $returnRequest): View
    {
        $returnRequest->load(['order.items.product', 'user', 'items.orderItem.product']);

        return view('admin.returns.show', compact('returnRequest'));
    }

    public function approve(ApproveReturnRequest $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $validated = $request->validated();

        $this->returnService->approve(
            $returnRequest,
            $validated['admin_notes'] ?? null,
            isset($validated['refund_amount']) ? (float) $validated['refund_amount'] : null,
        );

        return redirect()->route('admin.returns.index')
            ->with('status', 'Return request approved and refund processed.');
    }

    public function reject(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:2000'],
        ]);

        $this->returnService->reject($returnRequest, $validated['admin_notes']);

        return redirect()->route('admin.returns.index')
            ->with('status', 'Return request rejected.');
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'return_ids' => ['required', 'array', 'min:1'],
            'return_ids.*' => ['integer', 'exists:return_requests,id'],
        ]);

        $returns = ReturnRequest::query()
            ->whereIn('id', $validated['return_ids'])
            ->where('status', \App\Enums\ReturnStatus::Pending)
            ->get();

        foreach ($returns as $returnRequest) {
            $this->returnService->approve($returnRequest, 'Bulk approved by admin');
        }

        return redirect()->route('admin.returns.index')
            ->with('status', $returns->count() . ' return(s) approved.');
    }

    public function bulkReject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'return_ids' => ['required', 'array', 'min:1'],
            'return_ids.*' => ['integer', 'exists:return_requests,id'],
        ]);

        $returns = ReturnRequest::query()
            ->whereIn('id', $validated['return_ids'])
            ->where('status', \App\Enums\ReturnStatus::Pending)
            ->get();

        foreach ($returns as $returnRequest) {
            $this->returnService->reject($returnRequest, 'Bulk rejected by admin');
        }

        return redirect()->route('admin.returns.index')
            ->with('status', $returns->count() . ' return(s) rejected.');
    }
}
