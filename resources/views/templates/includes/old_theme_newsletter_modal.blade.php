<div id="newsletterModal" class="fixed inset-x-0 bottom-0 z-50 p-3 overflow-auto bg-black bg-opacity-50 hidden"
    data-csrf="{{ csrf_token() }}" data-email-error="{{ __('Please enter your email.') }}" data-vaild-email-error="{{ __('Please enter valid email.') }}">
    <div class="flex items-center justify-center min-h-screen">
        <div
            class="bg-{{ $primary_color == 'black' ? 'white' : $primary_color . '-50' }} rounded-xl p-8 lg:w-1/4 w-full">
            <!-- Modal content -->
            <div>
                <h1 class="text-xl font-semibold mb-3">{{ __('SUBSCRIBE TO OUR NEWSLETTER') }}</h1>
                <p class="text-gray-700 text-base">
                    {{ __('Subscribe to our newsletter to stay up to date with our latest news and offers.') }}
                </p>
                {{-- Error Message (hidden by default) --}}
                <div id="errorMessage" class="text-red-500 text-sm my-2 hidden"></div>
                {{-- Success Message (hidden by default) --}}
                <div id="successMessage" class="text-green-500 text-sm my-2 hidden"></div>
                <div class="flex flex-row mt-4">
                    <input type="hidden" id="card_id" name="card_id" value="{{ $business_card_details->card_id }}">
                    <input type="email" id="newsletter_email" placeholder="Your Email*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-{{ $primary_color }}-700 focus:ring-opacity-50" required />
                    @if ($primary_color == 'black')
                        <button id="subscribeButton"
                            class="ml-2 px-4 py-2 text-white text-center bg-black rounded-xl focus:outline-none">{{ __('Subscribe') }}</button>
                    @else
                        <button id="subscribeButton"
                            class="ml-2 px-4 py-2 text-white text-center bg-{{ $primary_color }}-600 hover:bg-{{ $primary_color }}-700 rounded-xl focus:outline-none">{{ __('Subscribe') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const newsletterModal = document.getElementById("newsletterModal");
        const modalContent = newsletterModal.querySelector("div.bg-{{ $primary_color == 'black' ? 'white' : $primary_color . '-50' }}");

        // Close the modal when clicking outside the content
        newsletterModal.addEventListener("click", (event) => {
            if (!modalContent.contains(event.target)) {
                newsletterModal.classList.add("hidden"); // Hide the modal
            }
        });
    });
</script>