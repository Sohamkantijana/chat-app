{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and status.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6"
          enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                          :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                          :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Status Text -->
        <div>
            <x-input-label for="status" :value="__('Status Text')" />
            <textarea id="status" name="status" rows="2"
                      class="mt-1 block w-full border-gray-300 rounded-lg">{{ old('status', $user->status) }}</textarea>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <!-- Multiple Status Images Upload -->
        <div>
            <x-input-label for="status_images" :value="__('Upload Status Images')" />
            <input type="file" id="status_images" name="status_images[]"
                   class="mt-1 block w-full" multiple accept="image/*" />

            @if ($errors->has('status_images.*'))
                @foreach ($errors->get('status_images.*') as $errorArray)
                    <x-input-error :messages="$errorArray" class="mt-2" />
                @endforeach
            @endif

            <p class="text-sm text-gray-600 mt-1">
                You can upload multiple images. Max size per image: 2MB.
            </p>
        </div>

        <!-- Show Existing Status Images -->
        @if($user->statuses->count())
            <div class="mt-4">
                <p class="font-semibold text-gray-800 mb-2">Your Current Status Images</p>

                <div class="grid grid-cols-3 gap-3">
                    @foreach($user->statuses as $status)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $status->image) }}"
                                 class="w-full h-28 object-cover rounded-lg shadow" />

                            <label class="flex items-center gap-2 mt-1 text-sm">
                                <input type="checkbox" name="delete_status_ids[]" value="{{ $status->id }}">
                                Delete
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
