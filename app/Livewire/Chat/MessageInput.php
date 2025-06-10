<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Message;
use App\Models\Chat;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Events\MessageSent;
use App\Events\GroupMessageSent;
use Illuminate\Support\Facades\Auth;

class MessageInput extends Component
{
    use WithFileUploads;

    public $chatId;
    public $chatType = 'private';
    public $body = '';
    public $image;

    protected $rules = [
        'body' => 'nullable|string|max:1000',
        'image' => 'nullable|image|max:10240', // 10MB max
    ];

    public function sendMessage()
    {
        $this->validate();

        if (!$this->chatId) return;

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('chat-images', 'public');
        }

        if ($this->chatType === 'private') {
            $chat = Chat::findOrFail($this->chatId);

            $message = Message::create([
                'chat_id' => $this->chatId,
                'user_id' => Auth::id(),
                'body'    => $this->body,
                'image'   => $imagePath,
            ]);

            event(new MessageSent($message));
        } elseif ($this->chatType === 'group') {
            $groupChat = GroupChat::findOrFail($this->chatId);

            $message = GroupMessage::create([
                'group_chat_id' => $this->chatId,
                'sender_id'     => Auth::id(),
                'body'          => $this->body,
                'image'         => $imagePath,
            ]);

            event(new GroupMessageSent($message));
        } else {
            return;
        }

        $this->body = '';
        $this->image = null;

        $this->dispatch('messageSent', chatId: $this->chatId);
    }

    public function render()
    {
        return view('livewire.chat.message-input');
    }
}
