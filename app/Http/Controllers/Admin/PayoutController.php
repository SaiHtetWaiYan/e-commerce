<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorPayout;
use App\Services\PayoutService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function __construct(public PayoutService $payoutService) {}

    public function index(): View
    {
        $payouts = VendorPayout::query()
            ->with('vendor.vendorProfile')
            ->latest()
            ->paginate(20);

        return view('admin.payouts.index', compact('payouts'));
    }

    public function create(): View
    {
        $vendors = User::query()->vendors()->with('vendorProfile')->get();

        return view('admin.payouts.create', compact('vendors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:users,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,paypal,manual'],
        ]);

        $vendor = User::query()->findOrFail($validated['vendor_id']);
        $payout = $this->payoutService->createPayout(
            $vendor,
            Carbon::parse($validated['period_start']),
            Carbon::parse($validated['period_end']),
            $validated['payment_method'],
        );

        return redirect()->route('admin.payouts.index')
            ->with('status', $payout->wasRecentlyCreated ? 'Payout created successfully.' : 'A payout already exists for the selected period.');
    }

    public function markPaid(Request $request, VendorPayout $payout): RedirectResponse
    {
        $validated = $request->validate([
            'payment_reference' => ['required', 'string', 'max:255'],
        ]);

        $this->payoutService->markAsPaid($payout, $validated['payment_reference']);

        return redirect()->route('admin.payouts.index')
            ->with('status', 'Payout marked as paid.');
    }
}
