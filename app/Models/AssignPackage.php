<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignPackage extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function activity(){
        return $this->belongsTo(Activity::class, 'package_id', 'id');
    }
}
