@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

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
                        <h2 class="page-title mb-2">
                            {{ __('Email Templates') }}
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
                
                <div class="card">
                    <div class="row g-0">
                        <div class="col-12 col-md-3 border-end">
                            <div class="card-body">
                                <h4 class="subheader">{{ __('Templates') }}</h4>
                                <div class="list-group list-group-transparent">
                                    {{-- Nav links --}}
                                    @include('admin.pages.email-templates.includes.navlinks', ['link' => $email_templates->email_template_id])
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-9 d-flex flex-column">
                            <form action="{{ route('admin.update.email.template.content') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                {{-- Email Template ID --}}
                                <input type="hidden" name="email_template_id" value="{{ $email_templates->email_template_id }}">
                                
                                <div class="card-body">
                                    <h3 class="card-title mb-4">{{ __('Update Email Template') }}</h3>
                                
                                    <div class="row g-4">
                                        <!-- Hide Contact / Inquiry Form Toggle -->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Send Email Notifications to the customer?') }}</label>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled" {{ $email_templates->is_enabled == 1 ? 'checked' : '' }}>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Template Subject --}}
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Email Template Subject') }}</label>
                                                <input type="text" name="email_template_subject" id="email_template_subject" value="{{ $email_templates->email_template_subject }}" class="form-control" placeholder="{{ __('Template Subject') }}">
                                            </div>
                                        </div>

                                        {{-- Template Content --}}
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Email Template Content') }}</label>
                                                <textarea name="email_template_content" id="email_template_content" cols="30" rows="5"
                                                    class="form-control text-capitalize" placeholder="{{ __('Template Content') }}">  
                                                    {{ $email_templates->email_template_content }}
                                                </textarea>
                                            </div>
                                        </div>

                                        {{-- Short codes --}}
                                        <div class="col-xl-12">
                                            <h2 class="page-title my-3"> {{ __('Available Short Codes') }} </h2>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Short Code') }}</th>
                                                            <th>{{ __('Value') }}</th>
                                                            <th>{{ __('Short Code') }}</th>
                                                            <th>{{ __('Value') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="py-3">:status</td>
                                                            <td class="py-3 fw-bold">{{ __('Appointment Status') }}</td>
                                                            <td class="py-3">:hyperlink</td>
                                                            <td class="py-3 fw-bold">{{ __('vCard URL') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:vcardname</td>
                                                            <td class="py-3 fw-bold">{{ __('vCard Name') }}</td>
                                                            <td class="py-3">:appname</td>
                                                            <td class="py-3 fw-bold">{{ __('App/Website Name') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:appointmentdate</td>
                                                            <td class="py-3 fw-bold">{{ __('Appointment Date') }}</td>
                                                            <td class="py-3">:appointmenttime</td>
                                                            <td class="py-3 fw-bold">{{ __('Appointment Time') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:googlecalendarurl</td>
                                                            <td class="py-3 fw-bold">{{ __('Google Calendar URL') }}</td>
                                                            <td class="py-3">:customername</td>
                                                            <td class="py-3 fw-bold">{{ __('Customer Name') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:appointmentpageurl</td>
                                                            <td class="py-3 fw-bold">{{ __('Appointment Page (User View)') }}</td>
                                                            <td class="py-3">:previousdomain</td>
                                                            <td class="py-3 fw-bold">{{ __('Previous Domain') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:currentdomain</td>
                                                            <td class="py-3 fw-bold">{{ __('Current Domain') }}</td>
                                                            <td class="py-3">:receivername</td>
                                                            <td class="py-3 fw-bold">{{ __('Receiver Name') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:receiveremail</td>
                                                            <td class="py-3 fw-bold">{{ __('Receiver Email') }}</td>
                                                            <td class="py-3">:receiverphone</td>
                                                            <td class="py-3 fw-bold">{{ __('Receiver Phone') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:receivermessage</td>
                                                            <td class="py-3 fw-bold">{{ __('Receiver Message') }}</td>
                                                            <td class="py-3">:planname</td>
                                                            <td class="py-3 fw-bold">{{ __('Plan Name') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:plancode</td>
                                                            <td class="py-3 fw-bold">{{ __('Plan Code') }}</td>
                                                            <td class="py-3">:planprice</td>
                                                            <td class="py-3 fw-bold">{{ __('Plan Price') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:expirydate</td>
                                                            <td class="py-3 fw-bold">{{ __('Expiry Date') }}</td>
                                                            <td class="py-3">:registeredname</td>
                                                            <td class="py-3 fw-bold">{{ __('Registered Name') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:registeredemail</td>
                                                            <td class="py-3 fw-bold">{{ __('Registered Email') }}</td>
                                                            <td class="py-3">:otpnumber</td>
                                                            <td class="py-3 fw-bold">{{ __('Two Factor OTP Number') }}</td>
                                                        </tr>  
                                                        <tr>
                                                            <td class="py-3">:orderid</td>
                                                            <td class="py-3 fw-bold">{{ __('Order ID') }}</td>
                                                            <td class="py-3">:cardname</td>
                                                            <td class="py-3 fw-bold">{{ __('Card Name') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:cardprice</td>
                                                            <td class="py-3 fw-bold">{{ __('Card Price') }}</td>
                                                            <td class="py-3">:paymentstatus</td>
                                                            <td class="py-3 fw-bold">{{ __('Payment Status') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:deliverystatus</td>
                                                            <td class="py-3 fw-bold">{{ __('Delivery Status') }}</td>
                                                            <td class="py-3">:quantity</td>
                                                            <td class="py-3 fw-bold">{{ __('Quantity') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:trackingnumber</td>
                                                            <td class="py-3 fw-bold">{{ __('Tracking Number') }}</td>
                                                            <td class="py-3">:courierpartner</td>
                                                            <td class="py-3 fw-bold">{{ __('Courier Partner') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:orderpageurl</td>
                                                            <td class="py-3 fw-bold">{{ __('Order Page (User View)') }}</td>
                                                            <td class="py-3">:totalprice</td>
                                                            <td class="py-3 fw-bold">{{ __('Total Price') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:supportemail</td>
                                                            <td class="py-3 fw-bold">{{ __('Support Email') }}</td>
                                                            <td class="py-3">:supportphone</td>
                                                            <td class="py-3 fw-bold">{{ __('Support Phone') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3">:customeremail</td>
                                                            <td class="py-3 fw-bold">{{ __('Customer Email') }}</td>
                                                            <td class="py-3">:actionlink</td>
                                                            <td class="py-3 fw-bold">{{ __('Action Link') }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <p class="text-muted mt-3">{{ __('You can use these short codes in your email template.') }}</p>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Website CSS --}}
    @php
        $emailCss = asset('css/email.css');
        $emailCss1 = asset('css/email1.css');
    @endphp

    {{-- Custom JS --}}
    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js" integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            // HTML Editor
            tinymce.init({
                selector: '#email_template_content',
                plugins: 'code preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons',
                menubar: 'file edit view insert format tools',
                toolbar: 'code undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | preview save print | insertfile link anchor | ltr rtl',
                height: 600,
                menubar: false,
                statusbar: false,
                content_css: [
                    '{{ $emailCss }}',
                    '{{ $emailCss1 }}',
                    'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap'
                ],
            });
        </script>
    @endsection
@endsection