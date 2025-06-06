@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

{{-- Custom CSS --}}
@section('css')
<link href="{{ asset('css/dropzone.min.css')}}" rel="stylesheet">
<script src="{{ asset('js/dropzone.min.js')}}"></script>
<script src="{{ asset('js/clipboard.min.js') }}"></script>
<style>
    .btn-group-sm>.btn, .btn-sm {
        --tblr-btn-line-height: 1.5;
        --tblr-btn-icon-size: .75rem;
        margin-right: 5px;
        font-size: 12px !important;
        margin: 13px 0 10px 5px !important;
    }

    .li-link {
        padding: 10px;
        margin: 4px;
    }

    .btn.disabled, .btn:disabled, fieldset:disabled .btn {
        border-color: #0000 !important;
    }

    .custom-nav {
        position: absolute;
        right: 5px;
        top: -2px;
    }

    .media-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection

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
                        {{ __('Services') }}
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
                    <form action="{{ route('user.save.services', Request::segment(3)) }}" method="post"
                        enctype="multipart/form-data" class="card">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div id="service" class="row">
                                        <div id="more-services" class="row"></div>

                                        <div class="col-lg-12">
                                            <button type="button" onclick="addService()" class="btn btn-primary">
                                                {{ __('Add One More Service') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <div class="d-flex">
                              <a href="{{ route('user.vproducts', Request::segment(3)) }}" class="btn btn-outline-primary ms-2">{{ __('Skip') }}</a>
                              <button type="submit" class="btn btn-primary ms-auto">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('user.includes.footer')
</div>

{{-- Media Library --}}
<div class="modal modal-blur fade" id="openMediaModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full-width modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <h3 class="mb-2">{{ __('Media Library') }}</h3>
                <div class="text-muted mb-5">
                    {{ __('Upload multiple images') }}
                </div>

                {{-- Upload multiple images --}}
                @include('user.pages.cards.media.upload')

                {{-- Upload multiple images --}}
                @include('user.pages.cards.media.list')

                {{-- Pagination --}}
                <div id="pagination"></div>
            </div>
        </div>
    </div>
</div>

{{-- Custom JS --}}
@push('custom-js')
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
<script>
    var count = 0;
    var currentSelection = 0;
    function addService() {
	"use strict";
    if (count >= {{ $plan_details->no_of_services}}) {
        new swal({
            title: `{{ __('Oops!') }}`,
            icon: 'warning',
            text: `{{ __('You have reached your current plan limit.') }}`,
            timer: 2000,
            buttons: false,
            showConfirmButton: false,
        });
    }
    else {
        count++;
        var id = getRandomInt();
        var services = `<div class='row' id=`+ id +`>
            <div class='col-md-6 col-xl-6'>
                <div class='mb-3'>
                    <label class='form-label required'>{{ __('Service Image') }}</label>
                    <div class='input-group mb-2'>
                        <input type='text' class='image`+ id +` media-model form-control' name='service_image[]' placeholder='{{ __('Service Image') }}' required>
                        <button class='btn btn-primary btn-md' type='button' onclick='openMedia(`+ id +`)'>{{ __('Choose image') }}</button>
                    </div>
                </div>
            </div>
            
            <div class='col-md-6 col-xl-6'>
                <div class='mb-3'> <label class='form-label required'>{{ __('Service Name') }}</label>
                    <input type='text' class='form-control' name='service_name[]' placeholder='{{ __('Service Name') }}' required>
                </div>
            </div>
            
            <div class='col-md-6 col-xl-6'>
                <div class='mb-3'> 
                    <label class='form-label required'>{{ __('Service Description') }}</label>
                    <input type='text' class='form-control' name='service_description[]' placeholder='{{ __('Service Description') }}' required>
                </div>
            </div>
            
            <div class='col-md-6 col-xl-6'>
                <div class='mb-3'>
                    <label class='form-label' for='enquiry'>{{ __('Inquiry Button') }}</label>
                    <select name='enquiry[]' id='enquiry' class='form-select enquiry' {{ $whatsAppNumberExists != true ? "disabled" : "" }} required>
                        <option value='Enabled'>{{ __('Enabled') }}</option>
                        <option value='Disabled' {{ $whatsAppNumberExists != true ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                    </select>

                    {{-- Check whatsapp number exists --}}
                    @if ($whatsAppNumberExists != true)
                    <p class="h6">{{ __("'Inquiry button' is disabled as you have not entered whatsapp number. Go to the 'Social Links' page and enter the WhatsApp number.") }}</p>
                    @endif
                    
                    <a href='#' class='btn mt-3 btn-danger btn-sm' onclick='removeService(`+id+`)'>{{ __('Remove') }}</a>
                </div>
                <br>
            </div>
        </div>`;
        $("#more-services").append(services).html();
    }
    }

    // Remove service
    function removeService(id) {
	"use strict";
        $("#"+id).remove();
    }

    // Generate random number
    function getRandomInt() {
        min = Math.ceil(0);
        max = Math.floor(9999999999);
        return Math.floor(Math.random() * (max - min) + min); //The maximum is exclusive and the minimum is inclusive
    }

    // Open Media modal
    function openMedia(id){
        "use strict";
        currentSelection = id;
        $('#openMediaModel').modal('show');
    }
</script>
{{-- Upload image in dropzone --}}
<script type="text/javascript">
    Dropzone.options.dropzone = {
            maxFilesize  : {{ env('SIZE_LIMIT')/1024 }},
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            init: function() {
            this.on("success", function(file, response) {
                loadMedia();
            });
        }
        };
</script>

{{-- Media with pagination --}}
<script>
    // Default values
    var currentPage = 1;
    var totalPages = 1;

    // Previous image
    function loadPreviousPage() {
        "use strict";

        if (currentPage > 1) {
            currentPage--;
            loadMedia(currentPage);
        }
    }

    // Next page
    function loadNextPage() {
        "use strict";

        if (currentPage < totalPages) {
            currentPage++;
            loadMedia(currentPage);
        }
    }

    // Load media images
    function loadMedia(page = 1) {
        $.ajax({
            url: '{{ route('user.media') }}',
            method: 'GET',
            data: { page: page },
            dataType: 'json',
            success: handleMediaResponse,
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Media response
    function handleMediaResponse(response) {
        "use strict";
        
        var mediaData = response.media.data;
        if (mediaData.length > 0) {
            $('#noImagesFound').hide();
            $('#showPagination').removeClass('d-none').addClass('card pagination-card');
            displayMediaCards(mediaData);
            updatePaginationInfo(response.media);
        } else {
            $('#noImagesFound').show();
            $('#showPagination').addClass('d-none');
            $('#mediaCardsContainer').html('');
            updatePaginationInfo(response.media);
        }
    }

    // Display media images in card type
    function displayMediaCards(mediaData) {
        "use strict";
        
        // Generate media image
        var mediaCardsHtml = '';
        mediaData.forEach(function (media) {
            mediaCardsHtml += `
                <div class="col-md-2 mb-4">
                    <div class="card">
                        <img src="${media.base_url}${media.media_url}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="${media.media_name}">
                        <div class="card-body">
                            <h5 class="card-title media-name">${media.media_name}</h5>
                            <a class="btn btn-icon btn-primary btn-md copyBoard" data-clipboard-text="${media.media_url}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Copy') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="8" y="8" width="12" height="12" rx="2"></rect>
                                    <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#mediaCardsContainer').html(mediaCardsHtml);
    }

    // Update pagination
    function updatePaginationInfo(media) {
        "use strict";
        
        $('#paginationStartIndex').text(media.from);
        $('#paginationEndIndex').text(media.to);
        $('#paginationTotalCount').text(media.total);
        currentPage = media.current_page;
        totalPages = media.last_page;

        $('#prevPageBtn').prop('disabled', currentPage <= 1);
        $('#nextPageBtn').prop('disabled', currentPage >= totalPages);
    }

    // Load more image in pagination
    $(document).ready(function () {
        "use strict";
        
        loadMedia(); // Initial load
    });
</script>

{{-- Clipboard --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        "use strict";
        
        var clipboard = new ClipboardJS('.copyBoard');

        // Success
        clipboard.on('success', function (e) {
            "use strict";

            // Place value in the field
            $('.image'+currentSelection).val(e.text);

            // Hide media modal
            $('#openMediaModel').modal('hide');
        });

        // Error
        clipboard.on('error', function (e) {
            "use strict";
        
            showErrorAlert('{{ __("Failed to copy text to clipboard. Please try again.") }}');
        });

        // Show success message
        function showSuccessAlert(message) {
            "use strict";
        
            showAlert(message, 'success');
        }

        // Show error message
        function showErrorAlert(message) {
            "use strict";
        
            showAlert(message, 'danger');
        }

        // Show alert
        function showAlert(message, type) {
            "use strict";
        
            var alertDiv = document.createElement('div');
            alertDiv.classList.add('alert', 'alert-important', 'alert-' + type, 'alert-dismissible');
            alertDiv.setAttribute('role', 'alert');

            var innerContent = '<div class="d-flex">' +
                '<div>' +
                message +
                '</div>' +
                '</div>' +
                '<a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>';

            alertDiv.innerHTML = innerContent;
            document.querySelector('#showAlert').appendChild(alertDiv);

            setTimeout(function () {
                "use strict";
        
                alertDiv.remove();
            }, 3000);
        }
    });
</script>

<script>
    // Array of element selectors
    var elementSelectors = ['.enquiry'];

    // Function to initialize TomSelect on an element
    function initializeTomSelect(el) {
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