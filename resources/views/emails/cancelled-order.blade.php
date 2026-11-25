<x-mail::message>
# Your Order Has Been Cancelled

Hi {{ $order->customer_name }},

We're sorry to let you know that your order has been cancelled.

**Order Number:** {{ $order->order_number }}

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

**Order Total:** RM {{ number_format($order->total, 2) }}

If you made a payment and are due a refund, our team will process it within 3–5 business days.

If you have any questions, please reply to this email.

<x-mail::button :url="route('shop.index')">
Continue Shopping
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
