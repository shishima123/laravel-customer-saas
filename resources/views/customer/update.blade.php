@extends('layouts.layout')

@section('title', __('message.user.users'))

@section('subheader')
    <div class="kt-subheader kt-grid__item {{ auth()->user()->isAdmin() ? 'kt-grid__item-custom-widths' : 'kt-grid__item-custom-width' }} " id="kt_subheader" >
        <div class="kt-subheader__main">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('customers.index') }}" class="kt-subheader__title fs-24 fw-500 text-truncate">
                    <img class="arrow-left" src="{{ asset('media/arrow-left.svg') }}" alt="">
                    {{ @$customer->name }}
                </a>
            @else
                <div class="kt-subheader__title fs-24 fw-500 text-truncate">
                    {{ @$customer->name }}
                </div>
            @endif
        </div>
        <div class="kt-subheader__toolbar">
            @if (auth()->user()->isAdmin())
                <div class="mark-remove-section">
                    <span class="fs-15 text-nowrap">{{ __('message.user.mark_removed') }}</span>
                    <span class="kt-switch kt-switch--sm mb-0 d-flex" style="margin-left: 18px;" data-toggle="kt-tooltip" data-placement="top" data-boundary="window" data-skin="brand" title="{{ __('message.user.tooltip_mark_remove') }}">
                    <label class="mb-0">
                        <input {{ $customer->user->status ? '' : 'checked' }} type="checkbox" name="status" value="0" data-user-id="{{ $customer->user->id }}">
                        <span></span>
                    </label>
                </span>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('custom-class-content', 'mobile-padding')

@section('content')
    <div class="nt-portlet__body rounded">
        <div class="user-detail">
            @include('customer._sidebar')
            @include("customer." . $subpage)
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/user/update.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
    <script src="{{ asset('js/user/user-common.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
@endpush
