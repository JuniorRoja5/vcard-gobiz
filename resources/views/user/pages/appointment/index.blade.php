@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

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
                            {{ __('Appointments') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
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

                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="appointmentsTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Created') }}</th>
                                            <th>{{ __('Customer Name') }}</th>
                                            <th>{{ __('Customer Email') }}</th>
                                            <th>{{ __('Customer Phone') }}</th>
                                            <th>{{ __('Appointment Date') }}</th>
                                            <th>{{ __('Appointment Time') }}</th>
                                            <th>{{ __('Notes') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="w-1">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Appointments --}}
                                        @foreach ($bookedAppointments as $appointment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $appointment->created_at->diffForHumans() }}</td>
                                                <td><strong>{{ $appointment->name }}</strong></td>
                                                <td><strong><a
                                                            href="mailto:{{ $appointment->email }}">{{ $appointment->email }}</a></strong>
                                                </td>
                                                <td><strong><a
                                                            href="tel:{{ $appointment->phone }}">{{ $appointment->phone }}</a></strong>
                                                </td>
                                                <td><strong>{{ $appointment->booking_date }}</strong></td>
                                                <td><strong>{{ $appointment->booking_time }}</strong></td>
                                                <td><strong>{{ $appointment->notes ?? '-' }}</strong></td>
                                                <td>
                                                    @if ($appointment->booking_status == 0)
                                                        <span
                                                            class="badge bg-warning text-white text-white">{{ __('Pending') }}</span>
                                                    @endif
                                                    @if ($appointment->booking_status == 1)
                                                        <span
                                                            class="badge bg-primary text-white text-white">{{ __('Confirmed') }}</span>
                                                    @endif
                                                    @if ($appointment->booking_status == 2)
                                                        <span
                                                            class="badge bg-success text-white text-white">{{ __('Completed') }}</span>
                                                    @endif
                                                    @if ($appointment->booking_status == -1)
                                                        <span
                                                            class="badge bg-red text-white text-white">{{ __('Canceled') }}</span>
                                                    @endif
                                                </td>
                                                <td class="w-1">
                                                    @if ($appointment->booking_status != 2)
                                                        <div class="btn-list flex-nowrap">
                                                            <div class="dropdown">
                                                                <button class="btn btn-icon btn-icon align-text-top"
                                                                    data-bs-boundary="viewport" data-bs-toggle="dropdown"
                                                                    aria-expanded="false">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-dots-vertical">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path
                                                                            d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                        <path
                                                                            d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                        <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                    </svg>
                                                                </button>
                                                                <div class="actions actions dropdown-menu dropdown-menu-end"
                                                                    style="">
                                                                    {{-- Accept the appointment --}}
                                                                    <a onclick="acceptAppointment(`{{ $appointment->booked_appointment_id }}`)"
                                                                        class="dropdown-item">
                                                                        {{ __('Update status') }}
                                                                    </a>
                                                                    {{-- Reschedule the appointment --}}
                                                                    <a onclick="rescheduleAppointment(`{{ $appointment->booked_appointment_id }}`, `{{ $appointment->booking_date }}`)"
                                                                        class="dropdown-item">
                                                                        {{ __('Reschedule') }}
                                                                    </a>
                                                                    {{-- Complete the appointment --}}
                                                                    <a onclick="completeAppointment(`{{ $appointment->booked_appointment_id }}`)"
                                                                        class="dropdown-item">
                                                                        {{ __('Complete') }}
                                                                    </a>
                                                                    {{-- Add my google calendar --}}
                                                                    <a onclick="addMyGoogleCalendar(`{{ $appointment->booked_appointment_id }}`)"
                                                                        class="dropdown-item">
                                                                        {{ __('Add My Google Calendar') }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.includes.footer')
    </div>

    {{-- Accept or cancel appointment --}}
    <div class="modal modal-blur fade" id="acceptAppointmentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
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
                    <div id="accept_appointment_status"></div>
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
                                <a class="btn btn-danger w-100" id="cancel_appointment_id">
                                    {{ __('Reject') }}
                                </a>
                            </div>
                            <div class="col">
                                <a class="btn btn-success w-100" id="accept_appointment_id">
                                    {{ __('Accept') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Complete appointment --}}
    <div class="modal modal-blur fade" id="completeAppointmentModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div id="complete_appointment_status"></div>
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
                                <a class="btn btn-danger w-100" id="complete_appointment_id">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Reschedule appointment modal --}}
    <div class="modal modal-blur fade" id="rescheduleAppointmentModal" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                {{-- Reschedule date and time --}}
                <form action="{{ route('user.reschedule.appointment') }}" method="post" class="card">
                    @csrf
                    <div class="modal-header">
                        <div class="modal-title">{{ __('Reschedule appointment') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" class="form-control" name="booked_appointment_id"
                                id="booked_appointment_id">
                            <label class="form-label">{{ __('Date') }}</label>
                            <div class="input-group mb-2">
                                <input type="date" name="date" id="date" class="form-control"
                                    placeholder="{{ __('Date') }}" required>
                                <span class="input-group-text">
                                    {{ __('Date') }}
                                </span>
                            </div>
                            <label class="form-label">{{ __('Time') }}</label>
                            <div class="input-group mb-2">
                                <input type="time" name="time" id="time" class="form-control"
                                    placeholder="{{ __('Time') }}" required>
                                <span class="input-group-text">
                                    {{ __('Time') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-outline-primary me-auto"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Reschedule') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add my google calendar --}}
    <div class="modal modal-blur fade" id="addMyGoogleCalendarModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus mb-2 text-danger icon-md">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                        <path d="M16 3v4" />
                        <path d="M8 3v4" />
                        <path d="M4 11h16" />
                        <path d="M16 19h6" />
                        <path d="M19 16v6" />
                    </svg>
                    <h3>{{ __('Add my Google Calendar') }}</h3>
                    <div id="add_my_google_calendar_status"></div>
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
                                <a class="btn btn-danger w-100" id="add_my_google_calendar_id" target="_blank">
                                    {{ __('Add') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom JS --}}
@section('scripts')
    <script>
        $('#appointmentsTable').DataTable({
            language: {
                "sProcessing": `{{ __('Processing...') }}`,
                "sLengthMenu": `{{ __('Show _MENU_ entries') }}`,
                "sSearch": `{{ __('Search:') }}`,
                "oPaginate": {
                    "sNext": `{{ __('Next') }}`,
                    "sPrevious": `{{ __('Previous') }}`
                },
                "sInfo": `{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}`,
                "sInfoEmpty": `{{ __('Showing 0 to 0 of 0 entries') }}`,
                "sInfoFiltered": `{{ __('(filtered from _MAX_ total entries)') }}`,
                "sInfoPostFix": "",
                "sUrl": "",
                "oAria": {
                    "sSortAscending": `{{ __(': activate to sort column in ascending order') }}`,
                    "sSortDescending": `{{ __(': activate to sort column in descending order') }}`
                },
                loadingRecords: `{{ __('Please wait - loading...') }}`,
                emptyTable: `{{ __('No data available in the table') }}` // Message for an empty table
            },
        });

        // Accept appointment
        function acceptAppointment(id) {
            "use strict";

            $("#acceptAppointmentModal").modal("show");
            var accept_appointment_status = document.getElementById("accept_appointment_status");
            accept_appointment_status.innerHTML = "<?php echo __('If you proceed, you will accept this appointment.'); ?>"
            var accept_appointment_link = document.getElementById("accept_appointment_id");
            accept_appointment_link.getAttribute("href");
            accept_appointment_link.setAttribute("href", "{{ route('user.accept.appointment') }}?id=" + id);
            var cancel_appointment_link = document.getElementById("cancel_appointment_id");
            cancel_appointment_link.getAttribute("href");
            cancel_appointment_link.setAttribute("href", "{{ route('user.cancel.appointment') }}?id=" + id);
        }

        // Complete appointment
        function completeAppointment(id) {
            "use strict";

            $("#completeAppointmentModal").modal("show");
            var complete_appointment_status = document.getElementById("complete_appointment_status");
            complete_appointment_status.innerHTML = "<?php echo __('If you proceed, you will complete this appointment.'); ?>"
            var complete_appointment_link = document.getElementById("complete_appointment_id");
            complete_appointment_link.getAttribute("href");
            complete_appointment_link.setAttribute("href", "{{ route('user.complete.appointment') }}?id=" + id);
        }

        // Reschedule appointment
        function rescheduleAppointment(booked_appointment_id, date) {
            "use strict";

            // Convert the date string into a Date object
            const dateObj = new Date(date);

            // Format the date to 'yyyy-mm-dd'
            const formattedDate = formatDateToYMD(dateObj);

            // Show the modal
            $("#rescheduleAppointmentModal").modal("show");

            // Get the input fields inside the modal
            var bookedAppointmentInput = document.getElementById("booked_appointment_id");
            var dateInput = document.getElementById("date");

            // Set the values of the input fields
            bookedAppointmentInput.value = booked_appointment_id;
            dateInput.value = formattedDate;
        }

        // Helper function to format date to 'dd mm yyyy'
        function formatDateToYMD(date) {
            const y = date.getFullYear();
            const m = date.getMonth() + 1; // Months are zero-indexed in JavaScript (0-11)
            const d = date.getDate();

            // Pad day and month with leading zeros if necessary
            const day = d < 10 ? '0' + d : d;
            const month = m < 10 ? '0' + m : m;

            return `${y}-${month}-${day}`;
        }

        // Add my google calendar
        function addMyGoogleCalendar(booked_appointment_id) {
            "use strict";

            $("#addMyGoogleCalendarModal").modal("show");
            var add_my_google_calendar_status = document.getElementById("add_my_google_calendar_status");
            add_my_google_calendar_status.innerHTML = "<?php echo __('If you proceed, you will add this appointment to your Google Calendar.'); ?>"
            var add_my_google_calendar_link = document.getElementById("add_my_google_calendar_id");
            add_my_google_calendar_link.getAttribute("href");
            add_my_google_calendar_link.setAttribute("href", "{{ route('user.add.my.google.calendar') }}?id=" +
                booked_appointment_id);
        }
    </script>
@endsection
@endsection
