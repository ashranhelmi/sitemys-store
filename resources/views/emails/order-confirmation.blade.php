<x-mail::message>
# Order Confirmed

Hi {{ $order->customer_name }},

Thank you for your order! We've received your payment and are preparing your items.

**Order Number:** {{ $order->order_number }}

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

| | |
|:---|---:|
| Subtotal | RM {{ number_format($order->subtotal, 2) }} |
| Shipping | RM {{ number_format($order->shipping, 2) }} |
| **Total** | **RM {{ number_format($order->total, 2) }}** |

**Shipping to:**
{{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->postcode }}

We'll send you another email once your order has been shipped.

<x-mail::button :url="route('shop.index')">
Continue Shopping
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
