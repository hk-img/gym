<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = ['workout_id', 'exercise_name','days','description'];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}
