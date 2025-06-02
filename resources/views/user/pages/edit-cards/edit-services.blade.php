@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

@section('css')
    <link href="{{ asset('css/dropzone.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/clipboard.min.js') }}"></script>
    <style>
        .btn-group-sm>.btn,
        .btn-sm {
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

        .btn.disabled,
        .btn:disabled,
        fieldset:disabled .btn {
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
        <div class="page-body">
            <div class="container-fluid">
                {{-- Failed --}}
                @if (Session::has('failed'))
                    <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('failed') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Success --}}
                @if (Session::has('success'))
                    <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('success') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Success of modal --}}
                <div id="successMessage" style="display:none;"
                    class="alert alert-important alert-success alert-dismissible mb-2">
                </div>

                {{-- Failed of modal --}}
                <div id="errorMessage" style="display:none;"
                    class="alert alert-important alert-danger alert-dismissible mb-2">
                </div>

                <div class="card">
                    <div class="row g-0">
                        <div class="col-12 col-md-3 border-end">
                            <div class="card-body">
                                <h4 class="subheader">{{ __('Update Business Card') }}</h4>
                                <div class="list-group list-group-transparent">
                                    {{-- Nav links --}}
                                    @include('user.pages.edit-cards.includes.nav-link', [
                                        'link' => 'services',
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-9 d-flex flex-column">
                            <div class="card-body m-0 py-0 px-2">
                                <div class="row align-items-center pt-3 px-2">
                                    <div class="col">
                                        <h2 class="card-title">
                                            {{ __('Services') }}
                                        </h2>
                                    </div>
                                    <div class="col-auto ms-auto d-print-none">
                                        <button type="button" class="btn btn-icon btn-primary" onclick="addService()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon d-lg-none d-inline"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            <span class="d-lg-inline d-none">{{ __('Create new') }}</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Services --}}
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="servicesTable" class="table table-vcenter border">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('#') }}</th>
                                                    <th>{{ __('Image') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Description') }}</th>
                                                    <th>{{ __('Inquiry') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <div class="d-flex">
                                  <a href="{{ route('user.cards') }}" class="btn btn-outline-primary ms-2">{{ __('Cancel') }}</a>
                                  <a href="{{ route('user.edit.vproducts', Request::segment(3)) }}" class="btn btn-primary ms-auto">{{ __('Skip') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.includes.footer')
    </div>

    {{-- Add service --}}
    <div class="modal modal-blur fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">{{ __('Add Service') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addServiceForm">
                        <input type="hidden" id="cardId" value="{{ $business_card->card_id }}">
                        {{-- Service Image --}}
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Service Image') }}</label>
                            <div class="input-group mb-2">
                                <input type="text" class="image form-control" id="serviceImage"
                                    placeholder="{{ __('Service Image') }}" required>
                                <button class="btn btn-primary btn-icon" type="button"
                                    onclick="openMedia()">{{ __('Choose image') }}</button>
                            </div>
                        </div>
                        {{-- Service name --}}
                        <div class="mb-3">
                            <label for="serviceName" class="form-label required">{{ __('Service Name') }}</label>
                            <input type="text" class="form-control" id="serviceName" name="service_name" required>
                        </div>
                        {{-- Service Description --}}
                        <div class="mb-3">
                            <label for="serviceDescription"
                                class="form-label required">{{ __('Service Description') }}</label>
                            <input type="text" class="form-control" id="serviceDescription"
                                name="service_description" required>
                        </div>
                        {{-- Inquiry Button --}}
                        <div class="mb-3">
                            <label class="form-label required" for="enquiry">{{ __('Inquiry Button') }}</label>
                            <select id="serviceInquiry" class="form-control enquiry mb-2"
                                {{ $whatsAppNumberExists != true ? 'disabled' : '' }}>
                                <option value="Enabled" selected>{{ __('Enabled') }}</option>
                                <option value="Disabled">{{ __('Disabled') }}</option>
                            </select>

                            {{-- Check whatsapp number exists --}}
                            @if ($whatsAppNumberExists != true)
                                <p class="h6">
                                    {{ __("'Inquiry button' is disabled as you have not entered whatsapp number. Go to the 'Social Links' page and enter the WhatsApp number.") }}
                                </p>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="saveService()">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Update service --}}
    <div class="modal modal-blur fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceModalLabel">{{ __('Edit Service') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm">
                        <input type="hidden" id="serviceId">
                        {{-- Service Image --}}
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Service Image') }}</label>
                            <div class="input-group mb-2">
                                <input type="text" class="image form-control" id="editServiceImage"
                                    placeholder="{{ __('Service Image') }}" required>
                                <button class="btn btn-primary btn-icon" type="button"
                                    onclick="openMedia()">{{ __('Choose image') }}</button>
                            </div>
                        </div>
                        {{-- Service name --}}
                        <div class="mb-3">
                            <label for="editServiceName" class="form-label required">{{ __('Service Name') }}</label>
                            <input type="text" class="form-control" id="editServiceName" name="service_name"
                                required>
                        </div>
                        {{-- Service Description --}}
                        <div class="mb-3">
                            <label for="editServiceDescription"
                                class="form-label required">{{ __('Service Description') }}</label>
                            <input type="text" class="form-control" id="editServiceDescription"
                                name="service_description" required>
                        </div>
                        {{-- Inquiry Button --}}
                        <div class="mb-3">
                            <label class="form-label required" for="enquiry">{{ __('Inquiry Button') }}</label>
                            <select id="editServiceInquiry" class="form-control enquiry mb-2" {{ $whatsAppNumberExists != true ? 'disabled' : '' }}>
                                <option value="Enabled">{{ __('Enabled') }}</option>
                                <option value="Disabled">{{ __('Disabled') }}</option>
                            </select>

                            {{-- Check whatsapp number exists --}}
                            @if ($whatsAppNumberExists != true)
                                <p class="h6">
                                    {{ __("'Inquiry button' is disabled as you have not entered whatsapp number. Go to the 'Social Links' page and enter the WhatsApp number.") }}
                                </p>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="updateService()">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete service modal -->
    <div class="modal modal-blur fade" id="deleteServiceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <h3>{{ __('Are you sure you want to delete this service?') }}</h3>
                    <div id="delete_status" class="text-muted">{{ __('Are you sure you want to delete this service?') }}</div>
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
                                <a class="btn btn-danger w-100" id="confirmDeleteButton">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <!-- Initialize DataTables -->
        <script>
            // Get services
            $(document).ready(function() {
                "use strict";
                $('#servicesTable').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.edit.services', $business_card->card_id) }}", // Replace with your actual API endpoint
                        dataSrc: 'data' // If your data is nested under a key 'data' in the response
                    },
                    language: {
                        "sProcessing": `{{ __("Processing...") }}`,
                        "sLengthMenu": `{{ __("Show _MENU_ entries") }}`,
                        "sSearch": `{{ __("Search:") }}`,
                        "oPaginate": {
                            "sNext": `{{ __("Next") }}`,
                            "sPrevious": `{{ __("Previous") }}`
                        },
                        "sInfo": `{{ __("Showing _START_ to _END_ of _TOTAL_ entries") }}`,
                        "sInfoEmpty": `{{ __("Showing 0 to 0 of 0 entries") }}`,
                        "sInfoFiltered": `{{ __("(filtered from _MAX_ total entries)") }}`,
                        "sInfoPostFix": "",
                        "sUrl": "",
                        "oAria": {
                            "sSortAscending": `{{ __(": activate to sort column in ascending order") }}`,
                            "sSortDescending": `{{ __(": activate to sort column in descending order") }}`
                        },
                        loadingRecords: `{{ __("Please wait - loading...") }}`,
                        emptyTable: `{{ __("No data available in the table") }}` // Message for an empty table
                    },
                    pageLength: 5,
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'service_image',
                            name: 'service_image',
                            render: function(data, type, full, meta) {
                                return '<img src="' + data +
                                    '" alt="Service Image" style="width: 50px; height: 50px;"/>';
                            }
                        },
                        {
                            data: 'service_name',
                            name: 'service_name'
                        },
                        {
                            data: 'service_description',
                            name: 'service_description'
                        },
                        {
                            data: 'service_enquiry',
                            name: 'service_enquiry'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });

            // Open Media modal
            function openMedia() {
                "use strict";
                $('#openMediaModel').modal('show');
            }

            // Open add service modal
            function addService() {
                "use strict";
                $('#addServiceModal').modal('show');
            }

            // Save service
            function saveService() {
                "use strict";
                var cardId = $('#cardId').val();
                var serviceImage = $('#serviceImage').val();
                var serviceName = $('#serviceName').val();
                var serviceDescription = $('#serviceDescription').val();
                var serviceInquiry = $('#serviceInquiry').val();

                $.ajax({
                    url: '{{ route('user.save.service') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        card_id: cardId,
                        service_image: serviceImage,
                        service_name: serviceName,
                        service_description: serviceDescription,
                        service_enquiry: serviceInquiry,
                    },
                    success: function(response) {
                        if (response.success) {
                            // Display success message
                            $('#successMessage').text("{{ __('New Service added successfully!') }}").show();
                            $('#successMessage').delay(1000).fadeOut(100);
                            $('#addServiceModal').modal('hide');
                            $('#addServiceForm').trigger("reset");

                            // Reload service table
                            $('#servicesTable').DataTable().ajax.reload();
                        } else {
                            // Display error message
                            $('#addServiceModal').modal('hide');
                            $('#errorMessage').text("{{ __('Failed to add service.') }}").show();
                            $('#errorMessage').delay(1500).fadeOut(200);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        $('#addServiceModal').modal('hide');
                        $('#errorMessage').text("{{ __('Something went wrong!') }}").show();
                        $('#errorMessage').delay(1500).fadeOut(200);
                    }
                });
            }

            // Edit service modal
            function editService(serviceId) {
                "use strict";
                $.ajax({
                    url: '/user/get-service/' + serviceId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#serviceId').val(response.data.id);
                            $('#editServiceImage').val(response.data.service_image);
                            $('#editServiceName').val(response.data.service_name);
                            $('#editServiceDescription').val(response.data.service_description);
                            $('#editServiceInquiry').val(response.data.enable_enquiry);
                            $('#editServiceModal').modal('show');
                        } else {
                            // Display error message
                            $('#addServiceModal').modal('hide');
                            $('#errorMessage').text("{{ __('Failed to fetch service data.') }}").show();
                            $('#errorMessage').delay(1500).fadeOut(200);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        $('#addServiceModal').modal('hide');
                        $('#errorMessage').text("{{ __('Something went wrong!') }}").show();
                        $('#errorMessage').delay(1500).fadeOut(200);
                    }
                });
            }

            // Update service
            function updateService() {
                "use strict";
                var serviceId = $('#serviceId').val();
                var serviceImage = $('#editServiceImage').val();
                var serviceName = $('#editServiceName').val();
                var serviceDescription = $('#editServiceDescription').val();
                var serviceInquiry = $('#editServiceInquiry').val();

                $.ajax({
                    url: '{{ route('user.update.service') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        service_id: serviceId,
                        service_image: serviceImage,
                        service_name: serviceName,
                        service_description: serviceDescription,
                        service_enquiry: serviceInquiry,
                    },
                    success: function(response) {
                        if (response.success) {
                            // Display success message
                            $('#successMessage').text("{{ __('Service updated successfully!') }}").show();
                            $('#successMessage').delay(1000).fadeOut(100);
                            $('#editServiceModal').modal('hide');

                            // Reload service table
                            $('#servicesTable').DataTable().ajax.reload();
                        } else {
                            // Display error message
                            $('#addServiceModal').modal('hide');
                            $('#errorMessage').text("{{ __('Failed to update service.') }}").show();
                            $('#errorMessage').delay(1500).fadeOut(200);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        $('#addServiceModal').modal('hide');
                        $('#errorMessage').text("{{ __('Something went wrong!') }}").show();
                        $('#errorMessage').delay(1500).fadeOut(200);
                    }
                });
            }

            // Function to delete the service
            function deleteService(serviceId) {
                $('#deleteServiceModal').data('service-id', serviceId).modal('show');
            }

            // jQuery to handle the modal "Okay" button click
            $(document).ready(function() {
                $('#confirmDeleteButton').click(function() {
                    deleteConfirmService();
                });
            });

            // Confirm delete service
            function deleteConfirmService() {
                var serviceId = $('#deleteServiceModal').data('service-id');
                $.ajax({
                    url: '/user/delete-service/' + serviceId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Display success message
                        $('#successMessage').text("{{ __('Service deleted successfully!') }}").show();
                        $('#successMessage').delay(1000).fadeOut(100);
                        $('#deleteServiceModal').modal('hide');

                        // Reload service table
                        $('#servicesTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        // Display error message
                        $('#addServiceModal').modal('hide');
                        $('#errorMessage').text("{{ __('Error deleting service') }}").show();
                        $('#errorMessage').delay(1500).fadeOut(200);
                    }
                });
            }
        </script>

        {{-- Upload image in dropzone --}}
        <script type="text/javascript">
            Dropzone.options.dropzone = {
                maxFilesize: {{ env('SIZE_LIMIT') / 1024 }},
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
                    data: {
                        page: page
                    },
                    dataType: 'json',
                    success: handleMediaResponse,
                    error: function(xhr, status, error) {
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
                mediaData.forEach(function(media) {
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
            $(document).ready(function() {
                "use strict";

                loadMedia(); // Initial load
            });
        </script>

        {{-- Clipboard --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                "use strict";

                var clipboard = new ClipboardJS('.copyBoard');

                // Success
                clipboard.on('success', function(e) {
                    "use strict";

                    // Place value in the field
                    $('.image').val(e.text);

                    // Hide media modal
                    $('#openMediaModel').modal('hide');
                });

                // Error
                clipboard.on('error', function(e) {
                    "use strict";

                    showErrorAlert('{{ __('Failed to copy text to clipboard. Please try again.') }}');
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

                    setTimeout(function() {
                        "use strict";

                        alertDiv.remove();
                    }, 3000);
                }
            });
        </script>
    @endpush
@endsection
