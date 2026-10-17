<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function vehicleRates()
    {
        return $this->hasMany(FacilityVehicleRate::class);
    }

    public function availabilities()
    {
        return $this->hasMany(FacilityAvailability::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilityUpdates()
    {
        return $this->hasMany(AvailabilityUpdate::class);
    }
}
