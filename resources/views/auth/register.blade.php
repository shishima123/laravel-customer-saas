@extends('layouts.clients.app', ['showHeader' => true, 'showFooter' => true])
@section('title', __('message.auth.sign_up'))

@section('content')
    @include('layouts.clients.heading', ['heading' => __('message.auth.sign_up')])
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-0">
        <div class="kt-login__container">
            <div class="kt-login__signin container pb-5 pt-4">
                <form class="kt-form" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-item">
                        <input id="name" type="text" class="form-control animated @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" animated required>
                        <label for="name" class="text-uppercase fs-10">{{__('message.user.user_name')}}</label>
                        @error('name')
                        <span class="invalid-feedback kt-font-regular" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-item">
                        <input id="email" type="text" class="form-control animated @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" animated required>
                        <label for="email" class="text-uppercase fs-10">{{__('message.auth.your_email')}}</label>
                        @error('email')
                        <span class="invalid-feedback kt-font-regular" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-item">
                        <input id="company_name" type="text"
                               class="form-control animated @error('company_name') is-invalid @enderror"
                               name="company_name" value="{{ old('company_name') }}" animated required>
                        <label for="company_name"
                               class="text-uppercase fs-10">{{__('message.user.company_name')}}</label>
                        @error('company_name')
                        <span class="invalid-feedback kt-font-regular" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-item">
                        <input id="password" type="password"
                               class="form-control animated @error('password') is-invalid @enderror" name="password"
                               animated required>
                        <label for="password" class="text-uppercase fs-10">{{__('message.auth.password')}}</label>
                        @error('password')
                        <span class="invalid-feedback kt-font-regular" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-item">
                        <input id="password-confirm" type="password"
                               class="form-control animated mt-2 @error('password_confirmation') is-invalid @enderror"
                               name="password_confirmation" animated required autocomplete="new-password">
                        <label for="password-confirm"
                               class="text-uppercase fs-10">{{__('message.auth.confirm_password')}}</label>
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! RecaptchaV3::field('register') !!}
                    @error('g-recaptcha-response')
                    <span class="invalid-feedback kt-font-regular d-block mb-4" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <div class="kt-login__actions">
                        <button type="submit"
                                class="btn btn-brand kt-login__btn-primary text-uppercase w-100">{{__('message.auth.sign_up')}}</button>
                    </div>
                </form>

                <div class="kt-login__account mt-3">
                    <span class="kt-login__account-msg">
                        {{ __('message.auth.already_member') }}
                    </span>
                    <a href="{{ route('login') }}" id="kt_login_signup"
                       class="kt-link kt-link--light kt-login__account-link text-capitalize text-underline">{{ __('message.auth.login') }}</a>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.clients.download')
@endsection

@section('recaptcha')
    {!! RecaptchaV3::initJs() !!}
@endsection
