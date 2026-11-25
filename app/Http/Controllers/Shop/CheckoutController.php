<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\OrderPending;
use App\Mail\WelcomeEmail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return redirect()->route('cart.index')
                    ->with('error', ($product->name ?? $item['product_name']) . ' is no longer available in the requested quantity. Please update your cart.');
            }
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        $shipping = \App\Support\Shipping::for($subtotal);
        $total    = $subtotal + $shipping;

        return view('shop.checkout', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        // A fresh clone has no payment keys yet: say so before creating an order that can't be paid.
        if (!config('services.billplz.api_key') || !config('services.billplz.collection_id')) {
            return back()->withInput()->with('error', 'Payments are not connected yet. Add your Billplz sandbox keys to .env (see the README).');
        }

        // Re-validate stock before creating order
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return redirect()->route('cart.index')
                    ->with('error', ($product->name ?? $item['product_name']) . ' is no longer available. Please update your cart.');
            }
        }

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'phone'    => 'required|string|max:20',
            'address'  => 'required|string',
            'city'     => 'required|string',
            'state'    => 'required|string',
            'postcode' => 'required|string|max:10',
        ]);

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name'     => $data['name'],
                'password' => Hash::make(Str::random(16)),
            ]
        );

        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        $shipping = \App\Support\Shipping::for($subtotal);
        $total    = $subtotal + $shipping;

        $order = Order::create([
            'order_number'   => Order::generateOrderNumber(),
            'user_id'        => $user->id,
            'customer_name'  => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'address'        => $data['address'],
            'city'           => $data['city'],
            'state'          => $data['state'],
            'postcode'       => $data['postcode'],
            'subtotal'       => $subtotal,
            'shipping'       => $shipping,
            'total'          => $total,
            'status'         => 'pending',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'size'         => $item['size'],
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'],
                'subtotal'     => $item['unit_price'] * $item['quantity'],
            ]);
        }

        // Send "order placed, pending payment" email
        try {
            Mail::to($order->customer_email)->send(new OrderPending($order));
        } catch (\Exception $e) {
            Log::error('Failed to send order pending email', ['error' => $e->getMessage()]);
        }

        // Welcome email for first-time buyers
        if ($user->wasRecentlyCreated) {
            try {
                Mail::to($order->customer_email)->send(new WelcomeEmail($user, $order));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email', ['error' => $e->getMessage()]);
            }
        }

        $billplzController = new BillplzController();
        return $billplzController->createBill($order);
    }

    public function thankYou(Order $order)
    {
        return view('shop.thank-you', compact('order'));
    }
}
