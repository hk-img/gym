<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignPT extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function trainer(){
        return $this->belongsTo(User::class, 'trainer_id', 'id');
    }

}
