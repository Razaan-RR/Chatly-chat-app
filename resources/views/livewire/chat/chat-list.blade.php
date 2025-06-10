<div class="flex flex-col h-full">

    {{-- Header / Title --}}
    <div class="p-4 border-b border-gray-300 flex items-center justify-between relative">
        <h2 class="text-xl font-semibold text-gray-800">Chats</h2>

        <div class="flex items-center space-x-2">
            {{-- + Add Chat Button --}}
            <button
                wire:click="$toggle('showAddUser')"
                class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded"
                title="Add Chat">
                +
            </button>

            {{-- Settings Button --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button
                    @click="open = !open"
                    class="text-xl hover:bg-gray-200 rounded px-2 py-1"
                    title="Settings"
                    type="button">
                    ⚙️
                </button>

                {{-- Dropdown Menu --}}
                <div
                    x-show="open"
                    x-transition
                    class="absolute right-0 mt-2 w-36 bg-white border border-gray-300 rounded shadow-lg z-50"
                    style="display: none;">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Chat List --}}
    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-300 bg-white">
        @if ($chats->isEmpty())
        <p class="p-4 text-gray-500 text-center">No chats found.</p>
        @else
        <ul>
            @foreach ($chats as $chat)
            @php
            $isGroup = $chat->chat_type === 'group';
            $lastMessage = $chat->messages->sortByDesc('created_at')->first();

            if ($isGroup) {
            $chatName = $chat->name ?? 'Unnamed Group';
            $avatarUrl = asset('group-avatar.png');
            } else {
            $otherUser = $chat->user1_id === auth()->id() ? $chat->user2 : $chat->user1;

            if ($otherUser) {
            $chatName = $otherUser->name;
            $avatarUrl = $otherUser->profile_photo_url;
            } else {
            $chatName = 'Unknown User';
            $avatarUrl = asset('default-avatar.png');
            }
            }
            @endphp

            <li
                wire:click="$dispatch('chatSelected', { chatId: {{ $chat->id }}, chatType: '{{ $chat->chat_type }}' })"
                class="cursor-pointer hover:bg-gray-100 transition-colors duration-150 border-b border-gray-200 last:border-none px-4 py-3">
                <div class="flex items-center space-x-3">
                    <img
                        src="{{ $avatarUrl }}"
                        alt="Avatar"
                        class="w-12 h-12 rounded-full object-cover" />
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 truncate">{{ $chatName }}</div>
                        <div class="text-sm text-gray-600 truncate">
                            {{ $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->body, 40) : 'No messages yet' }}
                        </div>
                    </div>
                </div>
            </li>
            @endforeach

        </ul>
        @endif
    </div>

    {{-- Add Chat Form --}}
    @if ($showAddUser)
    <div class="p-4 border-t border-gray-300 bg-gray-50">
        <form wire:submit.prevent="startChatWithEmail" class="space-y-2">
            <input
                type="email"
                wire:model.defer="newUserEmail"
                placeholder="Enter user email"
                required
                class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            @error('newUserEmail')
            <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Start Chat
            </button>
        </form>
    </div>
    @endif
</div>