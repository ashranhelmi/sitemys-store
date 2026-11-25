<x-mail::message>
# Your Order Has Expired

Hi {{ $order->customer_name }},

Your order **{{ $order->order_number }}** has expired because payment was not completed within 24 hours.

**Order Total:** RM {{ number_format($order->total, 2) }}

<x-mail::table>
| Item | Size | Qty |
|:-----|:-----|:----|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} |
@endforeach
</x-mail::table>

No charges have been made. If you'd still like these items, you're welcome to place a new order.

<x-mail::button :url="route('shop.index')">
Shop Again
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
