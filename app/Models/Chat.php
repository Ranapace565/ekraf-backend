<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'thread_id',
        'sender_id',
        'message',
        'is_read'
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function thread()
    {
        return $this->belongsTo(ChatThread::class, 'thread_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
