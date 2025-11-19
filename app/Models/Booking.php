<?php

namespace App\Models;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_id',
        'total_price',
        'status',
        'payment_status',
        'start_date',
        'end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
