<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shop\BillplzController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Public demo controls (DEMO_MODE only). The visitor works in a private SQLite copy
 * (see DemoSandbox), so switching to the admin needs no password.
 */
class DemoController extends Controller
{
    /** Fixed screens a demo link may open. Unknown names fall back to the role's home. */
    private const SCREENS = [
        'shopper' => ['shop' => 'shop.index', 'cart' => 'cart.index'],
        'admin'   => ['dashboard' => 'dashboard', 'orders' => 'admin.orders.index', 'products' => 'admin.products.index',
                      'customers' => 'admin.customers.index', 'payments' => 'admin.payments.index'],
    ];

    /** POST from the demo bar. */
    public function enter(Request $request)
    {
        $input = $request->validate(['role' => 'required|in:shopper,admin', 'screen' => 'nullable|string|max:30']);

        return $this->open($request, $input['role'], (string) ($input['screen'] ?? ''));
    }

    /** GET link for outside pages and the thank-you page: /demo/as/admin?screen=orders&order=12 */
    public function as(Request $request, string $role)
    {
        abort_unless(isset(self::SCREENS[$role]), 404);

        return $this->open($request, $role, substr((string) $request->query('screen', ''), 0, 30), $request->integer('order'));
    }

    public function reset(Request $request)
    {
        $file = config('database.connections.sqlite.database');
        Auth::logout();
        DB::disconnect('sqlite');
        if (is_string($file) && str_starts_with($file, storage_path('app/demo-sessions/'))) {
            @unlink($file);
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.index')->with('success', 'Demo reset. Everything is back to the start.');
    }

    /** Stands in for Billplz's FPX page: the visitor picks the outcome. Signed URL, own order only. */
    public function pay(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect($order->thankYouUrl());
        }

        return view('demo.pay', [
            'order'  => $order->load('items'),
            'action' => URL::signedRoute('demo.pay.finish', $order),
        ]);
    }

    public function finish(Request $request, Order $order)
    {
        $outcome = $request->validate(['outcome' => 'required|in:paid,failed'])['outcome'];
        $payment = $order->payment;
        abort_unless($payment && $payment->status === 'pending', 404);

        if ($outcome === 'paid') {
            (new BillplzController())->settleDemoPayment($payment);

            return redirect($order->thankYouUrl());
        }

        $payment->update(['status' => 'failed']);
        (new BillplzController())->sendPaymentFailed($order);

        return redirect($order->retryUrl());
    }

    private function open(Request $request, string $role, string $screen, int $orderId = 0)
    {
        Auth::guard('web')->logout();
        if ($role === 'admin') {
            $admin = User::where('is_admin', true)->orderBy('id')->first();
            abort_unless($admin, 503, 'The demo admin is missing. Reset the demo.');
            Auth::login($admin);
        }
        $request->session()->regenerate();

        if ($role === 'admin' && $screen === 'orders' && $orderId > 0 && Order::whereKey($orderId)->exists()) {
            return redirect()->route('admin.orders.show', $orderId);
        }
        $route = self::SCREENS[$role][$screen] ?? ($role === 'admin' ? 'dashboard' : 'shop.index');

        return redirect()->route($route);
    }
}
