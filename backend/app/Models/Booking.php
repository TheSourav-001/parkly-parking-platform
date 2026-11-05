<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'parking_facility_id',
        'vehicle_type_id',
        'vehicle_plate_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'planned_start_at',
        'duration_minutes',
        'planned_end_at',
        'unit_rate',
        'rate_unit',
        'base_amount',
        'total_amount',
        'currency',
        'booking_status',
        'payment_status',
        'capacity_held',
        'hold_expires_at',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'planned_start_at' => 'datetime',
            'duration_minutes' => 'integer',
            'planned_end_at'   => 'datetime',
            'unit_rate'        => 'decimal:2',
            'base_amount'      => 'decimal:2',
            'total_amount'     => 'decimal:2',
            'capacity_held'    => 'boolean',
            'hold_expires_at'  => 'datetime',
            'confirmed_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
            'completed_at'     => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parkingFacility()
    {
        return $this->belongsTo(ParkingFacility::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function adminNotifications()
    {
        return $this->hasMany(AdminNotification::class);
    }
}
