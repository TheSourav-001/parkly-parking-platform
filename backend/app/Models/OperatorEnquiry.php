<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorEnquiry extends Model
{
    protected $fillable = [
        'facility_name',
        'contact_name',
        'email',
        'phone',
        'location_zone',
        'address',
        'capacity',
        'notes',
        'ip_address',
        'status',
    ];
}
