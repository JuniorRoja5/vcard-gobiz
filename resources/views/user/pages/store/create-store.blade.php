@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

{{-- Custom CSS --}}
@section('css')
{{-- Cropper --}}
<link href="{{ asset('css/cropper.min.css') }}" rel="stylesheet">
<style>
    .section-theme {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .theme-image {
        width: 70% !important;
        /* height: 70% !important; */
        border-radius: 18px;
        margin-bottom: 1rem;
        padding: 10px;
    }

    .border-curve{
        border-radius: 16px;
    }


    .btn-choose-theme {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .avatar {
        --tblr-avatar-bg: #f6f8fb00;
    }

    .avatar-xl {
        --tblr-avatar-size: 10rem !important;
    }

    .ts-control {
        line-height: 1.9 !important;
    }

    .reduce-control {
        line-height: 1.7 !important;
    }

    [data-bs-theme=light] {
        --tblr-border-radius: 7px !important;
    }

    #lcl_elem_wrap {
        cursor: pointer;
    }
</style>
@endsection

@php 
$defaultImage = "";

foreach ($themes as $value => $theme) {
    if ($value == 0) {
        $defaultImage = url("img/vCards/".$theme->theme_thumbnail);
    }
}
@endphp

@section('content')
<div class="page-wrapper">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        {{ __('Overview') }}
                    </div>
                    <h2 class="page-title">
                        {{ __('Create New WhatsApp Store') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            {{-- Failed --}}
            @if(Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif
            
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <form action="{{ route('user.save.store') }}" method="post" enctype="multipart/form-data"
                        class="card">
                        @csrf
                        {{-- Create Card --}}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-xl-4 mb-5 text-center themes-lightbox">
                                    <img src="{{ $defaultImage }}" class="object-contain theme-image" alt="">

                                    <a href="#" class="btn btn-primary btn-choose-theme" data-bs-toggle="modal"
                                        data-bs-target="#themeModal">
                                        {{ __('Choose a theme') }}
                                    </a>
                                </div>
                                <div class="col-md-8 col-xl-8">
                                    <div class="row">
                                        <input type="hidden" class="form-control" name="theme_id" value="588969111070">

                                        {{-- Language --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="card_lang">{{ __('Language') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="card_lang" id="card_lang" class="form-control card_lang" required>
                                                    @foreach(config('app.languages') as $langLocale => $langName)
                                                    <option class="dropdown-item" value="{{ $langLocale }}" {{ $langLocale == config('app.locale') ? 'selected' : '' }}>{{ $langName }} ({{ strtoupper($langLocale) }})
                                                    </option>
                                                    @endforeach
                                                </select> 
                                            </div>
                                        </div>

                                        {{-- Profile image and banner images preview --}}
                                        <div class="col-md-12 col-xl-12 d-none" id="previewSection">
                                            <div class="row mb-3">
                                                <div class="col-md-6 col-xl-6 mb-3" id="previewCoverContainer"></div>
                                                <div class="col-md-6 col-xl-6" id="logoPreview"></div>
                                            </div>
                                        </div>

                                        {{-- Banner --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Banner') }} </div>
                                                <input type="file" class="form-control" id="coverInput"
                                                    placeholder="{{ __('Banner') }}" required
                                                    accept=".jpeg,.jpg,.png,.gif,.svg" />
                                                <input type="hidden" class="form-control" name="banner">
                                                <small class="fw-bold"><span class="text-danger">*</span> {{ __('Upload banner images one after the other') }}</small>
                                            </div>
                                        </div>

                                        {{-- Logo --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Logo') }}</div>
                                                <input type="file" class="form-control" id="logo"
                                                    placeholder="{{ __('Logo') }}" required
                                                    accept=".jpeg,.jpg,.png,.gif,.svg" />
                                                <input type="hidden" class="form-control" name="logo">
                                            </div>
                                        </div>

                                        {{-- Store name --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Store name') }}</label>
                                                <input type="text" class="form-control" name="title"
                                                    onload="convertToLink(this.value); checkLink()"
                                                    onkeyup="convertToLink(this.value); checkLink()"
                                                    placeholder="{{ __('Store name') }}" required>
                                            </div>
                                        </div>

                                        {{-- Personalized Link --}}
                                        @if ($plan_details->personalized_link)
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Personalized Link') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        {{ URL::to('/') }}
                                                    </span>
                                                    <input type="text" class="form-control" name="link" placeholder="{{ __('Personalized Link') }}" autocomplete="off" id="plink" onkeyup="checkLink()" minlength="3" required>
                                                    <span class="input-group-text" id="status">
                                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Store greeting --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Store greeting') }}</label>
                                                <input type="text" class="form-control" name="subtitle"
                                                    placeholder="{{ __('Ex: Welcome to') }}" required>
                                            </div>
                                        </div>

                                        {{-- Currency --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required" for="currency">{{ __('Currency')
                                                    }}</label>
                                                <select name="currency" id="currency" class="form-control currency" required>
                                                    <option value="" disabled selected>{{ __("Choose currency") }}</option>
                                                    @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->iso_code }}">{{ $currency->name }} ({{ $currency->symbol }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Country Code --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Country Code') }}</label>
                                                @include('user.pages.store.include.country-code')
                                            </div>
                                        </div>

                                        {{-- WhatsApp Number --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('WhatsApp Number') }}</label>
                                                <input type="number" class="form-control" name="whatsapp_no"
                                                    placeholder="{{ __('Ex. 9876543210') }}"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- WhatsApp Footer Text --}}
                                        <div class="col-md-12 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('WhatsApp Footer Text')
                                                    }}</label>
                                                <textarea class="form-control" name="whatsapp_msg"
                                                    data-bs-toggle="autosize"
                                                    placeholder="{{ __('Thanks note') }}"
                                                    required>{{ __('Thanks for shopping with us.') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-xl-4 my-3">
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Submit') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('user.includes.footer')
</div>

{{-- Choose a theme modal --}}
<div class="modal modal-blur fade" id="themeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <div class="input-group input-group-flat">
                                <input type="text" id="searchInput" class="form-control" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>                                             
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <!-- Using an icon for the close button -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M18 6l-12 12"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="results">
                    @foreach ($themes as $theme)
                    <div class="col-lg-2 col-sm-2 col-md-2 col-6">
                        <label class="form-imagecheck mb-2">
                            <input type="radio" id="theme_id" value="{{ $theme->theme_id }}" onclick="chooseTheme(this, `{{ asset('img/vCards/'.$theme->theme_thumbnail) }}`)"
                                class="form-imagecheck-input theme_id" {{ $loop->first ? 'checked' : '' }} required />
                            <span class="text-center font-weight-bold">
                                <img src="{{ asset('img/vCards/'.$theme->theme_thumbnail) }}"
                                    class="object-cover border-curve" alt="{{ $theme->theme_name }}">
                                    <div class="text-center">
                                        <p class="badge bg-primary text-white m-1">{{ $theme->theme_name }}</p>
                                    </div>
                            </span>
                        </label>

                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Image Cropping -->
<div id="cropModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Crop Image') }}</h4>
            </div>
            <div class="modal-body">
                <div class="cropper-container" style="width: 100%;">
                    <img id="croppedImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="crop">{{ __('Crop') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Crop Cover Image Modal -->
<div id="cropCoverModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Crop Cover Image') }}</h4>
            </div>
            <div class="modal-body">
                <div class="cropper-container" style="width: 100%;">
                    <img id="croppedCoverImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="coverCrop">{{ __('Crop') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Custom JS --}}
@push('custom-js')
{{-- Tom Select --}}
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>

{{-- Cropper --}}
<script src="{{ asset('js/cropper.min.js') }}"></script>

 {{-- Profile image cropping --}}
 <script>
    $(document).ready(function () {
        var cropper;
        var uploadedImageURL;

        // Initialize cropper when modal is shown
        $('#cropModal').on('shown.bs.modal', function () {
            cropper = new Cropper(document.getElementById('croppedImage'), {
                aspectRatio: 3, // Aspect ratio of 1:1
                viewMode: 1, // Set view mode to 3 (restrict the crop box to fit within the container, then scale the result image to fit exactly 512x512 pixels)
                autoCropArea: 1, // Auto crop the entire image
                cropBoxResizable: false, // Disable crop box resizing
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
        });

        // Handle image upload
        $('#logo').change(function (e) {
            var files = e.target.files;
            var reader = new FileReader();

            reader.onload = function (event) {
                uploadedImageURL = event.target.result;
                $('#croppedImage').attr('src', uploadedImageURL);
                $('#cropModal').modal('show');
            };

            reader.readAsDataURL(files[0]);
        });

        // Handle crop button click
        $('#crop').click(function () {
            var $button = $(this);
            $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Uploading...');

            var canvas = cropper.getCroppedCanvas({
                width: 512,
                height: 512,
            });

            canvas.toBlob(function (blob) {
                var formData = new FormData();
                formData.append('croppedImage', blob);

                // Include CSRF token in the AJAX request
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route("user.vcard.cropped.image") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Optionally, close the modal after successful upload
                        $('#cropModal').modal('hide');

                        // Set the imageUrl in the #logo input field
                        $('input[name="logo"]').val(response.imageUrl);
                    },
                    error: function () {
                        console.log('Upload error');
                    },
                    complete: function () {
                        // Re-enable the button and restore its text
                        $button.prop('disabled', false).html('Crop');
                    }
                });
            });

            // Display cropped image preview in #logoPreview
            var croppedImageURL = cropper.getCroppedCanvas().toDataURL();
            var previewSection = $('#previewSection').removeClass('d-none');
            var previewLogoContainer = $('#logoPreview');
            var previewHtml = '<div class="col-md-4"><img src="' + croppedImageURL + '" class="img-fluid rounded"></div>';
            previewLogoContainer.append(previewHtml);
        });
    });
</script>
<script>
$(document).ready(function () {
    var cropper;
    var uploadedImageURL;
    var currentFileIndex;
    var files = [];
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $('#coverInput').change(function (e) {
        files = e.target.files;
        if (files.length > 0) {
            currentFileIndex = 0;
            loadNextImage();
        }
    });

    function loadNextImage() {
        if (currentFileIndex < files.length) {
            var reader = new FileReader();
            reader.onload = function (event) {
                uploadedImageURL = event.target.result;
                $('#croppedCoverImage').attr('src', uploadedImageURL);
                $('#cropCoverModal').modal('show');
            };
            reader.readAsDataURL(files[currentFileIndex]);
        }
    }

    $('#cropCoverModal').on('shown.bs.modal', function () {
        cropper = new Cropper(document.getElementById('croppedCoverImage'), {
            aspectRatio: 1288 / 408,
            viewMode: 3,
            autoCropArea: 1,
            cropBoxResizable: false,
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
    });

    $('#coverCrop').click(function () {
        var $button = $(this);
        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Uploading...');

        var canvas = cropper.getCroppedCanvas({
            width: 1288,
            height: 408,
        });

        canvas.toBlob(function (coverBlob) {
            var formData = new FormData();
            var imageName = 'cropped_image_' + currentFileIndex + '.png';
            formData.append('croppedImage', coverBlob, imageName);

            $.ajax({
                url: '{{ route("user.store.cropped.images") }}', // Ensure this route is defined in your web.php
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    addPreview(response.imageUrl);
                    currentFileIndex++;
                    if (currentFileIndex < files.length) {
                        loadNextImage();
                    } else {
                        $('#cropCoverModal').modal('hide');
                    }
                },
                error: function () {
                    console.log('Upload error');
                },
                complete: function () {
                    // Re-enable the button and restore its text
                    $button.prop('disabled', false).html('Crop');
                }
            });
        });
    });

    var pushedCoverImages = [];
    function addPreview(imageUrl) {
        var previewCoverContainer = $('#previewCoverContainer');
        var previewSection = $('#previewSection').removeClass('d-none');
        var previewHtml = '<div class="col-md-6"><img src="' + imageUrl + '" class="img-fluid rounded"></div>';
        previewCoverContainer.append(previewHtml);
        // Set the imageUrl in the #banner input field
        pushedCoverImages.push(imageUrl);
        $('input[name="banner"]').val(pushedCoverImages);
    }
});
</script>
<script>
    function checkLink(){
    "use strict";
    var plink = $('#plink').val();

    if(plink.length > 2){
        $.ajax({
        url: "{{ route('user.check.link') }}",
        method: 'POST',
        data:{_token: "{{ csrf_token() }}", link: plink},
        }).done(function(res) {
            if(res.status == 'success') {
                // Set status badge
                $('#status').html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Available') }}"><svg xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-circle-check text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg></span>`);
            }else{
                $('#status').html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Not available') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-xbox-x text-danger"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10 -10 10s-10 -4.477 -10 -10s4.477 -10 10 -10m3.6 5.2a1 1 0 0 0 -1.4 .2l-2.2 2.933l-2.2 -2.933a1 1 0 1 0 -1.6 1.2l2.55 3.4l-2.55 3.4a1 1 0 1 0 1.6 1.2l2.2 -2.933l2.2 2.933a1 1 0 0 0 1.6 -1.2l-2.55 -3.4l2.55 -3.4a1 1 0 0 0 -.2 -1.4" /></svg></span>`);
            }
        });
    }else{
        $('#status').html(`<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>`);
    }
}

$(document).ready(function() {
   $(".modal").on("hidden.bs.modal", function() {
    $(".theme_id").prop('checked', false);
   });
 });

function chooseTheme(selectedTheme, thumbnail) {
    $("input[name='theme_id']").val(selectedTheme.value);
    $(".theme-image").attr("src", thumbnail);

    $("#themeModal").modal("hide");
}

/* Encode string to link */
function convertToLink( str ) {
    "use strict";
    //replace all special characters | symbols with a space
    str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
             .toLowerCase();

    // trim spaces at start and end of string
    str = str.replace(/^\s+|\s+$/gm,'');

    // replace space with dash/hyphen
    str = str.replace(/\s+/g, '-');
    document.getElementById("plink").value = str;
    //return str;
}

var APP_URL = '{{ config('app.url') }}';

$(document).ready(function() {
"use strict";

$('#searchInput').on('keyup', function() {
    "use strict";

    let query = $(this).val();
    let type = 'WhatsApp Store';

    $.ajax({
        url: '{{ route('user.search.theme') }}',
        type: 'GET',
        data: {
            query: query, type: type
        },
        dataType: 'json',
        success: function(response) {
            let output = '';
            if (response.length === 0) {
                output = '<div class="alert alert-warning">{{ __("No themes found.") }}</div>';
            } else {
                $.each(response, function(index, card) {
                    output += `<div class="col-lg-2 col-sm-2 col-md-2 col-6">
                                        <label class="form-imagecheck mb-2">
                                            <input type="radio" id="theme_id" value="${card.theme_id}" onclick="chooseTheme(this, '${APP_URL}/img/vCards/${card.theme_thumbnail}')" class="form-imagecheck-input theme_id" required />
                                            <span class="form-imagecheck-figure text-center font-weight-bold">
                                                <img src="${APP_URL}/img/vCards/${card.theme_thumbnail}"
                                                    class="object-cover" alt="${card.theme_name}">
                                            </span>
                                        </label>
                                        <div class="text-center">
                                            <h2 class="badge bg-primary text-white mt-2">${card.theme_name}</h2>
                                        </div>
                                    </div>`;
                    });
                }
                $('#results').html(output);
            }
        });
    });
});
</script>
<script>
    // Array of element selectors
    var elementSelectors = ['.card_lang', '.country_code', '.currency'];

    // Function to initialize TomSelect on an element
    function initializeTomSelect(el) {
        new TomSelect(el, {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            maxOptions: null,
            hideSelected: false, // Prevents hiding selected items
            plugins: {
                remove_button: {},  // Optional: Adds remove buttons to selected items
                duplicates: {} // Allows duplicate values
            },
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        });

        // Ensure the "required" attribute is enforced
        el.addEventListener('change', function() {
            if (el.value) {
                el.setCustomValidity('');
            } else {
                el.setCustomValidity('This field is required');
            }
        });
    
        // Trigger validation on load
        el.dispatchEvent(new Event('change'));
    }

    // Initialize TomSelect on existing elements
    elementSelectors.forEach(function(selector) {
        var elements = document.querySelectorAll(selector);
        elements.forEach(function(el) {
            initializeTomSelect(el);
        });
    });

    // Observe the document for dynamically added elements
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Ensure it's an element node
                    elementSelectors.forEach(function(selector) {
                        if (node.matches(selector)) {
                            initializeTomSelect(node);
                        }
                        // Also check if new nodes have children that match
                        var childElements = node.querySelectorAll(selector);
                        childElements.forEach(function(childEl) {
                            initializeTomSelect(childEl);
                        });
                    });
                }
            });
        });
    });

    // Configure the observer
    observer.observe(document.body, { childList: true, subtree: true });
</script>
@endpush
@endsection
