<x-mail::message>
# Payment Incomplete

Hi {{ $order->customer_name }},

We noticed your payment for order **{{ $order->order_number }}** was not completed.

Your order is still saved and you can retry payment at any time — your cart won't disappear.

**Order Total:** RM {{ number_format($order->total, 2) }}

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

Please note that stock is not reserved until payment is complete. If an item becomes unavailable before you complete your order, we'll let you know.

<x-mail::button :url="$order->retryUrl()">
Complete Payment
</x-mail::button>

If you have questions, feel free to reach out to us.

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
