<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders    = Order::count();
        $totalRevenue   = Order::whereIn('status', ['paid', 'processing', 'packed', 'shipped', 'delivered', 'completed'])->sum('total');
        $totalProducts  = Product::count();
        $totalCustomers = User::whereHas('orders')->count();
        $pendingOrders  = Order::where('status', 'pending')->count();

        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($o) => [
                'id'            => $o->id,
                'order_number'  => $o->order_number,
                'customer_name' => $o->customer_name,
                'total'         => $o->total,
                'status'        => $o->status,
                'created_at'    => $o->created_at->format('d M Y'),
            ]);

        $recentPayments = Payment::with('order')
            ->where('status', 'paid')
            ->latest('paid_at')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'              => $p->id,
                'billplz_bill_id' => $p->billplz_bill_id,
                'order_number'    => $p->order->order_number,
                'amount'          => $p->amount,
                'paid_at'         => $p->paid_at?->format('d M Y'),
            ]);

        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(fn($i) => [
                'product_name'  => $i->product_name,
                'total_sold'    => $i->total_sold,
                'total_revenue' => number_format($i->total_revenue, 2),
            ]);

        $lowStock = Product::where('stock', '<=', 5)
            ->where('is_active', true)
            ->orderBy('stock')
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'stock' => $p->stock,
            ]);

        // Monthly revenue — all time (for trend chart + year comparison)
        $revenueMonthly = Payment::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->selectRaw("strftime('%Y-%m', paid_at) as period, ROUND(SUM(amount), 2) as revenue, COUNT(*) as orders")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn($r) => [
                'period'  => $r->period,
                'label'   => Carbon::createFromFormat('Y-m', $r->period)->format('M Y'),
                'revenue' => (float) $r->revenue,
                'orders'  => (int) $r->orders,
            ])->values();

        // Daily revenue — last 365 days (for month comparison + custom range)
        $revenueDaily = Payment::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', now()->subDays(365)->startOfDay())
            ->selectRaw("strftime('%Y-%m-%d', paid_at) as period, ROUND(SUM(amount), 2) as revenue, COUNT(*) as orders")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn($r) => [
                'period'  => $r->period,
                'label'   => Carbon::parse($r->period)->format('d M'),
                'revenue' => (float) $r->revenue,
                'orders'  => (int) $r->orders,
            ])->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_orders'    => $totalOrders,
                'total_revenue'   => number_format($totalRevenue, 2),
                'total_products'  => $totalProducts,
                'total_customers' => $totalCustomers,
                'pending_orders'  => $pendingOrders,
            ],
            'recent_orders'    => $recentOrders,
            'recent_payments'  => $recentPayments,
            'top_products'     => $topProducts,
            'low_stock'        => $lowStock,
            'revenue_monthly'  => $revenueMonthly,
            'revenue_daily'    => $revenueDaily,
        ]);
    }
}
