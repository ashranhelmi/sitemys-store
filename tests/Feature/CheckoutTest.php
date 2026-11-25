<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_without_billplz_keys_checkout_explains_and_creates_no_order(): void
    {
        config(['services.billplz.api_key' => '', 'services.billplz.collection_id' => '']);
        $p = Product::create(['name' => 'Cap', 'slug' => 'cap', 'price' => 59, 'stock' => 5, 'sizes' => ['Free Size'], 'images' => [], 'is_active' => true]);

        $this->withSession(['cart' => ['k' => ['product_id' => $p->id, 'product_name' => 'Cap', 'size' => 'Free Size', 'unit_price' => 59, 'quantity' => 1, 'image' => null]]])
            ->from('/checkout')
            ->post('/checkout', ['name' => 'Buyer', 'email' => 'b@example.com', 'phone' => '0100000000', 'address' => '1 Jalan', 'city' => 'KL', 'state' => 'Selangor', 'postcode' => '50000'])
            ->assertRedirect('/checkout')
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'not connected'));

        $this->assertSame(0, Order::count());
    }
}
