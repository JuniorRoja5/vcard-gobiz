{{-- Alert message --}}
<div id="showAlert"></div>
            
{{-- Media images --}}
<div class="row" id="mediaCardsContainer"></div>

{{-- Empty media --}}
<div class="row" id="noImagesFound">
    <div class="col-sm-12 col-lg-12">
        <div class="container-fluid d-flex flex-column justify-content-center">
            <div class="empty">
                <div class="empty-img">
                    <img id="empty" src="{{ asset('img/empty.svg') }}" height="128" alt="">
                </div>
                <p class="empty-title">{{ __('No images found') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Pagination --}}
<div class="card pagination-card" id="showPagination">
    <div class="card-footer pagination-card-footer d-flex align-items-center" id="paginationLinks">
        <p class="m-0 text-muted">{{ __('') }} <span id="paginationStartIndex"></span> {{ __('to') }}
            <span id="paginationEndIndex"></span> {{ __('of') }} <span id="paginationTotalCount"></span> {{ __('results') }}
        </p>
        <nav class="custom-nav">
            <ul class="pagination">
                <li class="btn btn-sm btn-primary li-link" id="prevPageBtn" onclick="loadPreviousPage()">{{ __('Previous') }}</li>
                <li class="btn btn-sm btn-primary li-link" id="nextPageBtn" onclick="loadNextPage()">{{ __('Next') }}</li>
            </ul>
        </nav>
    </div>
</div>

{{-- Delete --}}
<div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <h3>{{ __('Are you sure?') }}</h3>
                <div id="delete_status" class="text-muted"></div>
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
                            <a class="btn btn-danger w-100" id="delete_id">
                                {{ __('Yes, proceed') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>