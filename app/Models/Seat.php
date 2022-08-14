<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $color
 * @property bool $is_available
 * @property int $row
 * @property int $place
 * @property int $x
 * @property int $y
 * @property int $hall_id
 *
 * @property Hall $hall
 */
class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'color', 'is_available', 'row', 'place', 'x',
        'y', 'hall_id'
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id', 'id');
    }

}
