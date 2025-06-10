<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessageReaction extends Model
{
    protected $fillable = ['group_message_id', 'user_id', 'reaction'];

    public function groupMessage()
    {
        return $this->belongsTo(GroupMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
