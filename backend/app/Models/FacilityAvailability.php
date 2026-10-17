<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_facility_id',
        'vehicle_type_id',
        'total_slots',
        'available_slots',
        'last_updated_by',
        'last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'total_slots'     => 'integer',
            'available_slots' => 'integer',
            'last_updated_at' => 'datetime',
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

    public function lastUpdatedByUser()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
