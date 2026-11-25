@extends('layouts.storefront')
@section('title', 'Privacy Policy — '.config('store.name'))
@section('content')
<div class="max-w-3xl mx-auto px-6 py-20 prose prose-invert prose-sm">
    <h1>Privacy Policy</h1>
    <p class="text-[#5b636e]">Last updated: {{ date('d F Y') }}</p>

    <h2>1. Information We Collect</h2>
    <p>When you place an order, we collect your name, email address, phone number, and shipping address. Payment is processed securely through Billplz — we do not store your banking credentials.</p>

    <h2>2. How We Use Your Information</h2>
    <p>We use your information to process and fulfil your order, send order confirmations and shipping updates, and improve our service.</p>

    <h2>3. Email Communications</h2>
    <p>By placing an order, you agree to receive transactional emails related to your purchase. We do not send marketing emails without explicit consent.</p>

    <h2>4. Data Security</h2>
    <p>All data is transmitted over HTTPS. We do not share your personal information with third parties except as required to fulfil your order (e.g. courier services).</p>

    <h2>5. Contact</h2>
    <p>For any privacy concerns, contact us at <a href="mailto:{{ config('store.email') }}">{{ config('store.email') }}</a>.</p>
</div>
@endsection
