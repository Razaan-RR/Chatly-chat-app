<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Models\User;

class ProfilePhotoForm extends Component
{
    use WithFileUploads;

    public $photo;
    public $preview;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->profile_photo_path) {
            $this->preview = Storage::url($user->profile_photo_path);
        }
    }


    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $this->preview = $this->photo->temporaryUrl();
    }

        public function save()
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $path = $this->photo->store('profile-photos', 'public');

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $path;
        $user->save();

        // Refresh preview URL using fresh user data from DB
        $user = $user->fresh();

        $this->preview = Storage::url($user->profile_photo_path);

        // Optional: clear the uploaded file so the preview shows only the saved photo
        $this->photo = null;

        session()->flash('message', 'Profile photo updated.');
    }

    public function render()
    {
        return view('livewire.profile.profile-photo-form');
    }
}
