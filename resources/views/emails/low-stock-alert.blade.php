<x-mail::message>
# Low Stock Alert

A product in your store has reached **0 stock** and is now unavailable to buyers.

**Product:** {{ $product->name }}
**Current Stock:** {{ $product->stock }}
**SKU:** {{ $product->sku ?? 'N/A' }}

Buyers will see an "Out of Stock" message on this product until stock is replenished.

<x-mail::button :url="url('/dashboard/products/' . $product->id . '/edit')">
Update Stock
</x-mail::button>

— {{ config('store.name') }} System
</x-mail::message>
