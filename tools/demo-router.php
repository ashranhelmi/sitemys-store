<?php
// Local only: php -S 127.0.0.1:8766 tools/demo-router.php → http://127.0.0.1:8766/examples/store/
// Mimics the live setup (public_html/examples/store -> app/public symlink).
$prefix = '/examples/store';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/' ) { header('Location: '.$prefix.'/'); exit; }
if (! str_starts_with($path, $prefix)) { http_response_code(404); echo 'Outside the demo folder'; exit; } // a root-absolute link is a bug: fail loudly
$relative = substr($path, strlen($prefix));
$public = realpath(__DIR__.'/../public');
$file = realpath($public.rawurldecode($relative));
if ($file && is_file($file) && str_starts_with($file, $public.'/') && ! str_ends_with($file, '.php')) {
    $types = ['css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json', 'webp' => 'image/webp',
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'svg' => 'image/svg+xml', 'ico' => 'image/x-icon', 'woff2' => 'font/woff2',
        'mp4' => 'video/mp4', 'txt' => 'text/plain'];
    header('Content-Type: '.($types[pathinfo($file, PATHINFO_EXTENSION)] ?? 'application/octet-stream'));
    header('Content-Length: '.filesize($file));
    readfile($file);
    return;
}
$_SERVER['SCRIPT_NAME'] = $prefix.'/index.php';
$_SERVER['PHP_SELF'] = $prefix.'/index.php';
$_SERVER['SCRIPT_FILENAME'] = $public.'/index.php';
require $public.'/index.php';
