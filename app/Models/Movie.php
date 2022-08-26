<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'original_title', 'tmdb_id', 'description', 'genres',
        'duration', 'tagline', 'actors', 'directors', 'trailer_path',
        'poster_path', 'backdrop_path', 'age_rating', 'release_date',
        'countries', 'rating'
    ];

    protected $casts = [
        'genres' => 'array',
        'actors' => 'array',
        'directors' => 'array',
        'countries' => 'array'
    ];

    public function premieres(): HasMany
    {
        return $this->hasMany(Premiere::class , 'movie_id', 'id');
    }
}
