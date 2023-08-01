@extends('layouts.layout')

@section('title', __('message.user.users'))

@section('subheader')
    <div class="kt-subheader kt-grid__item kt-grid__item-custom-width" id="kt_subheader">
        <div class="kt-subheader__main">
            <a href="{{ route('customers.index') }}" class="kt-subheader__title fs-24 fw-500 text-truncate">
                <img class="arrow-left" src="{{ asset('media/arrow-left.svg') }}" alt="">
                {{ @$customer->name }}
            </a>
            <div class="kt-subheader__title fs-24 fw-500 text-capitalize d-flex align-items-center">
                Administrator
            </div>
        </div>
        <div class="kt-subheader__toolbar"></div>
    </div>
@endsection

@section('custom-class-content', 'mobile-padding')

@section('content')
    <div class="nt-portlet__body rounded">
        <div class="user-detail">
            @include('admin-setting._sidebar')
            @include("admin-setting." . $subpage)
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#btnSubmit').on('click', function(e) {
                e.preventDefault()
                let $that = $(this);
                Common.prototype.confirmActionAlert(
                    function() {
                        $that.closest('form').submit()
                    }
                )
            })
            let $navbarWrapper = $('.user-detail-navbar-wrapper')
            Common.prototype.addBorderToNavMobile($navbarWrapper)
        })
    </script>
@endpush
