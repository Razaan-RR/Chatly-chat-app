<div class="space-y-4 max-w-sm mx-auto">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div>
    @php
        $user = auth()->user();
        $photoUrl = $preview ?: ($user && $user->profile_photo_path ? Storage::url($user->profile_photo_path) : null);
    @endphp

    @if ($photoUrl)
        <img src="{{ $photoUrl }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover mx-auto border border-gray-300" />
    @else
        <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center mx-auto text-gray-400">
            No Photo
        </div>
    @endif
</div>


    <div>
        <input type="file" wire:model="photo" id="photo" class="block w-full text-sm text-gray-600
            file:mr-4 file:py-2 file:px-4
            file:rounded file:border-0
            file:text-sm file:font-semibold
            file:bg-blue-50 file:text-blue-700
            hover:file:bg-blue-100
            "/>

        @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <button wire:click="save" 
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-150"
            wire:loading.attr="disabled"
            wire:target="save, photo">
            Save Changes
        </button>
    </div>
</div>
