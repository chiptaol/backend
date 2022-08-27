<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'seance_id', 'movie_id', 'user_id', 'cinema_title', 'hall_title', 'total_price', 'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(Seat::class, 'ticket_seat')->using(TicketSeat::class)->withTimestamps();
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }

    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class, 'seance_id', 'id');
    }
}
