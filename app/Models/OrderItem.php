<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'ticket_type_id', 'quantity', 'price_per_ticket'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id'); }
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }
    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class, 'order_item_id');
    }
}
