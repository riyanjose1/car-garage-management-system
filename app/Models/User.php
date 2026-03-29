<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function vehicles()
    {
        return $this->hasMany(\App\Models\Vehicle::class);
    }

    public function appointments()
    {
        return $this->hasMany(\App\Models\Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
