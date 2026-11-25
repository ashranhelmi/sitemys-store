<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_accounts_cannot_open_the_admin(): void
    {
        // checkout creates accounts like this one for every buyer
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
        $this->actingAs($customer)->get('/dashboard/admin/orders')->assertRedirect('/login');
    }

    public function test_admins_can_open_the_admin(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertOk();
    }

    public function test_public_registration_is_closed(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'x', 'email' => 'x@example.com', 'password' => 'password', 'password_confirmation' => 'password'])->assertNotFound();
    }

    public function test_storefront_is_the_home_page(): void
    {
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $this->get('/')->assertOk()->assertSee('The drop', false);
    }
}
