<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'seats_quantity', 'description', 'is_vip', 'cinema_id'
    ];

    protected $casts = [
        'is_vip' => 'boolean'
    ];

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class, 'cinema_id', 'id');
    }

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class , 'hall_id', 'id');
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class ,'hall_id', 'id');
    }

    public function formats(): BelongsToMany
    {
        return $this->belongsToMany(Format::class, 'hall_format')->using(HallFormat::class)->withTimestamps();
    }


}
