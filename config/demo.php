<?php

// Public demo mode (branch demo/public-preview only). Off unless DEMO_MODE=true.
return [
    'enabled'  => (bool) env('DEMO_MODE', false),
    // Seed database every visitor's private copy starts from. Built by tools/build-demo-template.sh, never from real data.
    'template' => env('DEMO_TEMPLATE', database_path('demo-template.sqlite')),
    'examples_url' => env('DEMO_EXAMPLES_URL', 'https://studio.sitemys.com/examples/'),
    'source_url'   => env('DEMO_SOURCE_URL', 'https://github.com/ashranhelmi/sitemys-store'),
];
