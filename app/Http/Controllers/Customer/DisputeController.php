<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisputeController extends Controller
{
    public function index(): View
    {
        $disputes = Dispute::query()
            ->where('complainant_id', auth()->id())
            ->with(['order', 'respondent'])
            ->latest()
            ->paginate(20);

        return view('customer.disputes.index', ['disputes' => $disputes]);
    }

    public function create(Order $order): View
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        return view('customer.disputes.create', ['order' => $order]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $order = Order::query()->findOrFail($validated['order_id']);
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        $respondentId = $order->items()->first()?->vendor_id ?? $order->user_id;

        Dispute::query()->create([
            'order_id' => $order->id,
            'complainant_id' => auth()->id(),
            'respondent_id' => $respondentId,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()->route('customer.disputes.index')
            ->with('status', 'Your dispute has been submitted. Our team will review it shortly.');
    }
}
