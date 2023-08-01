@extends('layouts.clients.app')
@section('title', __('message.payment.resume'))
@section('content')
    <div class="kt-grid__item login-heading-section">
        <div class="m-auto container d-flex justify-content-center align-items-center pt-5">
            <div class="text-center d-flex flex-column justify-content-center align-items-center">
                <h1 class="fs-52 fw-500">{{ __('message.payment.resume') }}</h1>
                <p class="fw-400 fs-18 heading-section-content">{{ __('message.payment.checkout_content') }}</p>
                <span class="rectangle"></span>
            </div>
        </div>
    </div>
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper  pt-0 pt-md-5 mt-md-5">
        <div class="kt-login__container mt-0 w-100" style="max-width: 544px;">
            <div class="kt-login__signin container pb-5">
                <form id="payment-form" action="{{ route('payments.resume', [auth()->user()->userable]) }}"
                      method="POST">
                    @csrf
                    <div class="form-group d-flex justify-content-between align-items-center">
                        <h3 class="fw-500 fs-24 mb-0">{{ __('message.payment.payment_detail') }}</h3>
                        <img src="{{ asset('media/powered_by_stripe.svg') }}" alt="Stripe">
                    </div>
                    <div class="form-group mb-0">
                        <div id="overlay-element" style="display: block; height: 200px;">
                            <div class="cv-spinner">
                                <span class="spinner"></span>
                            </div>
                        </div>
                        <div id="payment-element"></div>
                    </div>
                    <div id="card-errors" class="text-danger mt-2 mb-4" role="alert"></div>
                    <div id="btnSection" class="d-none justify-content-between align-items-center">
                        <a class="fs-15-original" href="{{ route('payments.index') }}">{{ __('message.back') }}</a>
                        <button type="submit" class="btn btn-brand h-48px text-uppercase w-100" id="card-button"
                                style="max-width: 160px"
                                data-secret="{{ $intent->client_secret }}">{{ __('message.payment.resume') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        let locale = "{{app()->getLocale()}}";
        const clientSecret = "{{ $intent->client_secret }}";
        const stripe = Stripe('{{ config('cashier.key') }}', {locale: locale});
        const _returnUrl = "{{ route('payments.resume-with-new-card') }}";
        const _userRoute = "{{ route('customers.index') }}";
    </script>
    <script src="{{ asset('js/payment/payment.js?v='.config('services.resource_version')) }}"
            type="text/javascript"></script>
@endpush
