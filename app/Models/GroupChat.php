<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// GroupChat.php
class GroupChat extends Model
{
    protected $fillable = ['name', 'owner_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_chat_user');
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_chat_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}


