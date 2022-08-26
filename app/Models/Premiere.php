<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Premiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id', 'release_date', 'cinema_id', 'release_end_date'
    ];

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class, 'premiere_id', 'id');
    }

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class, 'cinema_id', 'id');
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }

    public function scopeActual(Builder $builder)
    {
        return $builder->where('release_end_date', '>=', now()->format('Y-m-d'));
    }

    public function scopeNotActual(Builder $builder)
    {
        return $builder->where('release_end_date', '<', now()->format('Y-m-d'));
    }
}
