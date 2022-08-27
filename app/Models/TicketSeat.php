<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketSeat extends Pivot
{
    protected $fillable = [
        'ticket_id', 'seat_id'
    ];
}
