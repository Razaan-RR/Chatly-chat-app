<div class="flex flex-col h-full">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            @if ($chatType === 'group' && $selectedChat)
            {{-- Group avatar from public folder --}}
            <img src="{{ asset('group-avatar.png') }}" alt="{{ $selectedChat->name }}" class="w-10 h-10 rounded-full object-cover" />
            <h3 class="font-bold text-gray-700 text-lg">{{ $selectedChat->name }}</h3>
            @elseif ($chatPartner)
            <img src="{{ $chatPartner->profile_photo_url }}"
                alt="{{ $chatPartner->name }}"
                class="w-10 h-10 rounded-full object-cover" />
            <h3 class="font-bold text-gray-700 text-lg">{{ $chatPartner->name }}</h3>
            @else
            <h3 class="font-bold text-gray-700 text-lg">No Chat Selected</h3>
            @endif
        </div>


        {{-- 3-dot Menu --}}
        <div class="relative">
            <button wire:click="$toggle('showDropdown')" class="text-gray-900 focus:outline-none">
                &#8942;
            </button>
            @if ($showDropdown)
            <div class="absolute right-0 mt-2 w-40 bg-white border rounded shadow z-50">
                <button wire:click="openGroupCreator"
                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                    Create Group
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Group Creator --}}
    @if ($showGroupCreator)
    <div class="mb-4 space-y-2">
        <input type="text"
            wire:model.defer="groupName"
            class="w-full border px-3 py-2 rounded"
            placeholder="Enter group name"
            required />

        <div class="flex flex-wrap gap-2">
            @foreach ($groupEmails as $email)
            <span class="bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full">
                {{ $email }}
                <button wire:click="removeGroupEmail('{{ $email }}')"
                    class="ml-1 text-red-600 font-bold">&times;</button>
            </span>
            @endforeach
        </div>

        <form wire:submit.prevent="addGroupEmail">
            <input type="email"
                wire:model.defer="newGroupEmail"
                class="w-full border px-3 py-2 rounded"
                placeholder="Enter user email and press Enter"
                required />
        </form>

        <button wire:click="createGroup"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            Create Group Chat
        </button>
    </div>
    @endif

    {{-- Messages --}}
    <div class="flex-1 overflow-y-auto px-2 pb-4 scrollbar-custom space-y-3">
        @php $lastMessageDate = null; @endphp

        @foreach ($selectedChat?->messages ?? [] as $message)
        @php
        // Detect sender and ownership
        if ($chatType === 'private') {
        $isOwnMessage = $message->user_id === auth()->id();
        $sender = $message->user ?? null;
        } elseif ($chatType === 'group') {
        $isOwnMessage = $message->sender_id === auth()->id();
        $sender = $message->sender ?? null;
        } else {
        $isOwnMessage = false;
        $sender = null;
        }

        $messageDate = $message->created_at->format('Y-m-d');
        $dateLabel = $message->created_at->isToday()
        ? 'Today'
        : ($message->created_at->isYesterday() ? 'Yesterday' : $message->created_at->format('F j, Y'));
        @endphp

        {{-- Date Separator --}}
        @if ($lastMessageDate !== $messageDate)
        <div class="text-center text-xs text-gray-500 my-2">
            <span class="px-3 py-1 bg-gray-200 rounded-full">{{ $dateLabel }}</span>
        </div>
        @php $lastMessageDate = $messageDate; @endphp
        @endif

        {{-- Message Bubble --}}
        <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
            <div class="flex items-start gap-2 {{ $isOwnMessage ? 'flex-row' : 'flex-row-reverse' }}">

                {{-- + Emoji Trigger --}}
                <button wire:click="toggleReactionPicker({{ $message->id }})"
                    class="bg-gray-200 hover:bg-gray-300 text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">
                    +
                </button>

                {{-- Message Box --}}
                <div class="max-w-xs md:max-w-md px-4 py-2 rounded-xl shadow-sm relative
                        {{ $isOwnMessage ? 'bg-blue-100 border border-blue-300' : 'bg-gray-100 border border-gray-300' }}">

                    {{-- Sender name for group chat --}}
                    @if (!$isOwnMessage && $chatType === 'group' && $sender)
                    <div class="text-[10px] font-medium text-gray-500 mb-1">
                        {{ $sender->name }}
                    </div>
                    @endif


                    {{-- Optional Image --}}
                    @if ($message->image)
                    <a href="{{ asset('storage/' . $message->image) }}" target="_blank">
                        <img src="{{ asset('storage/' . $message->image) }}"
                            alt="Message Image"
                            class="rounded-xl max-w-[250px] w-full object-cover" />
                    </a>
                    @endif

                    {{-- Message Body --}}
                    @if ($message->body)
                    <p class="text-sm text-gray-800">{{ $message->body }}</p>
                    @endif

                    {{-- Timestamp & Status --}}
                    <div class="flex justify-between items-center text-[10px] text-gray-500 mt-1">
                        <span>{{ $message->created_at->format('H:i') }}</span>

                        @if ($isOwnMessage)
                        @if ($message->seen_at)
                        <span class="text-blue-600 font-semibold">✓✓</span>
                        @elseif ($message->delivered_at)
                        <span class="text-gray-600 font-semibold">✓✓</span>
                        @else
                        <span class="text-gray-600 font-semibold">✓</span>
                        @endif
                        @endif
                    </div>

                    {{-- Reactions --}}
                    @if ($message->reactions->count())
                    <div class="mt-1 flex flex-wrap gap-1 text-sm">
                        @foreach ($message->reactions->groupBy('reaction') as $emoji => $group)
                        <span class="bg-white border rounded-full px-2 py-0.5 text-xs">
                            {{ $emoji }} {{ $group->count() }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Reaction Picker --}}
                    @if ($showReactionPickerFor === $message->id)
                    <div class="mt-1 flex gap-1 bg-white border p-1 rounded shadow z-20">
                        @foreach (['❤️', '😂', '👍', '😮', '😢', '👎'] as $emoji)
                        <button wire:click="react({{ $message->id }}, '{{ $emoji }}')"
                            class="text-sm hover:scale-125 transition-transform duration-150">
                            {{ $emoji }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Profile Pic (Only for received messages) --}}
                @if (!$isOwnMessage && $sender && $sender->profile_photo_url)
                <img src="{{ $sender->profile_photo_url }}"
                    alt="{{ $sender->name }}"
                    class="w-8 h-8 rounded-full object-cover" />
                @elseif ($isOwnMessage)
                <div style="width: 32px;"></div>
                @endif

            </div>
        </div>
        @endforeach
    </div>
</div>