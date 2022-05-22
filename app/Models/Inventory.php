<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'survivor_id',
        'food',
        'water',
        'medication',
        'ammunition'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
