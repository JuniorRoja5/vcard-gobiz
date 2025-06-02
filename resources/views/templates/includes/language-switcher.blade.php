@php
use Illuminate\Support\Str;

if (app()->getLocale() == null) {
    Session::put('locale', $business_card_details->card_lang);
    app()->setLocale(Session::get('locale'));
}
@endphp

<div class="fixed top-0 ltr:right-0 rtl:left-0 m-4 absolute w-14 z-20">
    <select id="language" name="language" class="block w-full bg-white rounded-xl text-gray-700 py-2 px-1 focus:outline-none sm:text-sm">
        @foreach(config('app.languages') as $langLocale => $langName)
            <option value="{{ $langLocale }}" {{ app()->getLocale() == $langLocale ? 'selected' : '' }}><strong>{{ Str::upper($langLocale) }}</strong></option>
        @endforeach
    </select>
</div>

{{-- Custom JS --}}
@section('custom-js')
<script>
// Language switcher
$(document).ready(function() {
    "use strict";
    // Language switcher
    $('#language').change(function() {
        var selectedLocale = $(this).val();

        $.ajax({
            url: "{{ route('set.locale') }}",  // Use the route name defined earlier
            type: "POST",
            data: {
                locale: selectedLocale,
                _token: '{{ csrf_token() }}' // Include CSRF token
            },
            success: function(response) {
                console.log(response.message); // Log success message
                // Redirect page
                window.location = ``;
            },
            error: function(xhr) {
                console.error(xhr.responseText); // Log error message
            }
        });
    });
});
</script>
@endsection