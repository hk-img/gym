<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'duration',
        'price',
        'status'
    ];

    public function setNameAttribute($value){
        $this->attributes['name'] = ucfirst($value);
    }

    public function getNameAttribute($value){
        return ucfirst($value);
    }
}
