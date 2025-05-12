<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasApiTokens, Notifiable, HasRoles, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'username',
        'added_by',
        'address',
        'status',
        'otp',
        'time_slot',
        'pt_fees',
        'salary',
        'experience',
        'otp_sent_at',
        'start_date'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeExcludeSuperAdmin($query)
    {
        return $query->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Super Admin');
        });
    }

    // Accessors
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucwords($value),
        );
    }

    public function MembershipStatus(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
        );
    }

    // Relations

    public function assignPlan()
    {
        return $this->hasMany(AssignPlan::class, 'user_id', 'id')->with('plan');
    }
    
    public function assignPT()
    {
        return $this->hasMany(AssignPT::class, 'user_id', 'id')->with('trainer');
    }
    
    public function assignActivity()
    {
        return $this->hasMany(AssignPackage::class, 'user_id', 'id')->with('activity');
    }

    // A user can be added by another user (belongsTo self)
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    
    public function addedUsers()
    {
        return $this->hasMany(User::class, 'added_by');
    }
}
