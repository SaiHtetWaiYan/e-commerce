<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PayoutService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateVendorPayoutsCommand extends Command
{
    protected $signature = 'payouts:generate
                            {--from= : Period start date (Y-m-d)}
                            {--to= : Period end date (Y-m-d)}
                            {--method=bank_transfer : Payout payment method}
                            {--auto : Mark this run as scheduler-triggered}';

    protected $description = 'Generate vendor payouts for delivered orders in a date range';

    public function __construct(public PayoutService $payoutService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        [$periodStart, $periodEnd] = $this->resolvePeriod();
        $paymentMethod = (string) $this->option('method');

        $vendors = User::query()
            ->vendors()
            ->whereHas('vendorProfile', fn ($query) => $query->where('is_verified', true))
            ->with('vendorProfile')
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($vendors as $vendor) {
            $earnings = $this->payoutService->calculateVendorEarnings($vendor, $periodStart, $periodEnd);

            if ((float) $earnings['net_amount'] <= 0.0) {
                $skippedCount++;
                continue;
            }

            $payout = $this->payoutService->createPayout($vendor, $periodStart, $periodEnd, $paymentMethod);

            if ($payout->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->info("Payout generation complete. Created: {$createdCount}, skipped: {$skippedCount}.");

        return self::SUCCESS;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolvePeriod(): array
    {
        $fromOption = $this->option('from');
        $toOption = $this->option('to');

        if (is_string($fromOption) && $fromOption !== '' && is_string($toOption) && $toOption !== '') {
            $start = Carbon::parse($fromOption)->startOfDay();
            $end = Carbon::parse($toOption)->endOfDay();

            return [$start, $end];
        }

        $start = now()->subWeek()->startOfWeek();
        $end = now()->subWeek()->endOfWeek();

        return [$start, $end];
    }
}
