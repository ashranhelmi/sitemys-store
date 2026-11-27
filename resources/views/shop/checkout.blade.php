@extends('layouts.storefront')
@section('title', 'Checkout — '.config('store.name'))

@section('content')

{{-- Header bar --}}
<div class="bg-[#f5f4f1] border-b border-[#e3e0d9]">
    <div class="max-w-5xl mx-auto px-6 py-8">
        <p class="text-xs uppercase tracking-[0.3em] text-[#5b636e] mb-1">Almost there</p>
        <h1 class="font-display text-3xl md:text-4xl tracking-[-0.03em] text-[#15171a]">Checkout</h1>
    </div>
</div>

<div class="grid md:grid-cols-3 min-h-[80vh]">

    {{-- Left: Form — light --}}
    <div class="md:col-span-2 bg-[#f5f4f1] px-6 md:px-10 py-10">
        <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form" class="space-y-8"
              x-data @submit="$dispatch('checkout-submitting')">
            @csrf
            @php
                // Demo: fictional sample details so a visitor can check out in one tap. Change anything.
                $d = config('demo.enabled') ? ['name' => 'Aina Rahman', 'email' => 'aina@example.com', 'phone' => '012-345 6789',
                    'address' => 'No. 12, Jalan Contoh 3', 'city' => 'Kuala Lumpur', 'postcode' => '50000', 'state' => 'Kuala Lumpur'] : [];
            @endphp
            @if($d)
                <p class="text-sm text-[#5b636e] bg-white border border-[#e3e0d9] rounded-xl px-4 py-3">Demo: sample details are filled in. Change anything you like. No real payment is taken.</p>
            @endif

            {{-- Customer info --}}
            <div>
                <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-[#15171a] mb-5 flex items-center gap-3">
                    <span class="size-7 rounded-full bg-[#15171a] text-white text-xs flex items-center justify-center font-bold shrink-0">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    Customer Info
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $d['name'] ?? '') }}" required placeholder="Muhammad Ali"
                               class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] placeholder-[#a9a49b] focus:outline-none focus:border-[#0a84c6] transition">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">Email</label>
                            <input type="email" name="email" value="{{ old('email', $d['email'] ?? '') }}" required placeholder="you@email.com"
                                   class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] placeholder-[#a9a49b] focus:outline-none focus:border-[#0a84c6] transition">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $d['phone'] ?? '') }}" required placeholder="01X-XXXXXXX"
                                   class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] placeholder-[#a9a49b] focus:outline-none focus:border-[#0a84c6] transition">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shipping address --}}
            <div>
                <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-[#15171a] mb-5 flex items-center gap-3">
                    <span class="size-7 rounded-full bg-[#15171a] text-white text-xs flex items-center justify-center font-bold shrink-0">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    Shipping Address
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">Street Address</label>
                        <input type="text" name="address" value="{{ old('address', $d['address'] ?? '') }}" required placeholder="No. 12, Jalan..."
                               class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] placeholder-[#a9a49b] focus:outline-none focus:border-[#0a84c6] transition">
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">City</label>
                            <input type="text" name="city" value="{{ old('city', $d['city'] ?? '') }}" required placeholder="Kuala Lumpur"
                                   class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] placeholder-[#a9a49b] focus:outline-none focus:border-[#0a84c6] transition">
                            @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">Postcode</label>
                            <input type="text" name="postcode" value="{{ old('postcode', $d['postcode'] ?? '') }}" required placeholder="50000"
                                   class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] placeholder-[#a9a49b] focus:outline-none focus:border-[#0a84c6] transition">
                            @error('postcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#5b636e] mb-1.5 uppercase tracking-wider">State</label>
                            <select name="state" required
                                    class="w-full bg-white border border-[#e3e0d9] rounded-lg px-4 py-3 text-sm text-[#15171a] focus:outline-none focus:border-[#0a84c6] transition">
                                <option value="">Select</option>
                                @foreach(['Johor','Kedah','Kelantan','Kuala Lumpur','Labuan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Putrajaya','Sabah','Sarawak','Selangor','Terengganu'] as $state)
                                    <option value="{{ $state }}" @selected(old('state', $d['state'] ?? null) === $state)>{{ $state }}</option>
                                @endforeach
                            </select>
                            @error('state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div>
                <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-[#15171a] mb-5 flex items-center gap-3">
                    <span class="size-7 rounded-full bg-[#15171a] text-white text-xs flex items-center justify-center font-bold shrink-0">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </span>
                    Payment
                </h2>
                <div class="flex items-center gap-4 bg-white border-2 border-[#15171a] rounded-lg px-5 py-4">
                    <div class="size-4 rounded-full border-2 border-[#15171a] flex items-center justify-center shrink-0">
                        <div class="size-2 rounded-full bg-[#15171a]"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-[#15171a]">Billplz — FPX Online Banking</p>
                        <p class="text-xs text-[#5b636e] mt-0.5">All major Malaysian banks supported</p>
                    </div>
                    <svg class="size-4 text-[#15171a] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    {{-- Right: Summary — dark --}}
    <div class="bg-[#ffffff] border-l border-[#e3e0d9] px-8 py-10 flex flex-col justify-between">
        <div class="space-y-5">
            <h2 class="font-semibold uppercase tracking-wider text-[#15171a]">Order Summary</h2>

            @foreach($cart as $item)
                <div class="flex gap-3 items-start">
                    <div class="size-12 bg-[#e3e0d9] rounded-lg shrink-0 overflow-hidden">
                        @if($item['image'])
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-[#15171a] font-medium truncate">{{ $item['product_name'] }}</p>
                        @if($item['size']) <p class="text-xs text-[#5b636e]">{{ $item['size'] }}</p> @endif
                        <p class="text-xs text-[#5b636e] mt-0.5">× {{ $item['quantity'] }}</p>
                    </div>
                    <p class="text-sm text-[#15171a] shrink-0 font-medium">RM {{ number_format($item['unit_price'] * $item['quantity'], 2) }}</p>
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
                    <span class="text-[#15171a]">RM {{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-8 space-y-3" x-data="{ submitting: false }">
            <button type="submit" form="checkout-form"
                    @checkout-submitting.window="submitting = true"
                    :disabled="submitting"
                    class="w-full bg-[#15171a] hover:bg-[#2b2f36] disabled:opacity-60 text-[#f5f4f1] font-bold py-4 rounded-lg transition uppercase tracking-wider text-sm">
                <span x-show="!submitting">Pay Now — RM {{ number_format($total, 2) }}</span>
                <span x-show="submitting">Processing...</span>
            </button>
            <p class="text-xs text-[#5b636e] text-center">
                By placing an order you agree to our
                <a href="{{ route('legal.terms') }}" class="underline hover:text-[#15171a]">Terms & Conditions</a>.
            </p>
        </div>
    </div>
</div>

@endsection
