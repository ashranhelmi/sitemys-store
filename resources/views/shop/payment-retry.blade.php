@extends('layouts.storefront')
@section('title', 'Complete Your Payment — '.config('store.name'))

@section('content')

{{-- Top banner --}}
<div class="bg-[#ffffff] border-b border-[#e3e0d9] py-10 text-center">
    <div class="size-16 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center mx-auto mb-5">
        <svg class="size-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold tracking-tight text-[#15171a] mb-2">Payment Incomplete</h1>
    <p class="text-[#5b636e]">Your order is reserved but payment was not completed.</p>
    <p class="text-[#5b636e] text-sm mt-1">Order <span class="text-[#15171a] font-mono">{{ $order->order_number }}</span></p>
</div>

{{-- Order details --}}
<div class="bg-[#f5f4f1] py-16">
    <div class="max-w-2xl mx-auto px-6 space-y-6">

        {{-- Items --}}
        <div class="bg-white border border-[#e3e0d9] rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-[#e3e0d9]">
                <h2 class="text-sm font-semibold text-[#15171a] uppercase tracking-wider">Your Order</h2>
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

        {{-- Notice card --}}
        <div class="bg-white border border-amber-200 rounded-xl px-6 py-5 shadow-sm flex gap-4 items-start">
            <svg class="size-5 text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-[#15171a]">Your order is still saved</p>
                <p class="text-sm text-[#5b636e] mt-1">Click below to return to the payment page and complete your purchase. Your items are reserved.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-2" x-data="{ loading: false }">
            <form method="POST" action="{{ $order->renewUrl() }}" class="flex-1" @submit="loading = true">
                @csrf
                <button type="submit" :disabled="loading"
                        class="w-full bg-[#15171a] hover:bg-[#2b2f36] disabled:opacity-60 text-[#f5f4f1] font-bold py-4 rounded-lg transition uppercase tracking-wider text-sm">
                    <span x-show="!loading">Complete Payment — RM {{ number_format($order->total, 2) }}</span>
                    <span x-show="loading">Redirecting to payment...</span>
                </button>
            </form>
            <a href="{{ route('shop.index') }}"
               class="flex-1 text-center bg-white border border-[#e3e0d9] hover:border-[#15171a] text-[#5b636e] hover:text-[#15171a] font-semibold py-4 rounded-lg transition text-sm flex items-center justify-center">
                Back to Shop
            </a>
        </div>

    </div>
</div>

@endsection
