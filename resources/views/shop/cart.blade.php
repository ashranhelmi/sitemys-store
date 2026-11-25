@extends('layouts.storefront')
@section('title', 'Cart — '.config('store.name'))

@section('content')

{{-- Header bar — light --}}
<div class="bg-[#f5f4f1] border-b border-[#e3e0d9]">
    <div class="max-w-5xl mx-auto px-6 py-8 flex items-end justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-[#5b636e] mb-1">Your</p>
            <h1 class="font-display text-3xl md:text-4xl tracking-[-0.03em] text-[#15171a]">Cart</h1>
        </div>
        @php $cartCount = collect($cart)->sum('quantity'); @endphp
        @if($cartCount > 0)
            <p class="text-sm text-[#5b636e]">{{ $cartCount }} {{ Str::plural('item', $cartCount) }}</p>
        @endif
    </div>
</div>

@if(empty($cart))
    {{-- Empty state — light --}}
    <div class="bg-[#f5f4f1] min-h-[60vh] flex flex-col items-center justify-center text-center px-6 py-24">
        <div class="size-20 rounded-full bg-[#ebe8e2] border border-[#e3e0d9] flex items-center justify-center mx-auto mb-6">
            <svg class="size-8 text-[#a9a49b]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <p class="text-[#15171a] font-semibold text-lg mb-2">Your cart is empty</p>
        <p class="text-[#5b636e] text-sm mb-8">Looks like you haven't added anything yet.</p>
        <a href="{{ route('shop.index') }}" class="inline-block bg-[#15171a] hover:bg-[#333] text-white font-semibold px-8 py-3 uppercase tracking-wider text-sm transition rounded-lg">
            Browse Products
        </a>
    </div>

@else
    {{-- Cart content — split light/dark --}}
    <div class="grid md:grid-cols-3 min-h-[70vh]">

        {{-- Items — light --}}
        <div class="md:col-span-2 bg-[#f5f4f1] px-6 py-10 space-y-4">
            @php
                $subtotal = collect($cart)->sum(fn($i) => $i['unit_price'] * $i['quantity']);
                $shipping = \App\Support\Shipping::for($subtotal);
            @endphp
            @foreach($cart as $key => $item)
                <div class="flex gap-4 p-4 bg-white border border-[#e3e0d9] rounded-xl shadow-sm">
                    <div class="size-24 bg-[#ebe8e2] rounded-lg shrink-0 overflow-hidden border border-[#e3e0d9]">
                        @if($item['image'])
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[#15171a]">{{ $item['product_name'] }}</p>
                        @if($item['size'])
                            <span class="inline-block mt-1 text-xs border border-[#e3e0d9] text-[#5b636e] px-2 py-0.5 rounded">{{ $item['size'] }}</span>
                        @endif
                        <p class="text-[#15171a] font-semibold text-sm mt-2">RM {{ number_format($item['unit_price'], 2) }}</p>

                        <div class="flex items-center gap-4 mt-3">
                            <form method="POST" action="{{ route('cart.update') }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="hidden" name="key" value="{{ $key }}">
                                <label class="text-xs text-[#5b636e]">Qty:</label>
                                <select name="quantity" onchange="this.form.submit()"
                                        class="bg-white border border-[#e3e0d9] text-sm rounded-lg pl-4 pr-10 py-2.5 text-[#15171a] focus:outline-none focus:border-[#0a84c6] transition">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" @selected($i == $item['quantity'])>{{ $i }}</option>
                                    @endfor
                                </select>
                            </form>

                            <form method="POST" action="{{ route('cart.remove') }}">
                                @csrf @method('DELETE')
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button type="submit" class="text-xs text-[#5b636e] hover:text-red-500 transition">Remove</button>
                            </form>
                        </div>
                    </div>
                    <p class="font-bold text-sm text-[#15171a] shrink-0">RM {{ number_format($item['unit_price'] * $item['quantity'], 2) }}</p>
                </div>
            @endforeach

            <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm text-[#5b636e] hover:text-[#15171a] transition mt-2">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Continue Shopping
            </a>
        </div>

        {{-- Summary — dark --}}
        <div class="bg-[#ffffff] border-l border-[#e3e0d9] px-8 py-10 flex flex-col justify-between">
            <div class="space-y-5">
                <h2 class="font-semibold uppercase tracking-wider text-[#15171a]">Order Summary</h2>

                @foreach($cart as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-[#5b636e] truncate pr-2">{{ $item['product_name'] }} × {{ $item['quantity'] }}</span>
                        <span class="text-[#15171a] shrink-0">RM {{ number_format($item['unit_price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach

                <div class="border-t border-[#e3e0d9] pt-5 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-[#5b636e]">Subtotal</span>
                        <span class="text-[#15171a]">RM {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#5b636e]">Shipping</span>
                        <span class="text-[#15171a]">{{ $shipping > 0 ? 'RM '.number_format($shipping, 2) : 'Free' }}</span>
                    </div>
                    <div class="border-t border-[#e3e0d9] pt-3 flex justify-between font-bold text-base">
                        <span class="text-[#15171a]">Total</span>
                        <span class="text-[#15171a]">RM {{ number_format($subtotal + $shipping, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-3">
                <a href="{{ route('checkout.index') }}"
                   class="block w-full bg-[#15171a] hover:bg-[#2b2f36] text-[#f5f4f1] text-center font-bold py-4 rounded-lg transition uppercase tracking-wider text-sm">
                    Proceed to Checkout
                </a>
                <p class="text-xs text-[#5b636e] text-center">Secure checkout powered by Billplz</p>
            </div>
        </div>
    </div>
@endif

@endsection
