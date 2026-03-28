<?php

namespace App\Console\Commands;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Notifications\Vendor\LowStockNotification;
use Illuminate\Console\Command;

class SendLowStockAlerts extends Command
{
    protected $signature = 'app:send-low-stock-alerts
                            {--threshold= : Override the per-product low stock threshold}
                            {--dry-run : Preview alerts without sending}';

    protected $description = 'Notify vendors about products with low stock levels';

    public function handle(): int
    {
        $globalThreshold = $this->option('threshold') ? (int) $this->option('threshold') : null;
        $isDryRun = (bool) $this->option('dry-run');

        $query = Product::query()
            ->where('status', ProductStatus::Active)
            ->where('stock_quantity', '>', 0)
            ->with(['vendor']);

        if ($globalThreshold !== null) {
            $query->where('stock_quantity', '<=', $globalThreshold);
        } else {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->where('low_stock_threshold', '>', 0);
        }

        $lowStockProducts = $query->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low-stock products found.');

            return self::SUCCESS;
        }

        $grouped = $lowStockProducts->groupBy('vendor_id');
        $vendorCount = 0;
        $productCount = $lowStockProducts->count();

        foreach ($grouped as $vendorId => $products) {
            $vendor = $products->first()->vendor;

            if ($vendor === null) {
                continue;
            }

            if ($isDryRun) {
                $this->line("  [DRY RUN] Would notify {$vendor->name} about {$products->count()} low-stock product(s):");
                $products->each(fn (Product $p) => $this->line("    - {$p->name} (stock: {$p->stock_quantity}, threshold: {$p->low_stock_threshold})"));

                continue;
            }

            $vendor->notify(new LowStockNotification($products));
            $vendorCount++;
        }

        $this->info(
            $isDryRun
                ? "[DRY RUN] Would alert {$grouped->count()} vendor(s) about {$productCount} product(s)."
                : "Sent low-stock alerts to {$vendorCount} vendor(s) for {$productCount} product(s)."
        );

        return self::SUCCESS;
    }
}
