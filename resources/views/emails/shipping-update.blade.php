<x-mail::message>
# Your Order Has Been Shipped

Hi {{ $order->customer_name }},

Great news — your order is on its way!

**Order Number:** {{ $order->order_number }}

@if($order->courier || $order->tracking_number)
**Tracking Details:**
@if($order->courier) Courier: {{ $order->courier }} @endif
@if($order->tracking_number) Tracking Number: `{{ $order->tracking_number }}` @endif

@endif
<x-mail::table>
| Item | Size | Qty |
|:-----|:-----|:----|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->size ?? '—' }} | {{ $item->quantity }} |
@endforeach
</x-mail::table>

**Shipping to:**
{{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->postcode }}

<x-mail::button :url="route('shop.index')">
Shop Again
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
