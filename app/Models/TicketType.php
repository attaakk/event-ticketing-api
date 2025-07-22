<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $fillable = [
        'event_id', 'name', 'price', 'total_quantity', 'available_quantity'
    ];

    public function event()
    {
         return $this->belongsTo(Event::class, 'event_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'ticket_type_id');
    }
}
