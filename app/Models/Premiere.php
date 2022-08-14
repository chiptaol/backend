<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $movie_id
 *
 * @property Collection $seances
 */
class Premiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id'
    ];

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class, 'premiere_id', 'id');
    }
}
