<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Enums\OrderStatus;
use App\Models\Customer\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel pending orders that have exceeded the timeout.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $timeout = config('shop.unpaid_order_timeout', 1440);
        $staleTime = now()->subMinutes($timeout);

        $orders = Order::where('status', OrderStatus::PENDING)
            ->where('created_at', '<=', $staleTime)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No stale pending orders found.');
            return;
        }

        $this->info("Found {$orders->count()} stale pending orders. Starting cancellation...");

        foreach ($orders as $order) {
            $this->cancelOrder($order);
        }

        $this->info('Auto-cancellation process completed.');
    }

    /**
     * Cancel an order and restore stock.
     */
    protected function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Update Order Status
            $order->update(['status' => OrderStatus::CANCELLED]);

            // Restore Stock for each product
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }

            Log::info("Auto-Cancelled stale Order #{$order->id} and restored stock.");
            $this->line("Order #{$order->id} cancelled.");
        });
    }
}
