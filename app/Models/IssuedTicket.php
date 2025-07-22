<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuedTicket extends Model
{
    protected $fillable = [
        'order_item_id', 'owner_id', 'ticket_code', 'is_used'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
