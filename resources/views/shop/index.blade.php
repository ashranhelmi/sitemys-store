@extends('layouts.storefront')
@section('title', config('store.name').' · '.config('store.collection'))

@section('content')

{{-- ── Hero: shop the look ──────────────────────────────────
     Each look is a short clip with hotspots pinned to the garments.
     Pins are placed in source-image coordinates and re-projected for object-cover,
     so they stay on the clothes at any screen size. --}}
@if($looks->count())
<section
    class="relative h-[calc(100svh-4rem)] min-h-[600px] max-h-[900px] overflow-hidden bg-[#0b0d11] text-[#f2f4f7]"
    x-data="lookHero(@js($looks))"
    x-init="init()"
    @resize.window.debounce.100ms="layout()"
    @click="if (!$event.target.closest('[data-pin]')) open = null"
    aria-roledescription="carousel"
    aria-label="Shop the look"
>
    <div class="absolute inset-0" x-ref="stage">
        <template x-for="(look, i) in looks" :key="look.key">
            <div class="absolute inset-0 transition-opacity duration-700" :class="i === current ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                <img :src="look.poster" alt="" class="absolute inset-0 w-full h-full object-cover" :style="`object-position:${posX * 100}% ${posY * 100}%`">
                <template x-if="look.video && !reduced">
                    <video :src="look.video" :poster="look.poster" muted playsinline preload="auto"
                           class="absolute inset-0 w-full h-full object-cover" :style="`object-position:${posX * 100}% ${posY * 100}%`"
                           :data-look="i" @ended="ended()" x-on:error="look.video = null"></video>
                </template>
            </div>
        </template>
        {{-- readability scrims: left for the headline, bottom for the controls --}}
        <div class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-r from-[#0b0d11]/85 via-[#0b0d11]/35 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 h-2/5 z-20 pointer-events-none bg-gradient-to-t from-[#0b0d11]/90 to-transparent"></div>

        {{-- phones / tablets: tap-dots with a small card --}}
        <template x-if="!catalog">
            <div>
            <template x-for="(pin, p) in pins" :key="looks[current].key + p">
                <div class="absolute z-30 -translate-x-1/2 -translate-y-1/2" :style="`left:${pin.left}px;top:${pin.top}px`" x-show="pin.visible"
                     @mouseenter="open = p" @mouseleave="open = null">
                    <button type="button" data-pin @click="open = p"
                            class="group relative grid place-items-center size-9 rounded-full bg-white/20 backdrop-blur-md ring-1 ring-white/60 hover:bg-white/35 transition"
                            :aria-label="`${pin.name}, RM ${pin.price.toFixed(2)}`">
                        <span class="absolute inset-0 rounded-full ring-2 ring-white/50 animate-ping [animation-duration:2.4s]"></span>
                        <span class="size-3 rounded-full bg-[#FF4A3D] ring-2 ring-white"></span>
                    </button>
                    <a :href="pin.url" x-show="open === p" x-transition.opacity.duration.150ms
                       class="absolute left-1/2 -translate-x-1/2 top-11 w-56 flex items-center gap-3 rounded-2xl bg-white text-[#15171a] p-2.5 pr-3 shadow-xl ring-1 ring-black/5"
                       :class="pin.left > stageW - 140 ? 'left-auto right-0 translate-x-0' : ''">
                        <img :src="pin.image" alt="" class="size-14 rounded-xl object-cover bg-[#f5f4f1] shrink-0">
                        <span class="min-w-0">
                            <span class="block text-[13px] font-bold leading-snug" x-text="pin.name"></span>
                            <span class="block text-[13px] text-[#5b636e] mt-0.5" x-text="`RM ${pin.price.toFixed(2)}`"></span>
                        </span>
                    </a>
                </div>
            </template>
            </div>
        </template>

        {{-- desktop: living catalog. Each piece floats beside the model; a dashed line runs from its card to the red dot on the garment. --}}
        <template x-if="catalog">
            <div class="absolute inset-0 z-30 pointer-events-none" :key="looks[current].key">
                <div class="absolute inset-0" aria-hidden="true" x-html="linesSvg"></div>
                <template x-for="(c, i) in cards" :key="looks[current].key + 'c' + i">
                    <a :href="c.pin.url" class="cat-card pointer-events-auto absolute flex items-center gap-3 w-[236px] rounded-2xl bg-white/92 backdrop-blur-md text-[#15171a] p-2 pr-3 shadow-[0_18px_40px_-18px_rgba(0,0,0,.6)] ring-1 ring-white/40 hover:bg-white transition"
                       :style="`left:${c.x}px;top:${c.y}px;--d:${i * 110}ms`">
                        <img :src="c.pin.image" alt="" class="size-[60px] rounded-xl object-cover bg-[#f5f4f1] shrink-0">
                        <span class="min-w-0">
                            <span class="block text-[13px] font-bold leading-snug" x-text="c.pin.name"></span>
                            <span class="block text-[13px] text-[#5b636e] mt-0.5" x-text="`RM ${c.pin.price.toFixed(2)}`"></span>
                        </span>
                    </a>
                </template>
            </div>
        </template>
    </div>

    {{-- copy --}}
    <div class="relative z-30 h-full max-w-6xl mx-auto px-4 sm:px-6 flex flex-col justify-end pb-44 sm:pb-40 md:justify-center md:pb-0 pointer-events-none">
        <div class="max-w-xl pointer-events-auto">
            <p class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 backdrop-blur-md px-3.5 py-1.5 text-xs font-semibold tracking-[0.12em] uppercase text-[#f2f4f7]/85">
                <span class="size-1.5 rounded-full bg-[#FF4A3D]"></span>
                <span x-text="looks[current].eyebrow"></span>
            </p>
            <h1 class="font-display mt-5 text-[44px] leading-[1.02] sm:text-6xl md:text-7xl tracking-[-0.035em]" aria-live="polite">
                <template x-for="(line, l) in looks[current].lines" :key="current + '-' + l">
                    <span class="block">
                        <span x-text="line"></span><template x-if="l === looks[current].lines.length - 1"><span class="inline-block size-[0.2em] rounded-full bg-[#FF4A3D] ml-[0.04em]"></span></template>
                    </span>
                </template>
            </h1>
            <p class="mt-5 text-[15px] sm:text-base text-[#f2f4f7]/75 max-w-md">{{ config('store.collection') }}. <span x-show="!catalog">Tap a dot to shop the piece.</span><span x-show="catalog" x-cloak>Every piece in the look, pinned to where it's worn.</span> Every item carries one red dot.</p>
            <div class="mt-7 flex flex-wrap gap-2.5">
                <a href="#products" class="inline-flex items-center gap-2 rounded-xl bg-[#f2f4f7] text-[#0b0d11] px-5 py-3 text-sm font-bold hover:bg-white transition">Shop the drop</a>
                <a href="#looks" class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/5 backdrop-blur-md px-5 py-3 text-sm font-bold hover:bg-white/15 transition">See all looks</a>
            </div>
        </div>
    </div>

    {{-- look switcher + in-this-look strip --}}
    <div class="absolute inset-x-0 bottom-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pb-5 sm:pb-7 flex flex-col md:flex-row md:items-end gap-4 md:gap-8">
            <div class="flex gap-2 md:w-72 shrink-0" role="tablist">
                <template x-for="(look, i) in looks" :key="'t' + look.key">
                    <button type="button" @click="go(i)" class="group flex-1 text-left" role="tab" :aria-selected="i === current">
                        <span class="block h-[3px] rounded-full bg-white/20 overflow-hidden">
                            <span class="block h-full bg-[#f2f4f7] origin-left" :style="`transform:scaleX(${i < current ? 1 : i === current ? progress : 0})`"></span>
                        </span>
                        <span class="mt-2 block text-[11px] font-semibold tracking-[0.12em] uppercase" :class="i === current ? 'text-[#f2f4f7]' : 'text-[#f2f4f7]/50 group-hover:text-[#f2f4f7]/80'" x-text="'0' + (i + 1)"></span>
                    </button>
                </template>
            </div>
            <div x-show="catalog" class="md:ml-auto text-right" :key="'t' + looks[current].key">
                <p class="cat-total text-[11px] font-semibold tracking-[0.18em] uppercase text-[#f2f4f7]/70" x-text="`Full look · ${uniquePins.length} pieces`"></p>
                <p class="cat-total font-display text-6xl xl:text-7xl tracking-[-0.04em] leading-none mt-2">RM <span x-text="looks[current].total.toFixed(0)"></span><span class="inline-block size-[0.16em] rounded-full bg-[#FF4A3D] ml-[0.05em]"></span></p>
            </div>
            <div x-show="!catalog" class="md:ml-auto flex items-center gap-3 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/15 p-2 pr-3 overflow-x-auto max-w-full">
                <span class="hidden sm:block pl-2 pr-1 text-xs font-semibold text-[#f2f4f7]/70 whitespace-nowrap">In this look</span>
                <template x-for="(pin, p) in uniquePins" :key="looks[current].key + 'u' + p">
                    <a :href="pin.url" class="shrink-0" :title="pin.name" @mouseenter="open = pins.indexOf(pin)" @mouseleave="open = null">
                        <img :src="pin.image" :alt="pin.name" class="size-11 rounded-xl object-cover bg-white ring-1 ring-white/30 hover:ring-white transition">
                    </a>
                </template>
                <span class="pl-1 text-sm font-bold whitespace-nowrap" x-text="`RM ${looks[current].total.toFixed(0)}`"></span>
            </div>
        </div>
    </div>
</section>

<script>
function lookHero(looks) {
    return {
        looks, current: 0, progress: 0, open: null, pins: [], cards: [], linesSvg: '', catalog: false, stageW: 0,
        posX: 0.5, posY: 0.04, reduced: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
        timer: null, started: 0, plays: 0, minSeconds: 7,
        get uniquePins() { const seen = new Set(); return this.pins.filter(p => !seen.has(p.product) && seen.add(p.product)); },
        init() { this.layout(); this.$nextTick(() => this.play()); requestAnimationFrame(() => this.tick()); },
        layout() {
            const el = this.$refs.stage; const cw = el.clientWidth, ch = el.clientHeight; this.stageW = cw;
            // phones: keep the model (right side of every frame) in view
            this.posX = cw < 768 ? 0.74 : 0.5;
            const look = this.looks[this.current];
            const s = Math.max(cw / look.w, ch / look.h), dw = look.w * s, dh = look.h * s;
            const ox = (cw - dw) * this.posX, oy = (ch - dh) * this.posY;
            // phones: the headline covers the lower half, so only pins above it stay; the strip below lists every piece
            const maxTop = cw < 768 ? ch * 0.42 : ch - 150;
            this.pins = look.pins.map(p => {
                const left = ox + p.x * dw, top = oy + p.y * dh;
                return { ...p, left, top, visible: left > 24 && left < cw - 24 && top > 20 && top < maxTop };
            });
            this.catalog = cw >= 1280;
            if (this.catalog) {
                this.cards = this.layoutCards(cw, ch);
                // built as a string: Alpine templates don't render inside <svg>
                const k = this.looks[this.current].key;
                this.linesSvg = `<svg class="absolute inset-0 w-full h-full overflow-visible">` + this.cards.map((c, i) => {
                    const d = i * 110 + 150, id = `cm-${k}-${i}`;
                    return `<g style="--d:${d}ms"><mask id="${id}" maskUnits="userSpaceOnUse" x="0" y="0" width="${cw}" height="${ch}"><path class="cat-line" d="${c.path}" pathLength="1" fill="none" stroke="#fff" stroke-width="6"/></mask>`
                         + `<path d="${c.path}" mask="url(#${id})" fill="none" stroke="#f2f4f7" stroke-opacity=".95" stroke-width="1.6" stroke-dasharray="5 6" stroke-linecap="round"/>`
                         + `<circle class="cat-dot" cx="${c.pin.left}" cy="${c.pin.top}" r="10" fill="none" stroke="#f2f4f7" stroke-width="1.6"/></g>`;
                }).join('') + `</svg>`;
            } else { this.cards = []; this.linesSvg = ''; }
        },
        // Cards stack in a column at the right edge, each as close to its garment's height as it can be
        // without overlapping; the dashed line runs card → elbow → red dot.
        layoutCards(cw, ch) {
            const W = 236, H = 76, gap = 14, top = 28, bottom = ch - 190, x = cw - W - 40;
            const pins = this.pins.filter(p => p.left > 0 && p.left < x - 20).sort((a, b) => a.top - b.top);
            let ys = pins.map(p => Math.min(Math.max(p.top - H / 2, top), bottom - H));
            for (let i = 1; i < ys.length; i++) ys[i] = Math.max(ys[i], ys[i - 1] + H + gap);
            const over = ys.length ? ys[ys.length - 1] + H - bottom : 0;
            if (over > 0) ys = ys.map(y => Math.max(top, y - over));
            return pins.map((pin, i) => {
                const y = ys[i], cy = y + H / 2, knee = Math.max(pin.left + 40, x - 70);
                return { pin, x, y, path: `M ${x} ${cy} L ${knee} ${cy} L ${pin.left} ${pin.top}` };
            });
        },
        video() { return this.$root.querySelector(`video[data-look="${this.current}"]`); },
        // short clips repeat until they have shown for about minSeconds
        rounds(v) { return v && v.duration ? Math.max(1, Math.ceil(this.minSeconds / v.duration)) : 1; },
        ended() { const v = this.video(); this.plays++; if (this.plays < this.rounds(v)) { v.currentTime = 0; v.play().catch(() => this.next()); } else this.next(); },
        play() {
            this.plays = 0;
            this.started = performance.now(); clearTimeout(this.timer);
            const v = this.video();
            if (v) { v.currentTime = 0; v.play().catch(() => this.fallback()); } else this.fallback();
        },
        fallback() { clearTimeout(this.timer); this.timer = setTimeout(() => this.next(), 6000); },
        tick() {
            const v = this.video();
            this.progress = v && v.duration ? (this.plays + v.currentTime / v.duration) / this.rounds(v) : Math.min(1, (performance.now() - this.started) / 6000);
            requestAnimationFrame(() => this.tick());
        },
        go(i) { this.$root.querySelectorAll('video').forEach(v => v.pause()); this.current = i; this.open = null; this.layout(); this.$nextTick(() => this.play()); },
        next() { this.go((this.current + 1) % this.looks.length); },
    };
}
</script>
@endif

{{-- Marquee --}}
<div class="border-y border-[#e3e0d9] bg-white py-3 overflow-hidden" aria-hidden="true">
    <div class="flex gap-10 whitespace-nowrap w-max" style="animation: marquee 30s linear infinite;">
        @for($i = 0; $i < 6; $i++)
            @foreach(['Drop 01', 'Simpang', 'Heavyweight tees', 'Boxy hoodies', 'Cargo', 'Monsoon-ready', 'Made for KL weather'] as $word)
                <span class="inline-flex items-center gap-10 text-xs font-semibold uppercase tracking-[0.24em] text-[#5b636e]">{{ $word }} <span class="size-1.5 rounded-full bg-[#E0312B]"></span></span>
            @endforeach
        @endfor
    </div>
</div>
<style>@keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } } @media (prefers-reduced-motion: reduce) { [style*="marquee"] { animation: none !important; } }</style>

