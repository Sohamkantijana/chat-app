{{-- resources/views/profile/show.blade.php --}}
<x-guest-layout>
    <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6 mt-10">

        <div class="flex items-center gap-4 mb-6">
            <img src="{{ $user->profile_pic ?? 'https://via.placeholder.com/80' }}"
                 class="w-20 h-20 rounded-full object-cover border">
            <div>
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                <p class="text-gray-600">Status Viewer</p>
            </div>
        </div>

        <!-- Status Text -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-1">Status</h3>
            @if($user->status)
                <p class="text-gray-700 bg-gray-100 p-3 rounded">{{ $user->status }}</p>
            @else
                <p class="text-gray-500 italic">No status text set.</p>
            @endif
        </div>

        <!-- Status Images Slider -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">Status Images</h3>

            @if($user->statuses->count() > 0)

                <!-- Swiper CSS -->
                <link rel="stylesheet"
                      href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

                <div class="swiper myStatusSlider">
                    <div class="swiper-wrapper">
                        @foreach($user->statuses as $status)
                            <div class="swiper-slide">
                                <img src="{{ asset('storage/' . $status->image) }}"
                                     class="w-full h-80 object-cover rounded-lg shadow" />
                            </div>
                        @endforeach
                    </div>

                    <div class="swiper-pagination"></div>
                </div>

                <!-- Swiper JS -->
                <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        new Swiper('.myStatusSlider', {
                            pagination: { el: '.swiper-pagination' },
                            loop: true,
                            spaceBetween: 20,
                        });
                    });
                </script>

            @else
                <p class="text-gray-500 italic">No status images uploaded.</p>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('profile.edit') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit My Profile
            </a>
        </div>

    </div>
</x-guest-layout>
