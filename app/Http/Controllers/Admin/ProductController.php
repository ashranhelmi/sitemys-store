<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get()->map(fn($p) => [
            'id'         => $p->id,
            'name'       => $p->name,
            'slug'       => $p->slug,
            'price'      => $p->price,
            'stock'      => $p->stock,
            'is_active'  => $p->is_active,
            'first_image'=> $p->first_image,
            'sizes'      => $p->sizes,
            'created_at' => $p->created_at->format('d M Y'),
        ]);

        return Inertia::render('Admin/Products/Index', ['products' => $products]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'sizes'       => 'nullable|array',
            'images'      => 'nullable|array',
            'is_active'   => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        Product::create($data);

        return back()->with('success', 'Product created.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'sizes'       => 'nullable|array',
            'images'      => 'nullable|array',
            'is_active'   => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $product->update($data);

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }
}
