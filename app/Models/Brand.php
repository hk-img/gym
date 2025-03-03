<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Image\Enums\Fit;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes, HasSlug;

    protected $guarded = ['id'];

    public $registerMediaConversionsUsingModelInstance = true;

    public function registerMediaConversions(Media $media = null): void
    {
        if ($media->extension != 'webp') {
            $this
                ->addMediaConversion('webp')
                ->performOnCollections('images')
                ->nonQueued()
                ->format('webp');

            $this
                ->addMediaConversion('webp_small')
                ->width(70)
                ->height(70)
                ->sharpen(10)
                ->fit(Fit::Contain, 100, 100)
                ->performOnCollections('images')
                ->nonQueued()
                ->format('webp');

            $this
                ->addMediaConversion('webp_medium')
                ->width(300)
                ->height(300)
                ->performOnCollections('images')
                ->nonQueued()
                ->format('webp');

            $this
                ->addMediaConversion('webp_large')
                ->width(800)
                ->height(800)
                ->performOnCollections('images')
                ->nonQueued()
                ->format('webp');

            // Add more conversions as needed
        }
    }

    public function type(){
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function vehicle(){
        return $this->hasMany(Vehicle::class, 'brand_id', 'id');
    }

    public function price(){
        return $this->hasMany(ExShowroomPrice::class, 'brand_id', 'id');
    }

    public function usedVehicle(){
        return $this->hasMany(UsedVehicle::class, 'brand_id', 'id');
    }

    public function faqs()
    {
        return $this->morphMany(Faq::class, 'faqable');
    }

    public function delete()
    {
        // Soft delete related vehicles
        $this->vehicle()->each(function ($vehicle) {
            $vehicle->delete();
        });

        // Soft delete related showroom prices
        $this->price()->each(function ($price) {
            $price->delete();
        });

        // Perform soft delete on the type
        return parent::delete();
    }

    public function scopeActive($query){
        return $query->where('status',1);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'type.name']) // Include type name
            ->saveSlugsTo('slug')
            ->usingSeparator('-')
            ->doNotGenerateSlugsOnUpdate(); // Optional: prevent updating slug on name change
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = $model->generateSlug($model->name);
            }
        });
    }
}
