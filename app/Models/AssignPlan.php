<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignPlan extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Relations
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function plan(){
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    // Accessors
    public function MembershipStatus(): Attribute{
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
        );
    }
}
