<?php

// Storefront identity. Change these (or the env values) to make the store your own.
return [
    'name'         => env('STORE_NAME', 'Sitemys Store'),
    'tagline'      => env('STORE_TAGLINE', 'Streetwear from the junctions of KL.'),
    'email'        => env('STORE_EMAIL', 'hello@example.com'),
    'order_prefix' => env('STORE_ORDER_PREFIX', 'SMS'),
    'collection'   => env('STORE_COLLECTION', 'Drop 01 · Simpang'),
    'shipping_flat'      => (float) env('STORE_SHIPPING_FLAT', 10),
    'free_shipping_over' => (float) env('STORE_FREE_SHIPPING_OVER', 150),

    // Full-width crew band below the looks (set to null to hide it)
    'crew' => [
        'image'    => 'images/looks/crew.jpg',
        'lines'    => ['Three corners.', 'One dot'],
        'text'     => 'Drop 01 · Simpang. From the last train to the first kopi.',
    ],

    /*
     | "Shop the look" hero. Each look = a short clip (poster = its first frame) plus hotspots. Clips made in Google Flow (Veo) from the low-angle stills; short clips play twice. w / h = clip size.
     | 'card' = optional still for the "Shop the look" section (falls back to the poster).
     | x / y are fractions of the source image (0–1), so pins stay on the garment at any crop.
     */
    'looks' => [
        [
            'key' => 'lrt', 'eyebrow' => 'Look 01 · LRT, blue hour', 'lines' => ['Dressed for', 'the last train'],
            'video' => 'videos/look-lrt.mp4', 'poster' => 'images/looks/look-lrt.jpg', 'card' => 'images/looks/lowangle-lrt.jpg', 'w' => 1920, 'h' => 1080,
            'pins' => [
                ['product' => 'dot-6-panel-cap-ink', 'x' => 0.761, 'y' => 0.042],
                ['product' => 'dot-heavyweight-tee-ink', 'x' => 0.772, 'y' => 0.222],
                ['product' => 'lrt-coach-jacket-black', 'x' => 0.659, 'y' => 0.352],
                ['product' => 'pasar-cargo-pants-olive', 'x' => 0.686, 'y' => 0.519],
            ],
        ],
        [
            'key' => 'monsoon', 'eyebrow' => 'Look 02 · Monsoon, 5pm', 'lines' => ['Built for', 'the monsoon'],
            'video' => 'videos/look-monsoon.mp4', 'poster' => 'images/looks/look-monsoon.jpg', 'card' => 'images/looks/lowangle-monsoon.jpg', 'w' => 1920, 'h' => 1080,
            'pins' => [
                ['product' => 'ribbed-beanie-bone', 'x' => 0.691, 'y' => 0.042],
                ['product' => 'monsoon-windbreaker-brick', 'x' => 0.746, 'y' => 0.224],
                ['product' => 'pasar-cargo-pants-olive', 'x' => 0.652, 'y' => 0.507],
            ],
        ],
        [
            'key' => 'kopitiam', 'eyebrow' => 'Look 03 · Kopitiam, 10am', 'lines' => ['Kopi first,', 'then the city'],
            'video' => 'videos/look-kopitiam.mp4', 'poster' => 'images/looks/look-kopitiam.jpg', 'card' => 'images/looks/lowangle-kopitiam.jpg', 'w' => 1920, 'h' => 1080,
            'pins' => [
                ['product' => 'dot-6-panel-cap-ink', 'x' => 0.750, 'y' => 0.048],
                ['product' => 'simpang-boxy-hoodie-ash', 'x' => 0.781, 'y' => 0.230],
                ['product' => 'canvas-tote-natural', 'x' => 0.683, 'y' => 0.457],
            ],
        ],
    ],

    'demo'         => env('STORE_DEMO', true),
];
