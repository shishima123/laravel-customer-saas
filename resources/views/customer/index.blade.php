@extends('layouts.layout')

@section('title', __('message.user.users'))

@section('header-topbar-right')
    @include('layouts.input-search')
@endsection

@section('subheader')
    <div class="kt-subheader kt-grid__item {{ isset($noHeader) ? 'd-none' : '' }}" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title fs-36 fs-mobile-24 fw-500 text-capitalize">
                {{ __('message.user.users') }}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <button type="button" id="add-user" class="btn btn-brand kt-login__btn-primary text-uppercase d-flex justify-content-center" style="width: 160px; word-break: keep-all;"><i class="flaticon2-plus" aria-hidden="true"></i>{{ __('message.user.add_user') }}</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="nt-portlet__body rounded">
        <table id="user-table" class="table table-striped table-condensed table-hover" data-url="{{ route('customers.index') }}" aria-hidden="true">
            <caption>{{ __('message.user.users') }}</caption>
        </table>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/user/index.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
    <script src="{{ asset('js/user/table.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
    <script src="{{ asset('js/user/user-common.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
@endpush
