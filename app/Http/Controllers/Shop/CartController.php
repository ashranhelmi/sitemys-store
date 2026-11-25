<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        return view('shop.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size'       => 'nullable|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < 1) {
            return back()->with('error', $product->name . ' is out of stock.');
        }

        $cart    = session('cart', []);
        $key     = $product->id . '_' . ($request->size ?? 'none');
        $newQty  = ($cart[$key]['quantity'] ?? 0) + $request->quantity;

        if ($newQty > $product->stock) {
            return back()->with('error', 'Only ' . $product->stock . ' unit(s) available for ' . $product->name . '.');
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'slug'         => $product->slug,
                'size'         => $request->size,
                'unit_price'   => $product->price,
                'quantity'     => $request->quantity,
                'image'        => $product->first_image,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', $product->name . ' added to cart.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'key'      => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);

        if (isset($cart[$request->key])) {
            $cart[$request->key]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }

        return back();
    }

    public function remove(Request $request)
    {
        $request->validate(['key' => 'required|string']);

        $cart = session('cart', []);
        unset($cart[$request->key]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item removed from cart.');
    }
}
