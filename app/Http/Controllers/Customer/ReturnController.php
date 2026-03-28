<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SubmitReturnRequest;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(public ReturnService $returnService) {}

    public function index(): View
    {
        $returns = ReturnRequest::query()
            ->where('user_id', auth()->id())
            ->with('order')
            ->latest()
            ->paginate(15);

        return view('customer.returns.index', compact('returns'));
    }

    public function create(Order $order): View
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        return view('customer.returns.create', compact('order'));
    }

    public function store(SubmitReturnRequest $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        $validated = $request->validated();

        try {
            $this->returnService->createRequest(
                $order,
                auth()->user(),
                (string) $validated['reason'],
                array_map('intval', (array) ($validated['order_item_ids'] ?? [])),
            );

            return redirect()->route('customer.returns.index')
                ->with('status', 'Return request submitted successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['order_item_ids' => $e->getMessage()]);
        }
    }
}
