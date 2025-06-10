<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MessageReaction;

class Message extends Model
{
    protected $fillable = ['chat_id', 'user_id', 'body', 'image'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reactions()
    {
        return $this->hasMany(\App\Models\MessageReaction::class, 'message_id');
    }


    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class);
    }
}
