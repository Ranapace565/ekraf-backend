<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatThread extends Model
{
    protected $fillable = ['entrepreneur_id', 'visitor_id'];

    public function messages()
    {
        return $this->hasMany(Chat::class, 'thread_id');
    }

    public function entrepreneur()
    {
        return $this->belongsTo(User::class, 'entrepreneur_id');
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }
}
