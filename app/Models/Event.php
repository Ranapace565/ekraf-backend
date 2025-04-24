<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($Event) {
            $baseSlug = Str::slug($Event->title);
            $slug = $baseSlug;
            $count = 1;

            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $Event->slug = $slug;
            // $Event->slug = Str::slug($Event->title);

        });

        static::updating(function ($Event) {
            $baseSlug = Str::slug($Event->title);
            $slug = $baseSlug;
            $count = 1;

            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $Event->slug = $slug;
        });
    }

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'slug',
        'description',
        'event_date',
        'location',
        'latitude',
        'longitude',
        'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
