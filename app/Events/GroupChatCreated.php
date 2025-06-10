<?php

namespace App\Events;

use App\Models\GroupChat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GroupChatCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $groupChat;

    /**
     * Create a new event instance.
     */
    public function __construct(GroupChat $groupChat)
    {
        $this->groupChat = $groupChat->load('users'); // eager load users
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcast on private user channels for all group members.
     */
    public function broadcastOn()
    {
        // Return an array of PrivateChannels, one for each user in the group
        return $this->groupChat->users->map(function ($user) {
            return new PrivateChannel('user.' . $user->id);
        })->toArray();
    }

    /**
     * Data sent with the broadcast.
     */
    public function broadcastWith()
    {
        return [
            'group_chat_id' => $this->groupChat->id,
            'name' => $this->groupChat->name,
            'owner_id' => $this->groupChat->owner_id,
            // You can add more info if needed
        ];
    }

    public function broadcastAs()
    {
        return 'GroupChatCreated';
    }
}
