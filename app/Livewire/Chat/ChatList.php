<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Chat;
use App\Models\User;
use App\Models\GroupChat;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{
    public $chats = [];

    public $showAddUser = false;
    public $newUserEmail;

    protected $listeners = [
        'messageSent' => 'loadChats',
        'chatAdded' => 'loadChats',
        'group-created' => 'loadChats', 
        
    ];

    public function mount()
    {
        $this->loadChats();
    }

    public function loadChats()
    {
        $userId = Auth::id();

        // Personal (1-to-1) chats
        $personalChats = Chat::where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->with([
                'messages' => fn($q) => $q->latest()->limit(1),
                'user1',
                'user2',
            ])
            ->get()
            ->map(function ($chat) {
                $chat->chat_type = 'private';
                return $chat;
            });

        // Group chats where user is a member
        $groupChats = GroupChat::whereHas('users', fn($q) => $q->where('users.id', $userId))
            ->with([
                'messages' => fn($q) => $q->latest()->limit(1),
                'users',
            ])
            ->get()
            ->map(function ($chat) {
                $chat->chat_type = 'group';
                return $chat;
            });

        // Merge all chats, sort by last message time
        $this->chats = collect($personalChats)
            ->merge($groupChats)
            ->sortByDesc(fn($chat) => optional($chat->messages->first())->created_at)
            ->values();
    }

    public function openChat($chatId, $chatType = 'private')
    {
        $this->dispatch('chatSelected', [
            'chatId' => $chatId,
            'chatType' => $chatType,
        ]);
    }

    public function startChatWithEmail()
    {
        $targetUser = User::where('email', $this->newUserEmail)->first();

        if (!$targetUser) {
            $this->addError('newUserEmail', 'User not found.');
            return;
        }

        if ($targetUser->id === Auth::id()) {
            $this->addError('newUserEmail', 'You cannot chat with yourself.');
            return;
        }

        // Check if chat already exists
        $existing = Chat::where(function ($query) use ($targetUser) {
                $query->where('user1_id', Auth::id())
                      ->where('user2_id', $targetUser->id);
            })
            ->orWhere(function ($query) use ($targetUser) {
                $query->where('user1_id', $targetUser->id)
                      ->where('user2_id', Auth::id());
            })
            ->first();

        if (!$existing) {
            $chat = Chat::create([
                'user1_id' => Auth::id(),
                'user2_id' => $targetUser->id,
            ]);

            $this->dispatch('chatAdded');
        } else {
            $chat = $existing;
        }

        $this->newUserEmail = '';
        $this->showAddUser = false;

        $this->loadChats();

        $this->dispatch('chatSelected', [
            'chatId' => $chat->id,
            'chatType' => 'private',
        ]);
    }

    public function render()
    {
        return view('livewire.chat.chat-list');
    }
}
