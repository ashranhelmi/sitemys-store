<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CancelledOrder;
use App\Mail\OrderDelivered;
use App\Mail\OrderProcessing;
use App\Mail\RefundConfirmation;
use App\Mail\ShippingUpdate;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items', 'payment'])
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'             => $o->id,
                'order_number'   => $o->order_number,
                'customer_name'  => $o->customer_name,
                'customer_email' => $o->customer_email,
                'total'          => $o->total,
                'status'         => $o->status,
                'items_count'    => $o->items->count(),
                'created_at'     => $o->created_at->format('d M Y, H:i'),
            ]);

        return Inertia::render('Admin/Orders/Index', ['orders' => $orders]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'payment']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id'              => $order->id,
                'order_number'    => $order->order_number,
                'customer_name'   => $order->customer_name,
                'customer_email'  => $order->customer_email,
                'customer_phone'  => $order->customer_phone,
                'address'         => $order->address,
                'city'            => $order->city,
                'state'           => $order->state,
                'postcode'        => $order->postcode,
                'subtotal'        => $order->subtotal,
                'shipping'        => $order->shipping,
                'total'           => $order->total,
                'status'          => $order->status,
                'billplz_bill_id' => $order->billplz_bill_id,
                'courier'         => $order->courier,
                'tracking_number' => $order->tracking_number,
                'notes'           => $order->notes,
                'created_at'      => $order->created_at->format('d M Y, H:i'),
                'items'           => $order->items->map(fn($i) => [
                    'id'           => $i->id,
                    'product_name' => $i->product_name,
                    'size'         => $i->size,
                    'quantity'     => $i->quantity,
                    'unit_price'   => $i->unit_price,
                    'subtotal'     => $i->subtotal,
                ]),
                'payment' => $order->payment ? [
                    'billplz_bill_id' => $order->payment->billplz_bill_id,
                    'amount'          => $order->payment->amount,
                    'status'          => $order->payment->status,
                    'paid_at'         => $order->payment->paid_at?->format('d M Y, H:i'),
                ] : null,
            ],
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $statuses = implode(',', Order::STATUSES);

        $request->validate([
            'status'          => "required|in:{$statuses}",
            'courier'         => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:2000',
        ]);

        $previousStatus = $order->status;

        $order->update([
            'status'          => $request->status,
            'courier'         => $request->courier,
            'tracking_number' => $request->tracking_number,
            'notes'           => $request->notes,
        ]);

        // Restore stock when cancelled or refunded
        if (
            in_array($request->status, ['cancelled', 'refunded']) &&
            !in_array($previousStatus, ['cancelled', 'refunded'])
        ) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)
                        ->increment('stock', $item->quantity);
                }
            }

            if ($request->status === 'refunded') {
                try {
                    Mail::to($order->customer_email)->send(new RefundConfirmation($order));
                } catch (\Exception $e) {
                    Log::error('Failed to send refund confirmation email', ['error' => $e->getMessage()]);
                }
            } else {
                try {
                    Mail::to($order->customer_email)->send(new CancelledOrder($order));
                } catch (\Exception $e) {
                    Log::error('Failed to send cancellation email', ['error' => $e->getMessage()]);
                }
            }
        }

        // Send processing email when marked as processing
        if ($request->status === 'processing' && $previousStatus !== 'processing') {
            try {
                Mail::to($order->customer_email)->send(new OrderProcessing($order));
            } catch (\Exception $e) {
                Log::error('Failed to send processing email', ['error' => $e->getMessage()]);
            }
        }

        // Send shipping email when marked as shipped
        if ($request->status === 'shipped' && $previousStatus !== 'shipped') {
            try {
                Mail::to($order->customer_email)->send(new ShippingUpdate($order));
            } catch (\Exception $e) {
                Log::error('Failed to send shipping email', ['error' => $e->getMessage()]);
            }
        }

        // Send delivered email when marked as delivered
        if ($request->status === 'delivered' && $previousStatus !== 'delivered') {
            try {
                Mail::to($order->customer_email)->send(new OrderDelivered($order));
            } catch (\Exception $e) {
                Log::error('Failed to send delivered email', ['error' => $e->getMessage()]);
            }
        }

        return back()->with('success', 'Order updated.');
    }
}
