<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymSocialLinks extends Model
{
    protected $fillable = ['gym_id', 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
}
