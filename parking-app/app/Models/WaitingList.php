<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaitingList extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'waiting_list';

    protected $fillable = [
        'user_id',
        'parking_spot_id',
        'position',
        'requested_at',
        'status',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parkingSpot()
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    public function isWaiting()
    {
        return $this->status === 'waiting';
    }

    public static function getNextPosition()
    {
        return static::where('status', 'waiting')->max('position') + 1;
    }

    public function remove()
    {
        $this->update(['status' => 'removed']);
        static::where('position', '>', $this->position)
            ->where('status', 'waiting')
            ->decrement('position');
    }
}
