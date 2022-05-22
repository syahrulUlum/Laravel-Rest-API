<?php

namespace App\Models;

use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survivor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'age',
        'gender',
        'last_location',
        'inventory',
        'is_infected'
    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function trade()
    {
        return $this->hasOne(Trade::class);
    }

    public function getIsInfectedAttribute($value)
    {
        if ($value == 1) {
            return true;
        } else {
            return false;
        }
    }
}
