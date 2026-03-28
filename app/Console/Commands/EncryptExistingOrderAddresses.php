<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptExistingOrderAddresses extends Command
{
    protected $signature = 'orders:encrypt-addresses
                            {--dry-run : Show count without modifying}';

    protected $description = 'Encrypt existing unencrypted shipping/billing address data on orders';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $orders = DB::table('orders')
            ->whereNotNull('shipping_address')
            ->get(['id', 'shipping_address', 'billing_address']);

        $count = 0;

        foreach ($orders as $order) {
            $needsUpdate = false;
            $updates = [];

            // Check if shipping_address is raw JSON (not encrypted)
            if ($order->shipping_address !== null && $this->isRawJson($order->shipping_address)) {
                $updates['shipping_address'] = Crypt::encryptString($order->shipping_address);
                $needsUpdate = true;
            }

            if ($order->billing_address !== null && $this->isRawJson($order->billing_address)) {
                $updates['billing_address'] = Crypt::encryptString($order->billing_address);
                $needsUpdate = true;
            }

            if ($needsUpdate && ! $dryRun) {
                DB::table('orders')->where('id', $order->id)->update($updates);
                $count++;
            } elseif ($needsUpdate) {
                $count++;
            }
        }

        $label = $dryRun ? 'Would encrypt' : 'Encrypted';
        $this->info("{$label} addresses for {$count} order(s).");

        return self::SUCCESS;
    }

    private function isRawJson(string $value): bool
    {
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
