<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->orderBy('id')->get();
        $bySlug   = $products->keyBy('slug');

        // Resolve each look's hotspots to live products; pins for inactive/missing products are dropped.
        $looks = collect(config('store.looks', []))->map(function ($look) use ($bySlug) {
            $look['pins'] = collect($look['pins'])
                ->filter(fn ($pin) => $bySlug->has($pin['product']))
                ->map(function ($pin) use ($bySlug) {
                    $p = $bySlug[$pin['product']];
                    return $pin + [
                        'name'  => $p->name,
                        'price' => (float) $p->price,
                        'image' => $p->first_image ? asset($p->first_image) : null,
                        'url'   => route('shop.show', $p->slug),
                    ];
                })->values()->all();
            $look['total']  = collect($look['pins'])->unique('product')->sum('price');
            $look['video']  = file_exists(public_path($look['video'])) ? asset($look['video']) : null;
            $look['poster'] = asset($look['poster']);
            $look['closeup'] = isset($look['closeup']) && file_exists(public_path($look['closeup'])) ? asset($look['closeup']) : null;
            $look['card']   = isset($look['card']) && file_exists(public_path($look['card'])) ? asset($look['card']) : $look['poster'];
            return $look;
        })->filter(fn ($look) => count($look['pins']))->values();

        return view('shop.index', compact('products', 'looks'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $related = Product::where('is_active', true)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();

        return view('shop.show', compact('product', 'related'));
    }
}
