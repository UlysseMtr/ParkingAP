<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingSpot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function currentReservation()
    {
        return $this->hasOne(Reservation::class)->where('status', 'active')->latest();
    }

    public function isAvailable()
    {
        return $this->is_active && !$this->currentReservation()->exists();
    }
}
