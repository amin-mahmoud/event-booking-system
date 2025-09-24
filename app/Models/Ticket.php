<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'type', 'price', 'quantity', 'event_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getAvailableQuantityAttribute()
    {
        $bookedQuantity = $this->bookings()->where('status', '!=', 'cancelled')->sum('quantity');
        return $this->quantity - $bookedQuantity;
    }
}
