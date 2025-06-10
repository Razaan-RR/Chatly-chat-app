<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\GroupChat;
use App\Models\Chat;
use App\Models\Message;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Direct chat relationships
    public function chatsAsUser1()
    {
        return $this->hasMany(Chat::class, 'user1_id');
    }

    public function chatsAsUser2()
    {
        return $this->hasMany(Chat::class, 'user2_id');
    }

    // Fetch all direct chats
    public function allChats()
    {
        return Chat::where('user1_id', $this->id)
                   ->orWhere('user2_id', $this->id)
                   ->get();
    }

    // Group chats
    public function groupChats()
    {
        return $this->belongsToMany(GroupChat::class, 'group_chat_user', 'user_id', 'group_chat_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path && file_exists(public_path('storage/' . $this->profile_photo_path))) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return asset('default-avatar.png');
    }
}
