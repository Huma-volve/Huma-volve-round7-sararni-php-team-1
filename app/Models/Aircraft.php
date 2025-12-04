<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aircraft extends Model
{
    use HasFactory;
    protected $table = 'aircrafts';
    public $timestamps = false;
    protected $fillable = [
        'model',
        'total_seats'
    ];

    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }
}

