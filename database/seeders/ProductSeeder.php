<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo catalogue: "Drop 01 · Simpang", a fictional KL streetwear capsule.
 * Every piece carries one small embroidered red dot. Photos are AI-generated concept images.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $apparel = ['S', 'M', 'L', 'XL', 'XXL'];

        $products = [
            ['Dot Heavyweight Tee — Ink', 'dot-tee-ink', 79, 40, $apparel,
                '240gsm combed cotton, boxy fit, dropped shoulders and a thick ribbed collar. One red dot, embroidered on the left chest.'],
            ['Dot Heavyweight Tee — Bone', 'dot-tee-bone', 79, 4, $apparel,
                '240gsm combed cotton in an undyed bone tone. Boxy fit, dropped shoulders, thick ribbed collar. Red dot on the left chest.'],
            ['Simpang Boxy Hoodie — Charcoal', 'simpang-hoodie-charcoal', 169, 18, $apparel,
                'Brushed-back fleece, cropped boxy body, kangaroo pocket and no drawcords. Cut short enough for KL afternoons.'],
            ['Simpang Boxy Hoodie — Ash', 'simpang-hoodie-ash', 169, 12, $apparel,
                'The Simpang hoodie in heather ash. Brushed-back fleece, boxy body, kangaroo pocket, red dot on the chest.'],
            ['LRT Coach Jacket — Black', 'lrt-coach-jacket', 229, 9, $apparel,
                'Matte nylon shell with a light mesh lining and snap front. Throw it on for the last train home.'],
            ['Pasar Cargo Pants — Olive', 'pasar-cargo-olive', 159, 20, ['28', '30', '32', '34', '36'],
                'Cotton ripstop, relaxed straight leg, two bellows cargo pockets. Red dot on the side pocket flap.'],
            ['Monsoon Windbreaker — Brick', 'monsoon-windbreaker-red', 199, 3, $apparel,
                'Water-repellent nylon half-zip with a stowable hood and elastic cuffs. Built for the 5pm downpour.'],
            ['Dot 6-Panel Cap — Ink', 'dot-cap-ink', 59, 35, ['Free Size'],
                'Unstructured washed cotton twill, curved brim, adjustable strap. Red dot on the front panel.'],
            ['Ribbed Beanie — Bone', 'ribbed-beanie-bone', 45, 25, ['Free Size'],
                'Chunky rib knit with a folded cuff. For air-conditioned malls and Cameron weekends.'],
            ['Canvas Tote — Natural', 'canvas-tote-natural', 39, 50, ['Free Size'],
                'Heavy unbleached canvas with black webbing handles, sized for a laptop and a kopi bungkus.'],
        ];

        foreach ($products as [$name, $image, $price, $stock, $sizes, $description]) {
            Product::create([
                'name'        => $name,
                'slug'        => Str::slug($name),
                'description' => $description,
                'price'       => $price,
                'stock'       => $stock,
                'sizes'       => $sizes,
                'images'      => ["images/products/{$image}.jpg"],
                'is_active'   => true,
            ]);
        }
    }
}
