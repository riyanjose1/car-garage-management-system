<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'service_date',
        'service_time',
        'service_type',
        'notes',
        'status',
    ];

    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
