<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartMail;
use App\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartEmails extends Command
{
    protected $signature = 'cart:send-abandoned-emails
                            {--hours=24 : Consider carts abandoned after this many hours}
                            {--dry-run : Preview without sending}';

    protected $description = 'Send recovery emails for abandoned carts';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = (bool) $this->option('dry-run');

        $carts = Cart::query()
            ->whereNotNull('user_id')
            ->whereHas('items')
            ->where('updated_at', '<', now()->subHours($hours))
            ->where('updated_at', '>', now()->subDays(7))
            ->with(['user', 'items.product'])
            ->get();

        $sent = 0;

        foreach ($carts as $cart) {
            if ($cart->user === null || $cart->user->email === null) {
                continue;
            }

            if ($dryRun) {
                $this->info("Would send to: {$cart->user->email} ({$cart->items->count()} items)");
            } else {
                Mail::to($cart->user->email)->queue(new AbandonedCartMail($cart));
                $sent++;
            }
        }

        $label = $dryRun ? 'Would send' : 'Sent';
        $this->info("{$label} {$sent} abandoned cart email(s).");

        return self::SUCCESS;
    }
}
