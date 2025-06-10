<div class="flex h-screen">

    {{-- Left Sidebar (Chat List) --}}
    <aside class="w-80 border-r border-gray-300 flex flex-col">
        <livewire:chat.chat-list />
    </aside>

    {{-- Right Main Chat Area --}}
    <main class="flex-1 flex flex-col bg-blue-100">

        @if ($selectedChatId)
            @php
                // If $selectedChatId is an array (like a model or associative array), get 'id' safely,
                // else just use it directly (assuming it's a scalar id).
                if (is_array($selectedChatId)) {
                    $chatIdForKey = $selectedChatId['id'] ?? 'unknown';
                } else {
                    $chatIdForKey = $selectedChatId;
                }
                $chatBoxKey = 'chat-box-' . $chatIdForKey;
                $messageInputKey = 'message-input-' . $chatIdForKey;
            @endphp

            {{-- Chat Messages (Scrollable) --}}
            <section class="flex-grow overflow-y-auto p-4 scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-300">
                <livewire:chat.chat-box
                    :chatId="$selectedChatId"
                    :chatType="$chatType"
                    :wire:key="$chatBoxKey"
                />
            </section>

            {{-- Message Input at Bottom --}}
            <footer class="border-t border-gray-300 p-4 bg-gray-100">
                <livewire:chat.message-input
                    :chatId="$selectedChatId"
                    :chatType="$chatType"
                    :wire:key="$messageInputKey"
                />
            </footer>
        @else
            {{-- Placeholder if no chat selected --}}
            <div class="flex-grow flex items-center justify-center text-gray-400 text-lg select-none">
                Select a chat to start messaging
            </div>
        @endif

    </main>

</div>

<script>
    window.addEventListener('chatSelected', event => {
        Livewire.dispatch('chatSelected', {
            chatId: event.detail.chatId,
            chatType: event.detail.chatType,
        });
    });

    window.addEventListener('chatAdded', event => {
        Livewire.dispatch('chatAdded');
    });
</script>
