@extends('layouts.storefront')
@section('title', 'Order Confirmed — '.config('store.name'))

@section('content')

{{-- Top banner — dark --}}
<div class="bg-[#ffffff] border-b border-[#e3e0d9] py-10 text-center">
    <div class="size-16 rounded-full bg-[#15171a]/20 border border-[#15171a]/40 flex items-center justify-center mx-auto mb-5">
        <svg class="size-8 text-[#15171a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold tracking-tight text-[#15171a] mb-2">Order Confirmed</h1>
    <p class="text-[#5b636e]">Thank you, {{ $order->customer_name }}. We've got your order.</p>
    <p class="text-[#5b636e] text-sm mt-1">Confirmation sent to <span class="text-[#15171a]">{{ $order->customer_email }}</span></p>
</div>

{{-- Order details — light --}}
<div class="bg-[#f5f4f1] py-16">
    <div class="max-w-2xl mx-auto px-6 space-y-6">

        {{-- Order meta cards --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white border border-[#e3e0d9] rounded-xl p-4 text-center shadow-sm">
                <div class="size-8 rounded-full bg-[#ebe8e2] flex items-center justify-center mx-auto mb-2">
                    <svg class="size-4 text-[#5b636e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-xs text-[#5b636e] uppercase tracking-wider mb-1">Order</p>
                <p class="text-sm font-semibold text-[#15171a] font-mono truncate">{{ $order->order_number }}</p>
            </div>
            <div class="bg-white border border-[#e3e0d9] rounded-xl p-4 text-center shadow-sm">
                <div class="size-8 rounded-full bg-[#ebe8e2] flex items-center justify-center mx-auto mb-2">
                    <svg class="size-4 text-[#5b636e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs text-[#5b636e] uppercase tracking-wider mb-1">Status</p>
                <p class="text-sm font-semibold text-[#15171a]">{{ ucfirst($order->status) }}</p>
            </div>
            <div class="bg-white border border-[#e3e0d9] rounded-xl p-4 text-center shadow-sm">
                <div class="size-8 rounded-full bg-[#ebe8e2] flex items-center justify-center mx-auto mb-2">
                    <svg class="size-4 text-[#15171a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs text-[#5b636e] uppercase tracking-wider mb-1">Total Paid</p>
                <p class="text-sm font-bold text-[#15171a]">RM {{ number_format($order->total, 2) }}</p>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white border border-[#e3e0d9] rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-[#e3e0d9]">
                <h2 class="text-sm font-semibold text-[#15171a] uppercase tracking-wider">Items Ordered</h2>
            </div>
            <div class="divide-y divide-[#e3e0d9]">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center px-6 py-4 text-sm">
                        <div>
                            <p class="font-medium text-[#15171a]">{{ $item->product_name }}</p>
                            @if($item->size) <p class="text-xs text-[#5b636e] mt-0.5">Size: {{ $item->size }}</p> @endif
                            <p class="text-xs text-[#5b636e]">× {{ $item->quantity }}</p>
                        </div>
                        <p class="font-semibold text-[#15171a]">RM {{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-4 bg-[#f5f4f1] border-t border-[#e3e0d9] space-y-2">
                <div class="flex justify-between text-sm text-[#5b636e]">
                    <span>Subtotal</span><span>RM {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-[#5b636e]">
                    <span>Shipping</span><span>RM {{ number_format($order->shipping, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-[#15171a] pt-1 border-t border-[#e3e0d9]">
                    <span>Total</span><span class="text-[#15171a]">RM {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping address --}}
        <div class="bg-white border border-[#e3e0d9] rounded-xl px-6 py-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <svg class="size-4 text-[#5b636e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h2 class="text-xs font-semibold text-[#15171a] uppercase tracking-wider">Shipping To</h2>
            </div>
            <p class="text-sm text-[#5b636e]">{{ $order->address }}</p>
            <p class="text-sm text-[#5b636e]">{{ $order->city }}, {{ $order->state }} {{ $order->postcode }}</p>
        </div>

        @if(config('demo.enabled'))
            <div class="bg-[#15171a] text-white rounded-xl px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="font-semibold">Now see it from the shop's side</p>
                    <p class="text-sm text-white/70 mt-1">Your order is waiting in the admin. Move it to packed and shipped.</p>
                </div>
                <a href="{{ route('demo.as', ['role' => 'admin', 'screen' => 'orders', 'order' => $order->id]) }}"
                   class="shrink-0 inline-block bg-white text-[#15171a] font-semibold px-5 py-3 text-sm rounded-lg hover:bg-[#f5f4f1] transition">Open in admin</a>
            </div>
        @endif

        <div class="text-center pt-4">
            <a href="{{ route('shop.index') }}"
               class="inline-block bg-[#15171a] hover:bg-[#333] text-white font-semibold px-10 py-4 uppercase tracking-wider text-sm transition rounded-lg">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

@endsection
