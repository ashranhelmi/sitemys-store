@extends('layouts.storefront')
@section('title', 'Demo payment — '.config('store.name'))

@section('content')
<div class="bg-[#f5f4f1] py-14">
    <div class="max-w-md mx-auto px-6">
        <div class="bg-white border border-[#e3e0d9] rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#e3e0d9]">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#E0312B]">Demo payment</p>
                <h1 class="text-2xl font-bold tracking-tight text-[#15171a] mt-1">FPX online banking</h1>
                <p class="text-sm text-[#5b636e] mt-2">On a live store, the shopper pays here through Billplz with their own bank. In this demo, no money moves: pick what happens next.</p>
            </div>

            <div class="px-6 py-4 space-y-2 text-sm">
                <div class="flex justify-between text-[#5b636e]"><span>Order</span><span class="font-mono text-[#15171a]">{{ $order->order_number }}</span></div>
                <div class="flex justify-between text-[#5b636e]"><span>Items</span><span class="text-[#15171a]">{{ $order->items->sum('quantity') }}</span></div>
                <div class="flex justify-between font-bold text-[#15171a] pt-2 border-t border-[#e3e0d9]"><span>Total</span><span>RM {{ number_format($order->total, 2) }}</span></div>
            </div>

            <div class="px-6 pb-6 pt-2 grid gap-3">
                <form method="POST" action="{{ $action }}">
                    @csrf
                    <input type="hidden" name="outcome" value="paid">
                    <button type="submit" class="w-full bg-[#15171a] hover:bg-[#333] text-white font-semibold px-6 py-3.5 text-sm rounded-lg transition">Pay RM {{ number_format($order->total, 2) }}</button>
                </form>
                <form method="POST" action="{{ $action }}">
                    @csrf
                    <input type="hidden" name="outcome" value="failed">
                    <button type="submit" class="w-full bg-white border border-[#e3e0d9] hover:bg-[#f5f4f1] text-[#15171a] font-semibold px-6 py-3.5 text-sm rounded-lg transition">Payment fails</button>
                </form>
                <p class="text-xs text-[#5b636e] text-center">Pay: the order is marked paid and stock goes down. Fails: the shopper sees the retry page.</p>
            </div>
        </div>
    </div>
</div>
@endsection
