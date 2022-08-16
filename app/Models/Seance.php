<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $cinema_id
 * @property int $hall_id
 * @property int $premiere_id
 * @property array $format_ids
 * @property string $start_date_time
 *
 * @property Cinema $cinema
 * @property Hall $hall
 * @property Premiere $premiere
 * @property Collection $seats
 */
class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'cinema_id', 'hall_id', 'premiere_id', 'format_ids',
        'start_date_time'
    ];

    protected $casts = [
        'format_ids' => 'array'
    ];

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class , 'cinema_id', 'id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class ,'hall_id', 'id');
    }

    public function premiere(): BelongsTo
    {
        return $this->belongsTo(Premiere::class , 'premiere_id', 'id');
    }

    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(Seat::class)->using(SeanceSeat::class)->withPivot('price');
    }

}
