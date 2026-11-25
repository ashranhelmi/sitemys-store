<x-mail::message>
# Order Placed — Payment Pending

Hi {{ $order->customer_name }},

We've received your order and it's waiting for payment. Complete your payment to confirm your items.

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

If you did not complete payment, you can still do so by clicking the button below.

<x-mail::button :url="$order->retryUrl()">
Complete My Payment
</x-mail::button>

If you did not place this order, please ignore this email.

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
