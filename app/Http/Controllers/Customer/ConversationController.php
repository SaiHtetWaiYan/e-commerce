<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ReplyConversationRequest;
use App\Http\Requests\Customer\StartConversationRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $conversations = Conversation::query()
            ->where('buyer_id', auth()->id())
            ->with(['vendor.vendorProfile', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest('last_message_at')
            ->paginate(20);

        return view('customer.conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation): View
    {
        abort_unless((int) $conversation->buyer_id === (int) auth()->id(), 403);

        $conversation->load(['vendor.vendorProfile', 'messages.sender']);

        // Mark unread messages as read
        Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('customer.conversations.show', compact('conversation'));
    }

    public function reply(ReplyConversationRequest $request, Conversation $conversation): RedirectResponse
    {
        abort_unless((int) $conversation->buyer_id === (int) auth()->id(), 403);

        $payload = [
            'sender_id' => auth()->id(),
            'body' => $request->validated('body'),
        ];

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('messages', 'public');
        }

        $conversation->messages()->create($payload);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('customer.conversations.show', $conversation);
    }

    public function start(StartConversationRequest $request): RedirectResponse
    {
        $conversation = Conversation::query()->firstOrCreate(
            [
                'buyer_id' => auth()->id(),
                'vendor_id' => $request->validated('vendor_id'),
                'order_id' => $request->validated('order_id') ?? null,
            ],
            ['last_message_at' => now()],
        );

        $payload = [
            'sender_id' => auth()->id(),
            'body' => $request->validated('body'),
        ];

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('messages', 'public');
        }

        $conversation->messages()->create($payload);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('customer.conversations.show', $conversation);
    }
}
