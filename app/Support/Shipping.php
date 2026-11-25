<?php

namespace App\Support;

class Shipping
{
    /** Flat shipping fee, waived once the subtotal reaches the free-shipping threshold. */
    public static function for(float $subtotal): float
    {
        $freeOver = (float) config('store.free_shipping_over');

        return ($freeOver > 0 && $subtotal >= $freeOver) ? 0.0 : (float) config('store.shipping_flat');
    }
}
