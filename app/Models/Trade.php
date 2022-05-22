<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'survivor_id',
        'req_food',
        'req_water',
        'req_medication',
        'req_ammunition',
        'not_trade'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
