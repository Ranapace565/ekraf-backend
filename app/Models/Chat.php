<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
