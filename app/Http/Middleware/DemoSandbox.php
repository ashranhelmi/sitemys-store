<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public demo: every visitor who does something (add to cart, checkout, Admin switch) gets a private
 * copy of the seeded SQLite database, so nobody sees anyone else's changes. Visitors who only look
 * read the shared seed, read-only, and get no copy. Same idea as the property demo (ren-demo-runtime).
 * Does nothing unless DEMO_MODE=true.
 */
class DemoSandbox
{
    private const MAX_COPIES = 500;

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        // Billplz is not connected in the demo; nothing should arrive on its callback addresses.
        abort_if($request->routeIs('billplz.webhook', 'billplz.callback'), 404);

        $template = config('demo.template');
        abort_unless(is_file($template), 503, 'The demo is being prepared.');

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
        $hasCopy = is_string($id) && preg_match('/^[a-f0-9]{32}$/D', $id);

        if (! $hasCopy && $request->isMethodSafe()) {
            // Just looking: shared seed, read-only, no copy. Crawlers and cookie-less bots therefore
            // cannot fill the copy limit and lock real visitors out (27 Nov review).
            $this->useDatabase($template);
            DB::connection('sqlite')->statement('PRAGMA query_only = ON');

            return $this->finish($request, $next);
        }

        if (! $hasCopy) {
            // First real action. It must come from a browser that already holds our session cookie,
            // i.e. one that opened a demo page first.
            abort_unless($request->hasCookie(config('session.cookie')), 419, 'Please open the demo first.');
            abort_if(count($files) - $removed >= self::MAX_COPIES, 503, 'The demo is busy. Please try again later.');
            $id = bin2hex(random_bytes(16));
            $request->session()->put('demo_database', $id);
        }

        $database = $directory.'/'.$id.'.sqlite';
        if (! is_file($database)) {
            $temporary = $database.'.'.bin2hex(random_bytes(4)).'.tmp';
            copy($template, $temporary);
            chmod($temporary, 0600);
            rename($temporary, $database);
        }
        touch($database);
        $this->useDatabase($database);

        if ($request->allFiles()) {
            return back()->with('error', 'Uploads are turned off in the demo.');
        }
        // Account recovery, password change and deleting the demo admin are off.
        if (! $request->isMethodSafe() && (
            preg_match('~(forgot-password|reset-password|verify-email|confirm-password)~', $request->path())
            || $request->routeIs('profile.destroy', 'password.update')
        )) {
            return back()->with('error', 'Turned off in the demo.');
        }

        return $this->finish($request, $next);
    }

    private function useDatabase(string $file): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $file,
            // Emails are built and thrown away (array mailer): nothing is sent or written to disk,
            // so an address a visitor types at checkout never leaves their private copy.
            'mail.default' => 'array',
        ]);
        DB::purge('sqlite');
    }

    private function finish(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');

        return $response;
    }
}
