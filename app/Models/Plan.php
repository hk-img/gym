<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'duration',
        'price',
        'created_by',
        'status'
    ];

    // Accessor

    public function getNameAttribute($value){
        return ucfirst($value);
    }

    // public function price(): Attribute{
    //     return Attribute::make(
    //         get: fn ($value) => '₹ '.$value,
    //     );
    // }
}
