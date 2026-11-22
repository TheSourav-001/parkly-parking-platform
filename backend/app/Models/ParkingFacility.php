<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingFacility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'area_id',
        'thana_id',
        'district_id',
        'division_id',
        'name',
        'slug',
        'address_text',
        'latitude',
        'longitude',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function area()
    {
        return $this->belongsTo(Location::class, 'area_id');
    }

    public function thana()
    {
        return $this->belongsTo(Location::class, 'thana_id');
    }

    public function district()
    {
        return $this->belongsTo(Location::class, 'district_id');
    }

    public function division()
    {
        return $this->belongsTo(Location::class, 'division_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function vehicleRates()
    {
        return $this->hasMany(FacilityVehicleRate::class);
    }

    public function availabilities()
    {
        return $this->hasMany(FacilityAvailability::class);
    }

    public function availabilityUpdates()
    {
        return $this->hasMany(AvailabilityUpdate::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
