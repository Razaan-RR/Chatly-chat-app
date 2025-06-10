<?php

namespace App\Events;

use App\Models\MessageReaction;
use App\Models\GroupMessageReaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageReacted implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $reaction;

    public function __construct(MessageReaction|GroupMessageReaction $reaction)
    {
        $this->reaction = $reaction->load('user');
    }

    public function broadcastOn(): Channel
    {
        $message = $this->reaction->message;

        if (isset($message->chat_id)) {
            return new PrivateChannel('chat.' . $message->chat_id);
        } elseif (isset($message->group_chat_id)) {
            return new PrivateChannel('group.' . $message->group_chat_id);
        }

        return new PrivateChannel('unknown');
    }

    public function broadcastAs(): string
    {
        return 'message.reacted';
    }
}
