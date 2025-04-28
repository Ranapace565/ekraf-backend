<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosialMedia extends Model
{
    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
