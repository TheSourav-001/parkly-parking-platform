<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityVehicleRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_facility_id',
        'vehicle_type_id',
        'rate_amount',
        'rate_unit',
        'min_charge_amount',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate_amount'       => 'decimal:2',
            'min_charge_amount' => 'decimal:2',
            'is_active'         => 'boolean',
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
}
