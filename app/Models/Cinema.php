<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property string $title
 * @property string $address
 *
 * @property Collection $halls
 */
class Cinema extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'address'
    ];

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class, 'cinema_id', 'id');
    }

}
