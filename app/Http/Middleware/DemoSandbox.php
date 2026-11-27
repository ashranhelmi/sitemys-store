<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public demo: every browser session gets its own private copy of the seeded SQLite database,
 * so visitors can buy, edit products and move orders without seeing each other's changes.
 * Same pattern as the property demo (ren-demo-runtime). Does nothing unless DEMO_MODE=true.
 */
class DemoSandbox
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        $directory = storage_path('app/demo-sessions');
        File::ensureDirectoryExists($directory, 0700);

        // Bounded cleanup of expired copies (24 h idle). Never touches the template.
        $files = glob($directory.'/*.sqlite') ?: [];
        $removed = 0;
        foreach ($files as $file) {
            if ($removed >= 10) break;
            if (filemtime($file) < time() - 86400) { @unlink($file); $removed++; }
        }

        $id = $request->session()->get('demo_database');
        if (! is_string($id) || ! preg_match('/^[a-f0-9]{32}$/D', $id)) {
            abort_if(count($files) - $removed >= 500, 503, 'The demo is busy. Please try again later.');
            $id = bin2hex(random_bytes(16));
            $request->session()->put('demo_database', $id);
        }

        $database = $directory.'/'.$id.'.sqlite';
        if (! is_file($database)) {
            abort_unless(is_file(config('demo.template')), 503, 'The demo is being prepared.');
            $temporary = $database.'.'.bin2hex(random_bytes(4)).'.tmp';
            copy(config('demo.template'), $temporary);
            chmod($temporary, 0600);
            rename($temporary, $database);
        }
        touch($database);

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $database,
            // Demo: emails are built and thrown away (array mailer). Nothing is sent or written to disk,
            // so an address a visitor types at checkout never leaves their private copy.
            'mail.default' => 'array',
        ]);
        DB::purge('sqlite');

        if ($request->allFiles()) {
            return back()->with('error', 'Uploads are turned off in the demo.');
        }
        // Account recovery and deleting the demo admin would only break the visitor's own copy.
        if (! $request->isMethod('GET') && (
            preg_match('~(forgot-password|reset-password|verify-email|confirm-password)~', $request->path())
            || $request->routeIs('profile.destroy', 'password.update')
        )) {
            return back()->with('error', 'Turned off in the demo.');
        }

        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');

        return $response;
    }
}
