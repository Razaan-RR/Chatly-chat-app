<div>
    @if ($chatId)
        <form wire:submit.prevent="sendMessage" class="flex items-end space-x-2">

            {{-- Image Preview --}}
            @if ($image)
                <div class="relative">
                    <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                         class="h-20 w-20 rounded-md object-cover border border-gray-300" />
                    <button type="button" wire:click="$set('image', null)"
                            class="absolute top-[-6px] right-[-6px] bg-red-500 text-white text-xs rounded-full p-1 hover:bg-red-600">
                        ×
                    </button>
                </div>
            @endif

            {{-- File Input (styled like WhatsApp) --}}
            <label class="cursor-pointer bg-gray-200 p-2 rounded-lg hover:bg-gray-300 transition">
                📎
                <input type="file" wire:model="image" accept="image/*" class="hidden" />
            </label>

            {{-- Message Text Input --}}
            <input
                type="text"
                wire:model.defer="body"
                class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Type a message..."
            />

            {{-- Send Button --}}
            <button 
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition"
            >
                ➤
            </button>
        </form>
    @endif
</div>
