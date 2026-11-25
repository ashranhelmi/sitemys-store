<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('store.name'))</title>
    <meta name="description" content="@yield('description', config('store.tagline'))">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Sora:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="storefront bg-[#f5f4f1] text-[#15171a] antialiased" x-data="{ cartOpen: false }">

    {{-- Nav --}}
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-[#e3e0d9] bg-[#f5f4f1]/90 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('shop.index') }}" aria-label="{{ config('store.name') }} home">
                @include('partials.wordmark', ['size' => 'text-[26px]'])
            </a>

            <div class="flex items-center gap-6">
                <a href="{{ request()->routeIs('shop.index', 'home') ? '#products' : route('shop.index').'#products' }}" class="text-sm font-semibold text-[#5b636e] hover:text-[#15171a] transition">Shop</a>
                <a href="{{ request()->routeIs('shop.index', 'home') ? '#looks' : route('shop.index').'#looks' }}" class="hidden sm:inline text-sm font-semibold text-[#5b636e] hover:text-[#15171a] transition">Looks</a>

                {{-- Cart button --}}
                <button @click="cartOpen = true" class="relative flex items-center gap-2 text-sm text-[#5b636e] hover:text-[#15171a] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-[#E0312B] text-white text-[10px] font-bold rounded-full size-4 flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </button>
            </div>
        </div>
    </nav>

    {{-- Cart Drawer --}}
    <div x-show="cartOpen" x-cloak class="fixed inset-0 z-50 flex">
        <div class="absolute inset-0 bg-black/60" @click="cartOpen = false"></div>
        <div class="relative ml-auto w-full max-w-md bg-[#ffffff] border-l border-[#e3e0d9] h-full flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-[#e3e0d9]">
                <h2 class="font-semibold tracking-wider uppercase">Cart</h2>
                <button @click="cartOpen = false" class="text-[#5b636e] hover:text-[#15171a]">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @php $cart = session('cart', []); @endphp
                @forelse($cart as $key => $item)
                    <div class="flex gap-4 items-start">
                        <div class="size-16 bg-[#e3e0d9] rounded shrink-0 overflow-hidden">
                            @if($item['image'])
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $item['product_name'] }}</p>
                            @if($item['size']) <p class="text-xs text-[#5b636e]">Size: {{ $item['size'] }}</p> @endif
                            <p class="text-sm text-[#15171a] mt-1">RM {{ number_format($item['unit_price'], 2) }}</p>

                            <div class="flex items-center gap-3 mt-2">
                                <form method="POST" action="{{ route('cart.update') }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="key" value="{{ $key }}">
                                    <select name="quantity" onchange="this.form.submit()" class="bg-[#f5f4f1] border border-[#e3e0d9] text-sm rounded-lg pl-4 pr-10 py-2.5 text-[#15171a]">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" @selected($i == $item['quantity'])>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </form>

                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="key" value="{{ $key }}">
                                    <button type="submit" class="text-xs text-[#5b636e] hover:text-red-400 transition">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-[#5b636e] text-sm text-center py-12">Your cart is empty.</p>
                @endforelse
            </div>

            @if(count($cart) > 0)
                @php
                    $subtotal = collect($cart)->sum(fn($i) => $i['unit_price'] * $i['quantity']);
                    $shipping = \App\Support\Shipping::for($subtotal);
                @endphp
                <div class="p-6 border-t border-[#e3e0d9] space-y-4">
                    <div class="flex justify-between text-sm text-[#5b636e]">
                        <span>Subtotal</span><span>RM {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-[#5b636e]">
                        <span>Shipping</span><span>{{ $shipping > 0 ? 'RM '.number_format($shipping, 2) : 'Free' }}</span>
                    </div>
                    @if($shipping > 0 && config('store.free_shipping_over') > 0)
                        <p class="text-xs text-[#5b636e]">Add RM {{ number_format(config('store.free_shipping_over') - $subtotal, 2) }} more for free shipping.</p>
                    @endif
                    <div class="flex justify-between font-semibold">
                        <span>Total</span><span>RM {{ number_format($subtotal + $shipping, 2) }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="block w-full bg-[#15171a] hover:bg-[#2b2f36] text-[#f5f4f1] text-center font-semibold py-3 rounded-xl transition text-sm">
                        Checkout
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-6 right-6 z-50 bg-[#ffffff] border border-[#e3e0d9] text-sm px-4 py-3 rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed bottom-6 right-6 z-50 bg-red-900/80 border border-red-700 text-sm px-4 py-3 rounded shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[#e3e0d9] mt-24 py-12">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between gap-8">
                <div>
                    @include('partials.wordmark', ['size' => 'text-[30px]'])
                    <p class="text-[#5b636e] text-sm mt-2">{{ config('store.tagline') }}</p>
                </div>
                <div class="flex flex-col gap-2 text-sm text-[#5b636e]">
                    <a href="{{ route('legal.privacy') }}" class="hover:text-[#15171a] transition">Privacy Policy</a>
                    <a href="{{ route('legal.terms') }}" class="hover:text-[#15171a] transition">Terms & Conditions</a>
                    <a href="{{ route('legal.returns') }}" class="hover:text-[#15171a] transition">Return & Refund Policy</a>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-10 pt-6 border-t border-[#e3e0d9]">
                <p class="text-[#5b636e] text-xs">&copy; {{ date('Y') }} {{ config('store.name') }}. All rights reserved.</p>
                @if(config('store.demo'))
                <p class="text-[#5b636e] text-xs">Demo store · fictional products · built with <a href="https://sitemys.com" class="underline hover:text-[#15171a]">sitemys.</a></p>
                @endif
            </div>
        </div>
    </footer>

</body>
</html>
