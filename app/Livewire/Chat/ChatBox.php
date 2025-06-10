<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Chat;
use App\Models\GroupChat;
use App\Models\User;
use App\Models\Message;
use App\Models\GroupMessage;
use App\Models\MessageReaction;
use App\Models\GroupMessageReaction;
use App\Events\MessageReacted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ChatBox extends Component
{
    public $chatId;
    public $chatType = 'private';
    public $selectedChat;
    public $showReactionPickerFor = null;

    public $showDropdown = false;
    public $showGroupCreator = false;
    public $newGroupEmail = '';
    public $groupEmails = [];
    public $groupName = '';

    protected $listeners = [
        'messageSent' => 'refreshMessages',
        // Removed 'select-chat' to avoid confusion
    ];

    public function mount($chatId = null, $chatType = 'private')
    {
        $this->chatId = $chatId;
        $this->chatType = $chatType;
        $this->loadChat();
    }

    // REMOVE hydrate() to avoid unnecessary reloads causing glitches
    // public function hydrate()
    // {
    //     $this->loadChat();
    // }

    public function refreshMessages($chatId)
    {
        if ($chatId == $this->chatId) {
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        $this->loadChat();
    }

    public function loadChat()
    {
        if (!$this->chatId) {
            $this->selectedChat = null;
            return;
        }

        if ($this->chatType === 'private') {
            $this->selectedChat = Chat::with(['messages.user', 'messages.reactions.user'])->find($this->chatId);
        } elseif ($this->chatType === 'group') {
            $this->selectedChat = GroupChat::with(['messages.sender', 'messages.reactions.user', 'users'])->find($this->chatId);
        }

        if ($this->selectedChat) {
            $this->markMessagesDelivered();
            $this->markMessagesSeen();
        }
    }

    protected function markMessagesDelivered()
    {
        $authUserId = Auth::id();
        $messages = $this->selectedChat->messages ?? collect();

        foreach ($messages as $message) {
            $senderId = $this->chatType === 'group' ? $message->sender_id : $message->user_id;

            if ($senderId !== $authUserId && !$message->delivered_at) {
                $message->delivered_at = Carbon::now();
                $message->save();
            }
        }
    }

    protected function markMessagesSeen()
    {
        $authUserId = Auth::id();
        $messages = $this->selectedChat->messages ?? collect();

        foreach ($messages as $message) {
            $senderId = $this->chatType === 'group' ? $message->sender_id : $message->user_id;

            if ($senderId !== $authUserId && !$message->seen_at) {
                $message->seen_at = Carbon::now();
                $message->save();
            }
        }
    }

    public function toggleReactionPicker($messageId)
    {
        $this->showReactionPickerFor = $this->showReactionPickerFor === $messageId ? null : $messageId;
    }

    public function react(int $messageId, string $emoji)
    {
        $userId = Auth::id();

        if ($this->chatType === 'private') {
            $message = Message::findOrFail($messageId);

            $reaction = MessageReaction::updateOrCreate(
                ['message_id' => $message->id, 'user_id' => $userId],
                ['reaction' => $emoji]
            );
        } else {
            $groupMessage = GroupMessage::findOrFail($messageId);

            $reaction = GroupMessageReaction::updateOrCreate(
                ['group_message_id' => $groupMessage->id, 'user_id' => $userId],
                ['reaction' => $emoji]
            );
        }

        $this->showReactionPickerFor = null;

        broadcast(new MessageReacted($reaction))->toOthers();

        $this->loadMessages();
    }

    // === Group Chat Related ===

    public function openGroupCreator()
    {
        $this->showDropdown = false;
        $this->showGroupCreator = true;
    }

    public function addGroupEmail()
    {
        $email = trim($this->newGroupEmail);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $this->groupEmails)) {
            $this->groupEmails[] = $email;
        }

        $this->newGroupEmail = '';
    }

    public function removeGroupEmail($email)
    {
        $this->groupEmails = array_filter($this->groupEmails, fn($e) => $e !== $email);
    }

    public function createGroup()
    {
        if (empty($this->groupName)) {
            $this->addError('groupName', 'Group name is required.');
            return;
        }

        $users = User::whereIn('email', $this->groupEmails)->get();

        if ($users->isEmpty()) {
            $this->addError('newGroupEmail', 'No valid users found.');
            return;
        }

        if ($this->selectedChat && $this->chatType === 'private') {
            $existingUsers = collect([$this->selectedChat->user1, $this->selectedChat->user2]);
            $users = $users->merge($existingUsers);
        }

        $group = GroupChat::create([
            'name' => $this->groupName,
            'owner_id' => Auth::id(),
        ]);

        $userIds = $users->pluck('id')->push(Auth::id())->unique()->toArray();
        $group->users()->attach($userIds);

        $this->dispatch('group-created');

        $this->dispatch('chatSelected', [
            'chatId' => $group->id,
            'chatType' => 'group',
        ]);

        $this->reset(['showGroupCreator', 'groupEmails', 'newGroupEmail', 'groupName']);
    }

    public function getChatPartnerProperty()
    {
        if (!$this->selectedChat || $this->chatType === 'group') {
            return null;
        }

        $authUserId = Auth::id();

        if ($this->selectedChat->user1_id === $authUserId) {
            return $this->selectedChat->user2;
        } else {
            return $this->selectedChat->user1;
        }
    }

    public function render()
    {
        return view('livewire.chat.chat-box', [
            'selectedChat' => $this->selectedChat,
            'chatPartner' => $this->chatPartner,
            'chatType' => $this->chatType,
        ]);
    }
}
