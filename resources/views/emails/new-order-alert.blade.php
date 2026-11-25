<x-mail::message>
# New Order Received

A new order has been placed.

**Order Number:** {{ $order->order_number }}
**Customer:** {{ $order->customer_name }} ({{ $order->customer_email }})
**Phone:** {{ $order->customer_phone }}
**Total:** RM {{ number_format($order->total, 2) }}

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

**Ship to:**
{{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->postcode }}

<x-mail::button :url="route('admin.orders.show', $order)">
View Order
</x-mail::button>

Thanks,<br>
{{ config('store.name') }} Admin
</x-mail::message>
