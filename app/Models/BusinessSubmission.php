<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\BusinessSubmissionsFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'location',
        'owner_name',
        'description',
        'sector_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
