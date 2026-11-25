@extends('layouts.storefront')
@section('title', 'Terms & Conditions — '.config('store.name'))
@section('content')
<div class="max-w-3xl mx-auto px-6 py-20 prose prose-invert prose-sm">
    <h1>Terms & Conditions</h1>
    <p class="text-[#5b636e]">Last updated: {{ date('d F Y') }}</p>

    <h2>1. General</h2>
    <p>By accessing and placing an order through {{ config('store.name') }}, you confirm you are in agreement with and bound by these terms.</p>

    <h2>2. Products</h2>
    <p>All products are limited edition and sold on a first-come, first-served basis. We reserve the right to cancel any order if stock becomes unavailable.</p>

    <h2>3. Pricing</h2>
    <p>All prices are listed in Malaysian Ringgit (MYR) and are inclusive of applicable taxes. Shipping fees are added at checkout.</p>

    <h2>4. Order Terms</h2>
    <p>Once payment is confirmed, orders cannot be modified. Please review your order carefully before proceeding to payment.</p>

    <h2>5. Liability</h2>
    <p>{{ config('store.name') }} shall not be liable for any indirect, incidental, or consequential damages arising from the use of our products or services.</p>

    <h2>6. Governing Law</h2>
    <p>These terms are governed by the laws of Malaysia.</p>
</div>
@endsection
