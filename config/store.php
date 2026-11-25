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

    /*
     | "Shop the look" hero. Each look = a short clip (poster = its first frame) plus hotspots. w / h = clip size.
     | 'card' = optional still for the "Shop the look" section (falls back to the poster).
     | x / y are fractions of the source image (0–1), so pins stay on the garment at any crop.
     */
    'looks' => [
        [
            'key' => 'lrt', 'eyebrow' => 'Look 01 · LRT, blue hour', 'lines' => ['Dressed for', 'the last train'],
            'video' => 'videos/look-lrt.mp4', 'poster' => 'images/looks/look-lrt.jpg', 'card' => 'images/looks/lowangle-lrt.jpg', 'w' => 1664, 'h' => 1248,
            'pins' => [
                ['product' => 'dot-6-panel-cap-ink',        'x' => 0.796, 'y' => 0.046],
                ['product' => 'dot-heavyweight-tee-ink',    'x' => 0.808, 'y' => 0.254],
                ['product' => 'lrt-coach-jacket-black',     'x' => 0.680, 'y' => 0.378],
                ['product' => 'pasar-cargo-pants-olive',    'x' => 0.710, 'y' => 0.596],
            ],
        ],
        [
            'key' => 'monsoon', 'eyebrow' => 'Look 02 · Monsoon, 5pm', 'lines' => ['Made for', 'monsoon season'],
            'video' => 'videos/look-monsoon.mp4', 'poster' => 'images/looks/look-monsoon.jpg', 'card' => 'images/looks/lowangle-monsoon.jpg', 'w' => 1920, 'h' => 1088,
            'pins' => [
                ['product' => 'ribbed-beanie-bone',         'x' => 0.728, 'y' => 0.052],
                ['product' => 'monsoon-windbreaker-brick',  'x' => 0.766, 'y' => 0.258],
                ['product' => 'pasar-cargo-pants-olive',    'x' => 0.676, 'y' => 0.611],
            ],
        ],
        [
            'key' => 'kopitiam', 'eyebrow' => 'Look 03 · Kopitiam, 10am', 'lines' => ['Kopi first,', 'then the city'],
            'video' => 'videos/look-kopitiam.mp4', 'poster' => 'images/looks/look-kopitiam.jpg', 'card' => 'images/looks/lowangle-kopitiam.jpg', 'w' => 1664, 'h' => 1248,
            'pins' => [
                ['product' => 'dot-6-panel-cap-ink',        'x' => 0.758, 'y' => 0.044],
                ['product' => 'simpang-boxy-hoodie-ash',    'x' => 0.804, 'y' => 0.241],
                ['product' => 'canvas-tote-natural',        'x' => 0.650, 'y' => 0.458],
            ],
        ],
    ],

    'demo'         => env('STORE_DEMO', true),
];
