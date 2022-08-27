<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'seance_id', 'seat_id'
    ];
}
