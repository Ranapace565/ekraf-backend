<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($Article) {
            $baseSlug = Str::slug($Article->title);
            $slug = $baseSlug;
            $count = 1;

            while (Article::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $Article->slug = $slug;
            // $Article->slug = Str::slug($Article->title);

        });

        static::updating(function ($Article) {
            $baseSlug = Str::slug($Article->title);
            $slug = $baseSlug;
            $count = 1;

            while (Article::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $Article->slug = $slug;
        });
    }

    protected $fillable = ['user_id', 'title', 'slug', 'content', 'thumbnail'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
