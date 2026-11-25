<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('is_admin', false)->withCount('orders')
            ->with(['orders' => fn($q) => $q->latest()->take(1)])
            ->latest()
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'orders_count' => $u->orders_count,
                'last_order'   => $u->orders->first()?->created_at->format('d M Y'),
                'joined'       => $u->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/Customers/Index', ['customers' => $customers]);
    }
}
