@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js"
    integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                            {{ __('New Withdrawal Request') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-fluid">
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

                {{-- New Withdrawal Request --}}
                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('New Withdrawal Request') }}</h3>
                            </div>
                            <form action="{{ route('user.save.withdrawal.request') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <div class="row">
                                                {{-- Bank Details --}}
                                                <div class="col-sm-12 col-lg-12 mb-3">
                                                    <div class="form-group">
                                                        <label
                                                            class="form-label required">{{ __('Bank Details') }}</label>
                                                        <textarea name="bank_details" class="form-control" id="bank_details" rows="5" required>{{ $bankDetails }}</textarea>
                                                    </div>
                                                </div>

                                                {{-- Current Balance --}}
                                                <div class="col-sm-12 col-lg-6 mb-3">
                                                    <div class="form-group">
                                                        <label
                                                            class="form-label required">{{ __('Current Balance') }}</label>
                                                        <input type="number" class="form-control" name="amount" id="amount" min="0" max="{{ $currentBalance }}" step="0.01"
                                                            value="{{ $currentBalance }}" required>
                                                    </div>                                                    
                                                </div>

                                                {{-- Notes --}}
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="form-group">
                                                        <label
                                                            class="form-label">{{ __('Notes') }}</label>
                                                        <textarea class="form-control" name="notes" id="notes" rows="3">{{ old('notes') }}</textarea>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">{{ __('Request') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('user.includes.footer')
    </div>

    {{-- Custom Script --}}
    @section('scripts')
    <script>
        tinymce.init({
            selector: 'textarea#bank_details',
            plugins: 'code preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | pagebreak | link',
            toolbar_sticky: true,
            height: 200,
            menubar: false,
            statusbar: false,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        });
    </script>
    @endsection
@endsection