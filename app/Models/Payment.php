<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'billplz_bill_id', 'amount', 'status', 'paid_at', 'billplz_data',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'paid_at'      => 'datetime',
        'billplz_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
