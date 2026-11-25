<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUSES = ['pending', 'paid', 'processing', 'packed', 'failed', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded', 'expired'];

    protected $fillable = [
        'order_number', 'user_id',
        'customer_name', 'customer_email', 'customer_phone',
        'address', 'city', 'state', 'postcode',
        'subtotal', 'shipping', 'total',
        'status', 'billplz_bill_id',
        'courier', 'tracking_number', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /**
     * Order pages for the buyer use signed links: the order id alone must not open
     * someone else's name, email and address, or start a payment for their order.
     */
    public function thankYouUrl(): string { return \Illuminate\Support\Facades\URL::signedRoute('checkout.thank-you', $this); }
    public function retryUrl(): string    { return \Illuminate\Support\Facades\URL::signedRoute('payment.retry', $this); }
    public function renewUrl(): string    { return \Illuminate\Support\Facades\URL::signedRoute('payment.renew', $this); }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public static function generateOrderNumber(): string
    {
        return config('store.order_prefix') . '-' . strtoupper(uniqid());
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'warning',
            'paid'       => 'info',
            'processing' => 'info',
            'packed'     => 'info',
            'shipped'    => 'primary',
            'delivered'  => 'success',
            'completed'  => 'success',
            'cancelled'  => 'destructive',
            'refunded'   => 'destructive',
            'failed'     => 'destructive',
            'expired'    => 'secondary',
            default      => 'secondary',
        };
    }
}
