<x-mail::message>
# Your Order Has Been Delivered

Hi {{ $order->customer_name }},

Your order **{{ $order->order_number }}** has been delivered. We hope you love your new items!

<x-mail::table>
| Item | Size | Qty | Price |
|:-----|:-----|:----|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} | RM {{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

If there's any issue with your order, please contact us and we'll make it right.

<x-mail::button :url="route('shop.index')">
Shop Again
</x-mail::button>

Thanks for choosing {{ config('store.name') }},<br>
{{ config('store.name') }}
</x-mail::message>
