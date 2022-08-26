<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'cinema_id', 'hall_id', 'premiere_id', 'format_id',
        'start_date_time', 'start_date', 'end_date_time', 'prices'
    ];

    protected $casts = [
        'prices' => 'array'
    ];

    protected $with = ['format:id,title'];

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
        return $this->belongsToMany(Seat::class)->using(SeanceSeat::class)->withPivot('price', 'is_available')->withTimestamps();
    }

    public function format(): BelongsTo
    {
        return $this->belongsTo(Format::class, 'format_id', 'id');
    }

    public function scopeUpcoming(Builder $builder)
    {
        return $builder->where('start_date_time', '>', now()->subMinutes(30)->format('Y-m-d H:i'));
    }

}
