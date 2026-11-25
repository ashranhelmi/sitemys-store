<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Local demo admin only. On a real server create your own with `php artisan store:admin you@domain`.
        if (app()->environment('local', 'testing')) {
            User::factory()->create([
                'name'     => 'Demo Admin',
                'email'    => 'admin@example.com',
                'password' => 'password',
                'is_admin' => true,
            ]);
        }

        $this->call([
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
