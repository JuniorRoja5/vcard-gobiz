@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

{{-- Custom CSS --}}
@section('css')
{{-- Cropper --}}
<link href="{{ asset('css/cropper.min.css') }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js" integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="{{ asset('css/all.css') }}" />
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

    .tox-promotion {
        display: none !important;
    }

    #lcl_elem_wrap {
        cursor: pointer;
    }
</style>
@endsection

@php
$defaultImage = "";

foreach ($themes as $value => $theme) {
    $defaultImage = asset("img/vCards/flowershop.png");
    $themeId = "588969111146";
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
                        {{ __('Create New Business Card') }}
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
                    <form action="{{ route('user.save.business.card') }}" method="post" enctype="multipart/form-data"
                        class="card">
                        @csrf
                        {{-- Create Card --}}
                        <div class="card-body">
                            <div class="row">
                                {{-- Themes --}}
                                <div class="col-md-4 col-xl-4 mb-5 text-center themes-lightbox">
                                    <img src="{{ $defaultImage }}"
                                        class="object-contain theme-image" alt="">
                                    <a href="#" class="btn btn-primary btn-choose-theme" data-bs-toggle="modal"
                                        data-bs-target="#themeModal">
                                        {{ __('Choose a theme') }}
                                    </a>
                                </div>

                                {{-- Card details --}}
                                <div class="col-md-8 col-xl-8">
                                    <div class="row">
                                        <input type="hidden" class="form-control" name="theme_id" value="{{ $themeId }}">
                                        <input type="hidden" class="form-control" name="type" value="{{ Request::get('type') ? Request::get('type') : 'business' }}">

                                        <div class="{{ $plan_details->personalized_link ? 'col-md-6 col-xl-6' : 'col-md-8 col-xl-8' }}">
                                            <div class="mb-3">
                                                <label class="form-label required" for="card_lang">{{ __('Language') }}</label>
                                                <select name="card_lang" id="card_lang" class="form-control card_lang" required>
                                                    @foreach(config('app.languages') as $langLocale => $langName)
                                                    <option class="dropdown-item" value="{{ $langLocale }}" {{ $langLocale == config('app.locale') ? 'selected' : '' }}>{{ $langName }} ({{ strtoupper($langLocale) }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-6" id="cover-type-section">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Cover type') }}</div>
                                                <select id="coverType" name="cover_type" class="form-control cover_type" required>

                                                    <option class="dropdown-item" value="youtube">
                                                        {{ __('YouTube Video') }}
                                                    </option>

                                                    <option class="dropdown-item" value="youtube-ap">
                                                        {{ __('YouTube Video - Autoplay') }}
                                                    </option>

                                                    <option class="dropdown-item" value="vimeo">
                                                        {{ __('Vimeo Video') }}
                                                    </option>

                                                    <option class="dropdown-item" value="vimeo-ap">
                                                        {{ __('Vimeo Video - Autoplay') }}
                                                    </option>

                                                    <option class="dropdown-item" value="photo" selected>
                                                        {{ __('Photo') }}
                                                    </option>

                                                    <option class="dropdown-item" value="none">
                                                        {{ __('Default') }}
                                                    </option>

                                                </select>

                                                <small>{{ __('Autoplay video will be muted due to browser policies')
                                                    }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3" id="cover_url_col">
                                                <label id="cover_url_label" class="form-label required">{{ __('Cover Video URL') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="cover_url_title" >
                                                       {{ __("https://www.youtube.com/watch?v=") }}
                                                    </span>
                                                    <input type="text" class="form-control" id="cover_url_input" name="cover_url" value="{{ old('cover_url') }}"
                                                        placeholder="{{ __('Video ID') }}" autocomplete="off"
                                                        minlength="3">
                                                </div>
                                            </div>

                                            <div class="mb-3" id="coverChooser">
                                                <div class="form-label required">{{ __('Cover') }}</div>
                                                <input type="file" class="form-control" id="cover"
                                                    placeholder="{{ __('Cover') }}"
                                                    accept=".jpeg,.jpg,.png,.gif,.svg" />
                                                <input type="hidden" class="form-control" name="cover">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Logo') }}</div>
                                                <input type="file" class="form-control" id="logo"
                                                    placeholder="{{ __('Logo') }}"
                                                    accept=".jpeg,.jpg,.png,.gif,.svg" required />
                                                <input type="hidden" class="form-control" name="logo">
                                            </div>
                                        </div>

                                         {{-- Cover Preview --}}
                                         <div class="col-md-6 col-xl-6 d-none" id="coverNone">
                                            <span class="avatar avatar-xl w-100 me-3" id="coverPreview" style="background-position: left;border-radius:8px;"></span>
                                        </div>

                                        {{-- Logo Preview --}}
                                        <div class="col-md-6 col-xl-6 d-none" id="logoNone">
                                            <span class="avatar avatar-xl w-100 me-3" id="logoPreview" style="background-size: contain; background-position: left;border-radius:8px;"></span>
                                        </div>

                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Title') }}</label>
                                                <input type="text" class="form-control" name="title"
                                                    onload="convertToLink(this.value); checkLink()"
                                                    onkeyup="convertToLink(this.value); checkLink()"
                                                    value="{{ old('title') }}"
                                                    placeholder="{{ __('Business name / Your name') }}" required>
                                            </div>
                                        </div>

                                        @if ($plan_details->personalized_link)
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Personalized Link') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        {{ URL::to('/') }}
                                                    </span>
                                                    <input type="text" class="form-control" name="link" placeholder="{{ __('Personalized Link') }}" autocomplete="off" id="plink" onkeyup="checkLink()" value="{{ old('link') }}"
                                                        minlength="3" required>
                                                    <span class="input-group-text" id="status">
                                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Sub Title') }}</label>
                                                <input type="text" class="form-control" name="subtitle"
                                                    value="{{ old('subtitle') }}"
                                                    placeholder="{{ __('Location / Job title') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Description') }}</label>
                                                <textarea class="form-control" name="description" id="description"
                                                    data-bs-toggle="autosize"
                                                    placeholder="{{ __('About business / Bio') }}">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-xl-4 my-1">
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
                                        <p class="badge bg-primary text-white m-1">
                                            {{ $theme->theme_name }}
                                        </p>
                                        @php
                                        $vidSupportedIds = ["588969111094","588969111095", "588969111093", "588969111092", "588969111091", "588969111090", "588969111089", "588969111088", "588969111087", "588969111086"];
                                        @endphp
                                        @if (in_array($theme->theme_id, $vidSupportedIds))
                                        <p class="badge bg-primary text-white m-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Cover video supported for this theme') }}">
                                            <i class="fa-solid fa-video"></i>
                                        </p>
                                        @else
                                        <p class="badge bg-primary text-white m-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Cover video not supported for this theme') }}">
                                            <i class="fa-solid fa-video-slash"></i>
                                        </p>
                                        @endif
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

{{--  Available theme for Cover image --}}
<div class="modal modal-blur fade" id="availableCoverImage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 9v2m0 4v.01" />
                    <path
                        d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                </svg>
                <h3>{{ __('Are you sure?') }}</h3>
                <div id="status_message" class="text-muted">{{ __('Cover video not supported for this theme') }}</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                {{ __('Yes, proceed') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Image Cropping -->
<div class="modal modal-blur fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <h3 class="mb-5">{{ __('Crop Image') }}</h3>
                <div class="text-muted">
                    <img id="croppedImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                        <div class="col">
                            <a class="btn btn-danger w-100" id="crop">
                                {{ __('Crop') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile cover Image Cropping -->
<div class="modal modal-blur fade" id="cropCoverModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <h3 class="mb-5">{{ __('Crop Image') }}</h3>
                <div class="cropper-container text-muted">
                    <img id="croppedCoverImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                        <div class="col">
                            <a class="btn btn-danger w-100" id="coverCrop">
                                {{ __('Crop') }}
                            </a>
                        </div>
                    </div>
                </div>
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
{{-- Choose a theme modal --}}
<script>
var coverThemeId = "588969111142";
function chooseTheme(selectedTheme, thumbnail) {
    $("input[name='theme_id']").val(selectedTheme.value);
    $(".theme-image").attr("src", thumbnail);

    $("#themeModal").modal("hide");

    // Theme ID
    coverThemeId = selectedTheme.value;

    var vidSupportedIds = ["588969111146", "588969111145", "588969111144", "588969111143", "588969111142", "588969111141", "588969111140", "588969111139", "588969111138", "588969111137", "588969111136", "588969111135", "588969111134", "588969111133", "588969111132", "588969111131", "588969111130", "588969111129", "588969111128", "588969111127", "588969111126", "588969111125", "588969111094", "588969111095", "588969111093", "588969111092", "588969111091", "588969111090", "588969111089", "588969111088", "588969111087", "588969111086"];
    if (vidSupportedIds.includes(selectedTheme.value)) {
       // Nothing to do.
    }else{
         $("#availableCoverImage").modal("show");
    }
}
</script>
{{-- Profile image cropping --}}
<script>
    $(document).ready(function () {
        var cropper;
        var uploadedImageURL;

        // Initialize cropper when modal is shown
        $('#cropModal').on('shown.bs.modal', function () {
            cropper = new Cropper(document.getElementById('croppedImage'), {
                aspectRatio: 1, // Aspect ratio of 1:1
                viewMode: 3, // Set view mode to 3 (restrict the crop box to fit within the container, then scale the result image to fit exactly 512x512 pixels)
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
                imageSmoothingEnabled: true, // Enable image smoothing
                imageSmoothingQuality: 'high', // Set the image smoothing quality
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
            $("#logoPreview").css('background-image', 'url(' + croppedImageURL + ')');
        });
    });
</script>

{{-- Profile cover image cropping --}}
<script>
    $(document).ready(function () {
        var cropper;
        var uploadedCoverImageURL;

        var aspectRatio = 16 / 4; // Aspect ratio of : 16:4

        // Update aspect ratio based on theme ID
        function updateAspectRatio() {
            if (coverThemeId === "588969111146" || coverThemeId === "588969111145" || coverThemeId === "588969111144" || coverThemeId === "588969111143" || coverThemeId === "588969111142" || coverThemeId === "588969111141" || coverThemeId === "588969111140" || coverThemeId === "588969111139" || coverThemeId === "588969111138" || coverThemeId === "588969111137" || coverThemeId === "588969111136" || coverThemeId === "588969111135" || coverThemeId === "588969111134" || coverThemeId === "588969111133" || coverThemeId === "588969111132" || coverThemeId === "588969111131" || coverThemeId === "588969111130" || coverThemeId === "588969111129" || coverThemeId === "588969111128" || coverThemeId === '588969111127' || coverThemeId === '588969111125' || coverThemeId === '588969111126') {
                aspectRatio = 24 / 12; // Aspect ratio for theme 588969111125: 24:12
            } else {
                aspectRatio = 24 / 12; // Default aspect ratio
            }
        }

        // Initialize cropper when modal is shown
        $('#cropCoverModal').on('shown.bs.modal', function () {
            updateAspectRatio(); // Ensure the aspect ratio is updated based on the current `coverThemeId`
            cropper = new Cropper(document.getElementById('croppedCoverImage'), {
                aspectRatio: aspectRatio, // Aspect ratio of : 16:4
                viewMode: 3, // Set view mode to 3 (restrict the crop box to fit within the container, then scale the result image to fit exactly 512x512 pixels)
                autoCropArea: 1, // Auto crop the entire image
                cropBoxResizable: false, // Disable crop box resizing
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
        });

        // Handle image upload
        $('#cover').change(function (e) {
            var files = e.target.files;
            var reader = new FileReader();

            reader.onload = function (event) {
                uploadedCoverImageURL = event.target.result;
                $('#croppedCoverImage').attr('src', uploadedCoverImageURL);
                $('#cropCoverModal').modal('show');
            };

            reader.readAsDataURL(files[0]);
        });

        // Handle crop button click
        $('#coverCrop').click(function () {
            // Disable the button and add a loader
            var $button = $(this);
            $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Uploading...');

            // Get the original dimensions
            const croppedCoverImage = document.getElementById('croppedCoverImage');
            const originalWidth = croppedCoverImage.naturalWidth;
            const originalHeight = croppedCoverImage.naturalHeight;

            var canvas = cropper.getCroppedCanvas({
                width: originalWidth,
                height: originalHeight,
                imageSmoothingEnabled: true, // Enable image smoothing
                imageSmoothingQuality: 'high', // Set the image smoothing quality
            });

            canvas.toBlob(function (coverBlob) {
                var formData = new FormData();
                formData.append('croppedImage', coverBlob);

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
                        $('#cropCoverModal').modal('hide');

                        // Set the imageUrl in the #logo input field
                        $('input[name="cover"]').val(response.imageUrl);
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

            // Display cropped image preview in #coverPreview
            var croppedCoverImageURL = cropper.getCroppedCanvas().toDataURL();
            $("#coverPreview").css('background-image', 'url(' + croppedCoverImageURL + ')');
        });
    });
</script>

{{-- Check link --}}
<script>
    function checkLink(){
    "use strict";
    var plink = $('#plink').val();

    if(plink.length > 0){
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

// Preview Cover
$(document).ready(() => {
    "use strict";

    const coverInp = $("#cover");
    let imgURL;

    coverInp.change(function (e) {
        $("#logoNone").removeClass('d-none');
        $("#coverNone").removeClass('d-none');
        // Remove unwanted space
        $(".unwanted-space").remove();
        

        imgURL = URL.createObjectURL(e.target.files[0]);
        $("#coverPreview").css('background-image', 'url(' + imgURL + ')');
    });
});

// Preview logo
$(document).ready(() => {
    "use strict";

    const logoInp = $("#logo");
    let imgURL;

    logoInp.change(function (e) {
        $("#logoNone").removeClass('d-none');
        $("#coverNone").removeClass('d-none');
        // Remove unwanted space
        $(".unwanted-space").remove();

        imgURL = URL.createObjectURL(e.target.files[0]);
        $("#logoPreview").css('background-image', 'url(' + imgURL + ')');
    });


    $("#coverType").change( function (){

        if(this.value == "vimeo-ap" || this.value == "vimeo"){
            $("#cover_url_title").text("https://vimeo.com/");
            $("#cover_url_label").attr("class", "form-label required");
            $("#cover_url_input").prop("required", true);
            $("#coverNone").css('display', 'none');
            // $("#logoNone").addClass('d-none');
            $("#coverChooser").addClass('d-none');
            $("#cover").prop("required", false);
            $("#cover_url_col").css("display", "block");
            // Remove unwanted space
            $(".unwanted-space").remove();
            $(".cover-unwanted-space").remove();
        }

        if(this.value == "youtube-ap" || this.value == "youtube"){
            $("#cover_url_title").text("https://www.youtube.com/watch?v=");
            $("#cover_url_label").attr("class", "form-label required");
            $("#cover_url_input").prop("required", true);
            $("#coverNone").css('display', 'none');
            // $("#logoNone").addClass('d-none');
            $("#coverChooser").addClass('d-none');
            $("#cover").prop("required", false);
            $("#cover_url_col").css("display", "block");
            // Remove unwanted space
            $(".unwanted-space").remove();
            $(".cover-unwanted-space").remove();
        }

        if(this.value == "photo"){
            $("#cover_url_label").attr("class", "form-label");
            $("#cover_url_input").prop("required", false);
            $("#cover").prop("required", true);
            // $("#logoNone").removeClass('d-none');
            $("#coverNone").css('display', 'block');
            $("#coverChooser").removeClass('d-none');
            $("#cover_url_col").css("display", "none");
        }

        if(this.value == "none"){
            $("#cover_url_label").attr("class", "form-label");
            $("#cover_url_input").prop("required", false);
            $("#coverNone").css('display', 'none');
            $("#coverChooser").addClass('d-none');
            $("#cover_url_col").css("display", "none");
            $("#cover").prop("required", false);
            // Remove unwanted space
            $(".unwanted-space").remove();
            $(".cover-unwanted-space").remove();
        }

    });

    $("#coverType").val("photo").change();

});

var APP_URL = '{{ config('app.url') }}';

$(document).ready(function() {
"use strict";

$('#searchInput').on('keyup', function() {
    "use strict";

    let query = $(this).val();
    let type = 'vCard';

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

    // Tiny MCE
    tinymce.init({
        selector: 'textarea#description',
        plugins: 'preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons',
        menubar: 'file edit view insert format tools',
        toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | preview save print | insertfile link anchor | ltr rtl',
        content_style: 'body { font-family:Times New Roman,Arial,sans-serif; font-size:16px }',
        height : "295",
        menubar: true,
        statusbar: false,
    });
</script>
<script>
    // Array of element selectors
    var elementSelectors = ['.card_lang', '.cover_type'];

    // Function to initialize TomSelect and enforce the "required" attribute
    function initializeTomSelectWithRequired(el) {
        new TomSelect(el, {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            maxOptions: null,
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

    // Loop through each element ID
    elementSelectors.forEach(function(id) {
        // Check if the element exists
        var elements = document.querySelectorAll(id);
        if (elements) {
            // Apply TomSelect and enforce the "required" attribute
            elements.forEach(function(el) {
                initializeTomSelectWithRequired(el);
            });
        }
    });
</script>
@endpush
@endsection
