<x-mail::message>
# Welcome to {{ config('store.name') }}

Hi {{ $user->name }},

Your account has been created and your first order is on its way!

We're glad you're here. Your order details are below.

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

Your account email is **{{ $user->email }}**. You can log in anytime to track your orders.

<x-mail::button :url="route('shop.index')">
Start Shopping
</x-mail::button>

Thanks,<br>
{{ config('store.name') }}
</x-mail::message>
