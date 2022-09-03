<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'seance_id', 'movie_id', 'user_id', 'cinema_title', 'hall_title', 'total_price', 'status'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
           $ticket->{$ticket->getKeyName()} = Str::orderedUuid();
        });
    }

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
