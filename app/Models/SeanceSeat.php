<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SeanceSeat extends Pivot
{
    protected $fillable = [
        'price', 'status'
    ];

}
