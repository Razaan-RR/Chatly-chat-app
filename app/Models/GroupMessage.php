<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// GroupMessage.php
class GroupMessage extends Model
{
    protected $table = 'group_messages';

    protected $fillable = ['group_chat_id', 'sender_id', 'body', 'image'];

    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class, 'group_chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reactions()
    {
        return $this->hasMany(\App\Models\GroupMessageReaction::class, 'group_message_id');
    }
}
