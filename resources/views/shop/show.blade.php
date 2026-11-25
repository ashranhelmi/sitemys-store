@extends('layouts.storefront')
@section('title', $product->name . ' — '.config('store.name'))

@section('content')

{{-- Breadcrumb --}}
<div class="bg-[#f5f4f1] border-b border-[#e3e0d9]">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center gap-2 text-xs text-[#5b636e] uppercase tracking-wider">
        <a href="{{ route('shop.index') }}" class="hover:text-[#15171a] transition">Shop</a>
        <span>/</span>
        <span class="text-[#15171a]">{{ $product->name }}</span>
    </div>
</div>

{{-- Product body — light --}}
<div class="bg-[#f5f4f1] min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-16"
         x-data="{ sizeError: false, selectedSize: '', qty: 1, mainImage: '{{ $product->first_image ? asset($product->first_image) : '' }}' }">
        <div class="grid md:grid-cols-2 gap-14 items-start">

            {{-- Gallery --}}
            <div class="space-y-3">
                <div class="aspect-square bg-white rounded-2xl border border-[#e3e0d9] overflow-hidden">
                    @if($product->images && count($product->images))
                        <img :src="mainImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-[#a9a49b]">
                            <svg class="size-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>
                @if($product->images && count($product->images) > 1)
                    <div class="flex gap-2">
                        @foreach($product->images as $img)
                            <button @click="mainImage = '{{ asset($img) }}'"
                                    class="size-16 rounded-lg border-2 border-[#e3e0d9] hover:border-[#15171a] overflow-hidden shrink-0 transition">
                                <img src="{{ asset($img) }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="space-y-7">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-[#5b636e] mb-2">{{ config('store.collection') }}</p>
                    <h1 class="font-display text-3xl md:text-4xl tracking-[-0.03em] text-[#15171a]">{{ $product->name }}</h1>
                    <p class="text-xl text-[#15171a] font-extrabold mt-3">RM {{ number_format($product->price, 2) }}</p>
                </div>

                @if($product->description)
                    <p class="text-[#5b636e] leading-relaxed text-sm">{{ $product->description }}</p>
                @endif

                <div class="border-t border-[#e3e0d9]"></div>

                {{-- Size selection --}}
                @if($product->sizes && count($product->sizes))
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#15171a] mb-3">Size</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->sizes as $size)
                                <button type="button" @click="selectedSize = '{{ $size }}'; sizeError = false"
                                        :class="selectedSize === '{{ $size }}'
                                            ? 'bg-[#15171a] text-white border-[#15171a]'
                                            : 'bg-white border-[#e3e0d9] text-[#5b636e] hover:border-[#15171a] hover:text-[#15171a]'"
                                        class="px-5 py-2.5 border text-sm font-medium transition rounded-lg">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                        <p x-show="sizeError" x-cloak class="mt-2 text-sm font-semibold text-[#E0312B]">Pick a size first.</p>
                    </div>
                @endif

                {{-- Quantity --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#15171a] mb-3">Quantity</p>
                    <div class="flex items-center border border-[#e3e0d9] rounded-lg w-fit bg-white">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-5 py-3 text-[#5b636e] hover:text-[#15171a] transition text-lg leading-none">−</button>
                        <span x-text="qty" class="px-5 py-3 min-w-[3rem] text-center text-sm font-semibold text-[#15171a] border-x border-[#e3e0d9]"></span>
                        <button type="button" @click="qty = Math.min({{ max(1, $product->stock) }}, qty + 1)" class="px-5 py-3 text-[#5b636e] hover:text-[#15171a] transition text-lg leading-none">+</button>
                    </div>
                </div>

                {{-- Add to cart --}}
                @if($product->stock > 0)
                <form method="POST" action="{{ route('cart.add') }}"
                      @submit.prevent="
                        @if($product->sizes && count($product->sizes))
                        if (!selectedSize) { sizeError = true; return; }
                        @endif
                        $el.querySelector('[name=size]').value = selectedSize;
                        $el.querySelector('[name=quantity]').value = qty;
                        $el.submit();
                      ">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="size" value="">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="w-full bg-[#15171a] hover:bg-[#2b2f36] text-[#f5f4f1] font-bold py-4 uppercase tracking-[0.15em] text-sm transition rounded-lg">
                        Add to Cart
                    </button>
                </form>
                @else
                <button disabled
                        class="w-full bg-[#e3e0d9] text-[#5b636e] font-bold py-4 uppercase tracking-[0.15em] text-sm rounded-lg cursor-not-allowed">
                    Out of Stock
                </button>
                @endif

                {{-- Stock notice --}}
                @if($product->stock <= 5 && $product->stock > 0)
                    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                        <span class="size-2 rounded-full bg-amber-400 shrink-0"></span>
                        <p class="text-amber-700 text-sm font-medium">Only {{ $product->stock }} left in stock — order soon.</p>
                    </div>
                @elseif($product->stock === 0)
                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                        <span class="size-2 rounded-full bg-red-400 shrink-0"></span>
                        <p class="text-red-600 text-sm font-medium">Out of stock.</p>
                    </div>
                @endif

                {{-- Trust row --}}
                <div class="border-t border-[#e3e0d9] pt-6 grid grid-cols-3 gap-4 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="size-9 rounded-full bg-[#ebe8e2] flex items-center justify-center">
                            <svg class="size-4 text-[#5b636e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1 1h1m8-1h4l2-5H10v5h3z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-[#15171a] uppercase tracking-wider">Free Shipping</p>
                        <p class="text-xs text-[#5b636e]">RM {{ number_format(config('store.free_shipping_over'), 0) }}+</p>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <div class="size-9 rounded-full bg-[#ebe8e2] flex items-center justify-center">
                            <svg class="size-4 text-[#5b636e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-[#15171a] uppercase tracking-wider">Easy Returns</p>
                        <p class="text-xs text-[#5b636e]">7 days</p>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <div class="size-9 rounded-full bg-[#ebe8e2] flex items-center justify-center">
                            <svg class="size-4 text-[#5b636e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-[#15171a] uppercase tracking-wider">Secure Pay</p>
                        <p class="text-xs text-[#5b636e]">Billplz</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Related --}}
@if($related->count())
<section class="bg-white border-t border-[#e3e0d9] py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <h2 class="font-display text-2xl md:text-3xl tracking-[-0.03em] mb-8">Pairs well with<span class="inline-block size-[0.2em] rounded-full bg-[#E0312B] ml-[0.04em]"></span></h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($related as $item)
                <a href="{{ route('shop.show', $item->slug) }}" class="group block rounded-2xl bg-[#f5f4f1] border border-[#e3e0d9] p-2 hover:border-[#15171a]/30 transition">
                    <div class="aspect-square rounded-xl overflow-hidden bg-white">
                        @if($item->first_image)<img src="{{ asset($item->first_image) }}" alt="{{ $item->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500">@endif
                    </div>
                    <p class="px-1.5 pt-3 text-sm font-bold leading-snug">{{ $item->name }}</p>
                    <p class="px-1.5 pb-1.5 text-sm text-[#5b636e] mt-1">RM {{ number_format($item->price, 2) }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
