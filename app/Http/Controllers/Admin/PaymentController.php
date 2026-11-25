<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('order')
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'              => $p->id,
                'billplz_bill_id' => $p->billplz_bill_id,
                'order_number'    => $p->order->order_number,
                'customer_name'   => $p->order->customer_name,
                'amount'          => $p->amount,
                'status'          => $p->status,
                'paid_at'         => $p->paid_at?->format('d M Y, H:i'),
                'created_at'      => $p->created_at->format('d M Y, H:i'),
            ]);

        return Inertia::render('Admin/Payments/Index', ['payments' => $payments]);
    }
}
