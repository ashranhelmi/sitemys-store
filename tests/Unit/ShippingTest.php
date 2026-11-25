<?php

namespace Tests\Unit;

use App\Support\Shipping;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    public function test_flat_fee_below_threshold_and_free_from_it(): void
    {
        config(['store.shipping_flat' => 10, 'store.free_shipping_over' => 150]);

        $this->assertSame(10.0, Shipping::for(149.99));
        $this->assertSame(0.0, Shipping::for(150));
        $this->assertSame(0.0, Shipping::for(526));
    }

    public function test_threshold_zero_means_always_flat(): void
    {
        config(['store.shipping_flat' => 12, 'store.free_shipping_over' => 0]);

        $this->assertSame(12.0, Shipping::for(999));
    }
}
