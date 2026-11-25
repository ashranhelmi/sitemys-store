<?php

namespace App\Console\Commands;

use App\Mail\OrderExpired;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpireStaleOrders extends Command
{
    protected $signature   = 'orders:expire {--hours=24 : Hours before a pending order is expired}';
    protected $description = 'Mark pending orders older than threshold as expired and restore stock';

    public function handle(): void
    {
        $hours  = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $stale = Order::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->with('items')
            ->get();

        if ($stale->isEmpty()) {
            $this->info('No stale orders found.');
            return;
        }

        foreach ($stale as $order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)
                        ->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'expired']);

            try {
                Mail::to($order->customer_email)->send(new OrderExpired($order));
            } catch (\Exception $e) {
                Log::error('Failed to send order expired email', ['error' => $e->getMessage()]);
            }

            $this->line("Expired: {$order->order_number}");
        }

        $this->info("Done — expired {$stale->count()} order(s).");
    }
}
