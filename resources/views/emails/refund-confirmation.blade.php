<x-mail::message>
# Refund Confirmed

Hi {{ $order->customer_name }},

Your refund for order **{{ $order->order_number }}** has been processed.

**Refund Amount:** RM {{ number_format($order->total, 2) }}

Please allow 3–7 business days for the refund to appear in your account, depending on your bank or payment method.

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

If you have any questions about your refund, please don't hesitate to reach out.

<x-mail::button :url="route('shop.index')">
Continue Shopping
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
