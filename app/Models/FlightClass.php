<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_id',
        'class_id',
        'price_per_seat',
        'seats_available',
        'price_rules',
        'baggage_rules',
        'fare_conditions',
        'taxes_fees_breakdown',
        'refundable'
    ];

    protected $casts = [
        'price_rules' => 'array',
        'baggage_rules' => 'array',
        'fare_conditions' => 'array',
        'taxes_fees_breakdown' => 'array',
        'refundable' => 'boolean'
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}