<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'transaction_reference',
        'provider',
        'provider_transaction_id',
        'amount',
        'currency',
        'payment_status',
        'payment_method',
        'gateway_payload',
        'initiated_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'          => 'decimal:2',
            'gateway_payload' => 'array',
            'initiated_at'    => 'datetime',
            'verified_at'     => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
