<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'description', 'venue', 'start_time', 'end_time', 'status'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class, 'event_id');
    }
}