{{-- ── The drop ───────────────────────────────────────────── --}}
<section id="products" class="py-20 md:py-24 scroll-mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-end justify-between gap-6 mb-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.32em] text-[#5b636e] mb-3">{{ config('store.collection') }}</p>
                <h2 class="font-display text-4xl md:text-5xl tracking-[-0.035em]">The drop<span class="inline-block size-[0.2em] rounded-full bg-[#E0312B] ml-[0.04em]"></span></h2>
            </div>
            <p class="text-sm text-[#5b636e] shrink-0">{{ $products->count() }} {{ Str::plural('piece', $products->count()) }}</p>
        </div>

        @if($products->isEmpty())
            <div class="rounded-2xl border border-dashed border-[#e3e0d9] bg-white p-12 text-center">
                @include('partials.wordmark', ['size' => 'text-[32px]'])
                <p class="mt-4 text-sm text-[#5b636e]">New pieces are coming soon. Add products from the admin panel.</p>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
                @foreach($products as $product)
                    <a href="{{ route('shop.show', $product->slug) }}" class="group block rounded-2xl bg-white border border-[#e3e0d9] p-2 sm:p-2.5 hover:border-[#15171a]/30 hover:shadow-[0_12px_30px_-18px_rgba(21,23,26,.35)] transition">
                        <div class="aspect-square rounded-xl overflow-hidden bg-[#f5f4f1] relative">
                            @if($product->first_image)
                                <img src="{{ asset($product->first_image) }}" alt="{{ $product->name }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500">
                            @endif
                            @if($product->stock <= 5 && $product->stock > 0)
                                <span class="absolute top-2 left-2 rounded-full bg-white/90 backdrop-blur px-2.5 py-1 text-[11px] font-bold text-[#15171a] inline-flex items-center gap-1.5">
                                    <span class="size-1.5 rounded-full bg-[#E0312B]"></span>{{ $product->stock }} left
                                </span>
                            @elseif($product->stock === 0)
                                <span class="absolute top-2 left-2 rounded-full bg-[#15171a] px-2.5 py-1 text-[11px] font-bold text-white">Sold out</span>
                            @endif
                        </div>
                        <div class="px-1.5 pt-3 pb-1.5">
                            <p class="text-sm font-bold leading-snug">{{ $product->name }}</p>
                            <p class="text-sm text-[#5b636e] mt-1">RM {{ number_format($product->price, 2) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ── Looks ──────────────────────────────────────────────── --}}
