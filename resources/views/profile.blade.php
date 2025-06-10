<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-blue-50 min-h-screen overflow-y-auto">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Upload Profile Photo -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-blue-200">
                <div class="max-w-xl">
                    <livewire:profile.profile-photo-form />
                </div>
            </div>
            
            <!-- Update Profile Info -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-blue-200">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-blue-200">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <!-- Delete User -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-blue-200 mb-16">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

