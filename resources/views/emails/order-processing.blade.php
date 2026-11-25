<x-mail::message>
# Your Order Is Being Processed

Hi {{ $order->customer_name }},

Good news — we're now processing your order and getting your items ready.

**Order Number:** {{ $order->order_number }}

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

**Shipping to:**
{{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->postcode }}

We'll send you another update once your order has been packed and shipped.

<x-mail::button :url="route('shop.index')">
Continue Shopping
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
