<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ChatPage extends Component
{
    public $selectedChatId = null;
    public $chatType = null;

    // Listen to only one event: chatSelected
    protected $listeners = ['chatSelected' => 'setSelectedChat'];

    public function setSelectedChat($chatId = null, $chatType = null)
    {
        $this->selectedChatId = $chatId;
        $this->chatType = $chatType;

        logger()->info('Chat selected', ['chatId' => $chatId, 'chatType' => $chatType]);
    }

    public function render()
    {
        /** @var \Livewire\Wireable|\Illuminate\View\View $view */
        $view = view('livewire.chat.chat-page', [
            'selectedChatId' => $this->selectedChatId,
            'chatType' => $this->chatType,
        ]);

        // @phpstan-ignore-next-line (if using PHPStan)
        // @intelephense-ignore-next-line (for Intelephense)
        return $view->layout('layouts.app');
    }
}
