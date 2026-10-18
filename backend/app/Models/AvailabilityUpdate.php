<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilityUpdate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'parking_facility_id',
        'vehicle_type_id',
        'old_available_slots',
        'new_available_slots',
        'change_reason',
        'updated_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_available_slots' => 'integer',
            'new_available_slots' => 'integer',
            'created_at'          => 'datetime',
        ];
    }

    public function parkingFacility()
    {
        return $this->belongsTo(ParkingFacility::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
