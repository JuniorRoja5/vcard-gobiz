@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
    {{-- TinyMCE CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js" integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                            {{ __('Create Campaign') }}
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
                        <form action="{{ route('admin.marketing.campaigns.save') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Campaign Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- Campaign Name --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Campaign Name') }}</div>
                                                    <input type="text" class="form-control" name="campaign_name" value="{{ old('campaign_name') }}"
                                                        placeholder="{{ __('Campaign Name') }}" required>
                                                </div>
                                            </div>

                                            {{-- Campaign Description --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Description') }}</div>
                                                    <input type="text" class="form-control" name="campaign_description" value="{{ old('campaign_description') }}"
                                                        placeholder="{{ __('Description') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header">
                                <h4 class="page-title">{{ __('Email Notification Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- Available Groups --}}
                                            @if (count($groups) > 0)
                                                {{-- Campaign Groups --}}
                                                <div class="col-md-12 col-xl-6">
                                                    <div class="mb-3">
                                                        <div class="form-label required">{{ __('Groups') }}</div>
                                                        <select class="form-control" id="group" name="group">
                                                            @foreach ($groups as $group)
                                                                <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-12 col-xl-6">
                                                    <div class="mb-3">
                                                        {{-- Heading --}}
                                                        <div class="form-label">{{ __('Groups') }}</div>
                                                        <div class="form-text fw-bold">{{ __('No groups found') }}</div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Email Subject --}}
                                            <div class="col-md-12 col-xl-6">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Email Subject') }}</div>
                                                    <input type="text" class="form-control" name="email_subject" value="{{ old('email_subject') }}"
                                                        placeholder="{{ __('Email Subject') }}" required>
                                                </div>
                                            </div>

                                            {{-- Email Body using TinyMCE --}}
                                            <div class="col-md-12 col-xl-12">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Email Body') }}</div>
                                                    <textarea class="form-control" name="email_body" id="email_body" rows="10">{{ old('email_body') }}</textarea>
                                                </div>
                                                {{-- Use #name to use the customer name --}}
                                                <span class="fw-bold">{{ __('Use #name to use the customer name.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary {{ count($groups) > 0 ? '' : 'disabled' }}">{{ __('Send') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.includes.footer')
    </div>
@endsection

{{-- Custom JS --}}
@section('scripts')
{{-- Tom Select --}}
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
<script>
    // Array of element IDs
    var elementSelectors = ['group'];

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
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties +
                            '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties +
                            '</span>' + escape(data.text) + '</div>';
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
        var el = document.getElementById(id);
        if (el) {
            // Apply TomSelect and enforce the "required" attribute
            initializeTomSelectWithRequired(el);
        }
    });
</script>
{{-- TinyMCE --}}
<script>
tinymce.init({
    selector: 'textarea#email_body',
    plugins: 'code preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons',
    menubar: 'file edit view insert format tools',
    toolbar: 'code undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | preview save print | insertfile link anchor | ltr rtl',
    content_style: 'body { font-family:Times New Roman,Arial,sans-serif; font-size:16px }',
    menubar: false,
    statusbar: false,
});
</script>
@endsection