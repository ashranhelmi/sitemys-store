@extends('layouts.storefront')
@section('title', 'Return & Refund Policy — '.config('store.name'))
@section('content')
<div class="max-w-3xl mx-auto px-6 py-20 prose prose-invert prose-sm">
    <h1>Return & Refund Policy</h1>
    <p class="text-[#5b636e]">Last updated: {{ date('d F Y') }}</p>

    <h2>1. Eligibility</h2>
    <p>Returns are accepted within 7 days of delivery. Items must be unworn, unwashed, and in original condition with all tags attached.</p>

    <h2>2. Non-Returnable Items</h2>
    <p>Sale items and items marked as final sale are not eligible for return or exchange.</p>

    <h2>3. How to Return</h2>
    <p>Email us at <a href="mailto:{{ config('store.email') }}">{{ config('store.email') }}</a> with your order number and reason for return. We will provide return instructions within 2 business days.</p>

    <h2>4. Refund Conditions</h2>
    <p>Once we receive and inspect the returned item, we will process your refund within 7 business days. Refunds are issued to the original payment method.</p>

    <h2>5. Shipping Costs</h2>
    <p>Return shipping costs are borne by the customer unless the item received was defective or incorrect.</p>
</div>
@endsection
