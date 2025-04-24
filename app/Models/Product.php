<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductsFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'price',
        'detail',
        'photo',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
