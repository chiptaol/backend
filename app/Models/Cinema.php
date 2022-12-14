<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Cinema extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'address', 'reference_point', 'longitude', 'latitude', 'logo_id', 'phone'
    ];

    protected $with = ['logo:id,path'];

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class, 'cinema_id', 'id');
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(FileSource::class, 'logo_id', 'id');
    }

    public function premieres(): HasMany
    {
        return $this->hasMany(Premiere::class, 'cinema_id', 'id');
    }

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class, 'cinema_id', 'id');
    }

}
