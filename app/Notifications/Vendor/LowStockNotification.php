<?php

namespace App\Notifications\Vendor;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Collection<int, Product> */
    public Collection $products;

    /**
     * @param  Collection<int, Product>  $products
     */
    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->products->count();

        $mail = (new MailMessage)
            ->subject("{$count} Product(s) Running Low on Stock")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$count} of your products are running low on stock:");

        $this->products->take(5)->each(function (Product $product) use ($mail): void {
            $mail->line("- {$product->name}: {$product->stock_quantity} remaining");
        });

        if ($count > 5) {
            $mail->line("...and ".($count - 5).' more.');
        }

        return $mail
            ->action('Manage Inventory', route('vendor.inventory.index'))
            ->line('Please restock these products soon to avoid missed sales.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'message' => $this->products->count().' product(s) are running low on stock.',
            'product_count' => $this->products->count(),
            'url' => route('vendor.inventory.index'),
        ];
    }
}
