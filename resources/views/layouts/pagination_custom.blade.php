<div class="dataTables_wrapper">
    <div class="dataTable_wrapper--content">
        <div class="d-flex align-items-left">
            <nav class="dataTables_paginate paging_simple_numbers mobile" style="display: {{ $paginator->hasMorePages() ? 'block' : 'none' }}">
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled previous" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true"><i class="la la-angle-left" aria-hidden="true"></i></span>
                        </li>
                    @else
                        <li class="page-item previous">
                            <a class="page-link partner-job-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                                <i class="la la-angle-left" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page" data-page-index="{{ $page }}"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link partner-job-page-link" href="{{ $url }}" data-page-index="{{ $page }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item next">
                            <a class="page-link partner-job-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                                <i class="la la-angle-right" aria-hidden="true"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled next" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true"><i class="la la-angle-right" aria-hidden="true"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>

        <div class="dataTables_pager justify-content-sm-end mt-md-0 text-right mt-0 mobile {{ $paginator->hasMorePages() ? 'd-flex' : 'd-none' }}">
            <div class="dataTables_length">
                <label>
                    @php
                        $perPage = $paginator->perPage();
                    @endphp
                    <select id="paginationChangeLength" aria-controls="table" class="kt-custom-select form-control form-control-sm">
                        <option value="5" {{ $perPage == 5 ? 'selected' : ''}}>5 / {{ __('message.page') }}</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : ''}} >10 / {{ __('message.page') }}</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : ''}}>25 / {{ __('message.page') }}</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : ''}}>50 / {{ __('message.page') }}</option>
                    </select>
                </label>
            </div>
            @isset($showTotal)
            <div class="dataTables_info" id="job-table_info" role="status" aria-live="polite">{{ $paginator->total() }} {{ __('message.record') }}</div>
            @endisset
            @isset($showGoto)
                <div class="d-flex">
                    <span class="mr-1" style="line-height: 33px;">{{ __('message.go_to') }}</span>
                    <input type="text" id="pageJump" class="form-control form-control-sm fw-500 fs-15 go-to-input">
                </div>
            @endif
        </div>
    </div>
</div>

@push('script')
    <script>
        $(function() {
            let url = window.location.href
            $('#paginationChangeLength').on('change', function() {
                window.location.replace(UpdateQueryString('length', $(this).val()), url)
            })
            $('#pageJump').on('keypress',function(e) {
                if(e.which == 13) {
                    window.location.replace(UpdateQueryString('page', $(this).val()), url)
                }
            })
        })
        function UpdateQueryString(key, value, url) {
            if (!url) url = window.location.href;
            var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                hash;

            if (re.test(url)) {
                if (typeof value !== 'undefined' && value !== null) {
                    return url.replace(re, '$1' + key + "=" + value + '$2$3');
                }
                else {
                    hash = url.split('#');
                    url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                        url += '#' + hash[1];
                    }
                    return url;
                }
            }
            else {
                if (typeof value !== 'undefined' && value !== null) {
                    var separator = url.indexOf('?') !== -1 ? '&' : '?';
                    hash = url.split('#');
                    url = hash[0] + separator + key + '=' + value;
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                        url += '#' + hash[1];
                    }
                    return url;
                }
                else {
                    return url;
                }
            }
        }
    </script>
@endpush

@push('style')
    <style>
        .dataTable_wrapper--content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(49%, 1fr));
            grid-gap: 10px;
        }
    </style>
@endpush
