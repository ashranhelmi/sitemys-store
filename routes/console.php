<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('store:admin {email} {--name=Admin}', function (string $email) {
    $password = $this->secret('Password (min 8 characters)');
    if (strlen((string) $password) < 8) {
        $this->error('Password must be at least 8 characters.');
        return 1;
    }
    $user = \App\Models\User::updateOrCreate(['email' => $email], ['name' => $this->option('name'), 'password' => $password]);
    $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();
    $this->info("Admin ready: {$email}");
})->purpose('Create or promote a store admin account');