@if($looks->count())
<section id="looks" class="py-20 md:py-24 bg-white border-y border-[#e3e0d9] scroll-mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <p class="text-xs font-bold uppercase tracking-[0.32em] text-[#5b636e] mb-3">Shop the look</p>
        <h2 class="font-display text-4xl md:text-5xl tracking-[-0.035em] mb-10">Three corners of KL<span class="inline-block size-[0.2em] rounded-full bg-[#E0312B] ml-[0.04em]"></span></h2>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach($looks as $look)
                @php $pieces = collect($look['pins'])->unique('product'); @endphp
                <article class="rounded-2xl border border-[#e3e0d9] bg-[#f5f4f1] overflow-hidden flex flex-col">
                    <div class="aspect-[4/5] overflow-hidden">
                        <img src="{{ $look['card'] }}" alt="{{ implode(' ', $look['lines']) }}" loading="lazy" class="w-full h-full object-cover object-[74%_50%]">
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#5b636e]">{{ $look['eyebrow'] }}</p>
                        <h3 class="mt-2 text-xl font-extrabold tracking-tight">{{ implode(' ', $look['lines']) }}</h3>
                        <ul class="mt-4 space-y-2.5 flex-1">
                            @foreach($pieces as $pin)
                                <li>
                                    <a href="{{ $pin['url'] }}" class="flex items-center gap-3 group">
                                        <img src="{{ $pin['image'] }}" alt="" class="size-11 rounded-lg object-cover bg-white border border-[#e3e0d9]">
                                        <span class="flex-1 text-sm font-semibold group-hover:underline">{{ $pin['name'] }}</span>
                                        <span class="text-sm text-[#5b636e]">RM {{ number_format($pin['price'], 0) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-5 pt-4 border-t border-[#e3e0d9] flex items-center justify-between text-sm">
                            <span class="text-[#5b636e]">Full look</span>
                            <span class="font-extrabold">RM {{ number_format($look['total'], 2) }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Crew band ──────────────────────────────────────────── --}}
@php $crew = config('store.crew'); @endphp
@if($crew && file_exists(public_path($crew['image'])))
<section class="relative overflow-hidden bg-[#0b0d11] text-[#f2f4f7] flex flex-col lg:block lg:h-[clamp(440px,42.9vw,780px)]" aria-label="{{ implode(' ', $crew['lines']) }}">
    {{-- phones: copy first, photo below it (no text over faces); wider screens: copy over the open sky on the left --}}
    <div class="relative order-2 lg:order-none h-[420px] md:h-[520px] lg:h-auto lg:absolute lg:inset-0">
        <img src="{{ asset($crew['image']) }}" alt="Three models from the Drop 01 looks under a KL five-foot-way at dusk" loading="lazy"
             class="absolute inset-0 w-full h-full object-cover object-[67%_50%] lg:object-[60%_20%]">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0b0d11] via-transparent to-transparent lg:bg-gradient-to-r lg:from-[#0b0d11]/75 lg:via-[#0b0d11]/20"></div>
    </div>
    <div class="relative order-1 lg:order-none lg:h-full max-w-6xl w-full mx-auto px-4 lg:px-6 pt-14 pb-6 lg:py-0 flex lg:items-center">
        <div class="max-w-xl">
            <h2 class="font-display text-4xl lg:text-6xl tracking-[-0.035em] leading-[1.02]">
                @foreach($crew['lines'] as $line)
                    <span class="block whitespace-nowrap">{{ $line }}@if($loop->last)<span class="inline-block size-[0.2em] rounded-full bg-[#FF4A3D] ml-[0.04em]"></span>@endif</span>
                @endforeach
            </h2>
            <p class="mt-4 max-w-[17rem] lg:max-w-sm text-[15px] lg:text-base text-[#f2f4f7]/85">{{ $crew['text'] }}</p>
            <a href="#products" class="mt-6 inline-flex rounded-xl bg-[#f2f4f7] text-[#0b0d11] px-5 py-3 text-sm font-bold hover:bg-white transition">Shop the drop</a>
        </div>
    </div>
</section>
@endif

{{-- ── Why ────────────────────────────────────────────────── --}}
<section class="py-20 md:py-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-3 gap-5">
            @foreach([
                ['One dot, every piece', 'No big logos. A single red dot, embroidered, so you know it when you see it.', '<circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="9"/>'],
                ['Cut for KL weather', 'Heavy cotton that breathes, boxy fits that sit off the body, a windbreaker for the 5pm rain.', '<path d="M4 14a5 5 0 0 1 4.6-5A6 6 0 0 1 20 11a4 4 0 0 1-1 7.9H8"/><path d="M8 19l-1 2M12 19l-1 2M16 19l-1 2"/>'],
                ['Small runs', 'Each drop is made in small numbers. The stock count on every product is the real count.', '<path d="M4 7h16M4 12h10M4 17h6"/>'],
            ] as [$title, $desc, $icon])
                <div class="rounded-2xl bg-white border border-[#e3e0d9] p-7">
                    <span class="grid place-items-center size-11 rounded-xl bg-[#f5f4f1] border border-[#e3e0d9]">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                    </span>
                    <h3 class="mt-5 text-lg font-extrabold tracking-tight">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-[#5b636e] leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Trust bar ──────────────────────────────────────────── --}}
<section class="pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="rounded-2xl bg-[#15171a] text-[#f2f4f7] grid sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-white/10">
            @foreach([
                ['Free shipping', 'On orders from RM '.number_format(config('store.free_shipping_over'), 0).'. RM '.number_format(config('store.shipping_flat'), 0).' flat below that.'],
                ['Secure checkout', 'FPX online banking through Billplz.'],
                ['7-day returns', 'Unworn, tags on. See the return policy.'],
            ] as [$title, $desc])
                <div class="p-6 sm:p-7">
                    <p class="text-sm font-extrabold inline-flex items-center gap-2"><span class="size-1.5 rounded-full bg-[#FF4A3D]"></span>{{ $title }}</p>
                    <p class="mt-1.5 text-sm text-[#f2f4f7]/65">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
