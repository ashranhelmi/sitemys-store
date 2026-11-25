<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BillplzVerificationTest extends TestCase
{
    use RefreshDatabase;

    private Payment $payment;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['services.billplz.api_key' => 'test-key', 'services.billplz.sandbox' => true]);

        $this->product = Product::create(['name' => 'Tee', 'slug' => 'tee', 'price' => 79, 'stock' => 5, 'sizes' => ['M'], 'images' => [], 'is_active' => true]);
        $order = Order::create([
            'order_number' => 'SMS-TEST1', 'customer_name' => 'Buyer', 'customer_email' => 'buyer@example.com',
            'customer_phone' => '0100000000', 'address' => '1 Jalan Ujian', 'city' => 'Kuala Lumpur', 'state' => 'Kuala Lumpur',
            'postcode' => '50000', 'subtotal' => 79, 'shipping' => 10, 'total' => 89, 'status' => 'pending', 'billplz_bill_id' => 'bill123',
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $this->product->id, 'product_name' => 'Tee', 'size' => 'M', 'unit_price' => 79, 'quantity' => 1, 'subtotal' => 79]);
        $this->payment = Payment::create(['order_id' => $order->id, 'billplz_bill_id' => 'bill123', 'amount' => 89, 'status' => 'pending']);
    }

    private function billplzSays(array $bill): void
    {
        Http::fake(['*/api/v3/bills/bill123' => Http::response(array_merge(['id' => 'bill123', 'amount' => 8900, 'paid_amount' => 8900], $bill))]);
    }

    public function test_a_forged_redirect_does_not_mark_the_order_paid(): void
    {
        $this->billplzSays(['paid' => false, 'paid_amount' => 0, 'state' => 'due']);

        $this->get('/payment/callback?billplz[id]=bill123&billplz[paid]=true')->assertRedirect($this->payment->order->retryUrl());

        $this->assertSame('pending', $this->payment->fresh()->status);
        $this->assertSame(5, $this->product->fresh()->stock);
    }

    public function test_a_forged_webhook_does_not_mark_the_order_paid(): void
    {
        $this->billplzSays(['paid' => false, 'paid_amount' => 0, 'state' => 'due']);

        $this->post('/webhook/billplz', ['id' => 'bill123', 'paid' => 'true', 'amount' => '8900'])->assertOk();

        $this->assertSame('pending', $this->payment->fresh()->status);
    }

    public function test_a_verified_payment_is_recorded_once_even_when_redirect_and_webhook_both_arrive(): void
    {
        $this->billplzSays(['paid' => true, 'state' => 'paid', 'paid_at' => '2026-11-25T22:00:00+08:00']);

        $this->get('/payment/callback?billplz[id]=bill123&billplz[paid]=true')->assertRedirect($this->payment->order->thankYouUrl());
        $this->post('/webhook/billplz', ['id' => 'bill123', 'paid' => 'true'])->assertOk();

        $this->assertSame('paid', $this->payment->fresh()->status);
        $this->assertSame('paid', $this->payment->order->fresh()->status);
        $this->assertSame(4, $this->product->fresh()->stock); // deducted once, not twice
    }

    public function test_order_pages_need_the_signed_link(): void
    {
        $order = $this->payment->order;

        $this->get("/thank-you/{$order->id}")->assertForbidden();
        $this->get("/payment/retry/{$order->id}")->assertForbidden();
        $this->post("/payment/renew/{$order->id}")->assertForbidden();
        $this->get($order->thankYouUrl())->assertOk()->assertSee('Buyer');
    }

    public function test_a_paid_bill_for_the_wrong_amount_is_rejected(): void
    {
        $this->billplzSays(['paid' => true, 'paid_amount' => 100, 'state' => 'paid']);

        $this->post('/webhook/billplz', ['id' => 'bill123'])->assertOk();

        $this->assertSame('pending', $this->payment->fresh()->status);
    }
}
