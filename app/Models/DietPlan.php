<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    protected $guarded = ['id'];

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Accessor
    public function dietPlanName():Attribute{
        return Attribute::make(
            get: fn($value) => ucwords($value)
        );
    }
}
