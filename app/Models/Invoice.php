<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'user_id',
        'invoice_number',
        'labor_cost',
        'parts_cost',
        'total_amount',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {

            // If already set, don't overwrite
            if (!empty($invoice->invoice_number)) {
                return;
            }

            // Example: INV-20260220-0001
            $today = now()->format('Ymd');

            $countToday = self::whereDate('created_at', today())->count() + 1;

            $sequence = str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);

            $invoice->invoice_number = "INV-{$today}-{$sequence}";
        });
    }

    public function appointment()
    {
        return $this->belongsTo(\App\Models\Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}