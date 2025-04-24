<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    /** @use HasFactory<\Database\Factories\BusinessFactory> */
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($business) {
            $baseSlug = Str::slug($business->business_name);
            $slug = $baseSlug;
            $count = 1;

            while (Business::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $business->slug = $slug;
            // $business->slug = Str::slug($business->business_name);

        });

        static::updating(function ($business) {
            $baseSlug = Str::slug($business->business_name);
            $slug = $baseSlug;
            $count = 1;

            while (Business::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $business->slug = $slug;
        });
    }

    protected $fillable = [
        'user_id',
        'sector_id',
        'business_name',
        'slug',
        'owner_name',
        'proof_photo',
        'description',
        'location',
        'latitude',
        'longitude',
        'instagram',
        'facebook',
        'tiktok',
        'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
