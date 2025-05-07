<?php

namespace App\Models;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Equipment extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = ['id'];
}
