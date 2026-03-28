<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Notifications\Customer\DisputeStatusNotification;
use App\Notifications\Vendor\DisputeOpenedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisputeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Dispute::query()
            ->with(['order', 'complainant', 'respondent'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return view('admin.disputes.index', [
            'disputes' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function show(Dispute $dispute): View
    {
        return view('admin.disputes.show', [
            'dispute' => $dispute->load(['order.items.product', 'complainant', 'respondent', 'conversation.messages', 'resolvedBy']),
        ]);
    }

    public function resolve(Request $request, Dispute $dispute): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:resolved,closed'],
            'resolution' => ['required', 'string', 'max:2000'],
        ]);

        $dispute->update([
            'status' => DisputeStatus::from($validated['status']),
            'resolution' => $validated['resolution'],
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        $statusLabel = $validated['status'] === 'resolved' ? 'resolved' : 'closed';
        $message = "Your dispute #{$dispute->id} has been {$statusLabel}.";

        $dispute->complainant?->notify(new DisputeStatusNotification($dispute, $message));
        $dispute->respondent?->notify(new DisputeStatusNotification($dispute, $message));

        return back()->with('status', 'Dispute has been '.$validated['status'].'.');
    }
}
