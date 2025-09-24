<?php

namespace App\Models;

use App\Traits\CommonQueryScopes;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use  CommonQueryScopes;

    protected $fillable = [
        'title', 'description', 'date', 'location', 'created_by'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Ticket::class);
    }
}
