<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageReaction extends Model
{
    
    protected $table = 'message_reactions';

    protected $fillable = [
        'message_id',
        'user_id',
        'reaction',
    ];
    
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
