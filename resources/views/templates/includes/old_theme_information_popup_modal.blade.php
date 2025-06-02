@php
    use App\InformationPop;

    // Queries
    $information_pop = InformationPop::where('card_id', $business_card_details->card_id)->first();

    // Variables
    $confetti_effect = $information_pop->confetti_effect;
    $img = $information_pop->info_pop_image;
    $title = $information_pop->info_pop_title;
    $desc = $information_pop->info_pop_desc;
    $button_text = $information_pop->info_pop_button_text;
    $button_url = $information_pop->info_pop_button_url;
@endphp

<div id="infoPopModal" class="fixed inset-x-0 bottom-0 z-50 p-3 overflow-auto bg-black bg-opacity-50">
    <div class="flex items-end justify-center min-h-screen">
        <div
            class="bg-{{ $primary_color == 'black' ? 'white' : $primary_color . '-50' }} rounded-[32px] p-6 lg:w-1/4 w-full">
            <div class="flex justify-end items-center">
                <button id="closeInfoPopModal" class="text-gray-700 text-3xl">&times;</button>
            </div>
            <!-- Modal content -->
            <div class="mb-4 flex flex-col justify-center items-center">
                <img src="{{ url($img) }}" alt="" class="w-20 h-20 mb-3 rounded-xl shadow-xl">
                <h1 class="text-xl font-semibold mb-3">{{ $title }}</h1>
                <p class="text-gray-700 text-sm h-28 overflow-y-auto">
                    {{ $desc }}
                </p>
            </div>
            <div class="flex flex-col">
                @if ($primary_color == 'black')
                    <a href="{{ $button_url }}" target="_blank"
                        class="px-4 py-2 text-white text-center bg-[#121212] rounded-xl focus:outline-none">{{ $button_text }}</a>
                @else
                    <a href="{{ $button_url }}" target="_blank"
                        class="px-4 py-2 text-{{ $primary_color }}-600 text-center bg-{{ $primary_color }}-100 hover:bg-{{ $primary_color }}-200 rounded-xl focus:outline-none">{{ $button_text }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Enable confetti effect --}}
@if ($confetti_effect == 1)
    {{-- Confetti --}}
    <script src="{{ asset('js/confetti.browser.min.js') }}"></script>
    <script>
        // Function to generate a random color in hexadecimal format
        function getRandomColor() {
            return '#' + Math.floor(Math.random() * 16777215).toString(16);
        }
    
        // Generate an array of random colors
        const randomColors = Array.from({ length: 7 }, getRandomColor);
    
        // Check the screen width and set values for confetti
        if (window.innerWidth > 768) {
            // Desktop settings
            confetti({
                particleCount: 200,
                spread: 120,
                colors: randomColors,
                origin: { x: 0.5, y: 0.75 }  // Center of the screen (50% from the left and 50% from the top)
            });
        } else {
            // Mobile settings
            confetti({
                particleCount: 100,
                spread: 100,
                colors: randomColors,
                origin: { x: 0.5, y: 0.72 }  // For mobile, make it start higher up on the screen
            });
        }
    </script>        
@endif