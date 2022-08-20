<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $title
 * @property string $original_title
 * @property string $tmdb_id
 * @property string $description
 * @property array $genres
 * @property int $duration
 * @property string $tagline
 * @property array $actors
 * @property array $directors
 * @property string $trailer
 * @property string $poster
 * @property int $age_rating
 *
 * @property Premiere $premiere
 */
class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'original_title', 'tmdb_id', 'description', 'genres',
        'duration', 'tagline', 'actors', 'directors', 'trailer',
        'poster', 'age_rating'
    ];

    protected $casts = [
        'genres' => 'array',
        'actors' => 'array',
        'directors' => 'array',
    ];

    public function premiere(): HasOne
    {
        return $this->hasOne(Premiere::class , 'movie_id', 'id');
    }
}
