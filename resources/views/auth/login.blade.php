@extends('layouts.clients.app', ['showHeader' => true, 'showFooter' => true])
@section('title', __('message.auth.sign_in'))

@section('content')
    @include('layouts.clients.heading', ['heading' => __('message.auth.sign_in')])
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-0">
        <div class="kt-login__container">
            <div class="kt-login__signin container pb-4 pt-3">
                <form class="kt-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-item">
                        <input id="email" type="text" class="form-control animated @error('email') is-invalid @enderror"
                               name="email" value="{{ session()->get('emailParam') ?? old('email') }}" animated
                               required>
                        <label for="email" class="text-uppercase fs-10">{{__('message.auth.your_email')}}</label>
                    </div>

                    <div class="form-item">
                        <input id="password" type="password"
                               class="form-control animated @error('email') is-invalid @enderror" name="password"
                               animated required>
                        <label for="password" class="text-uppercase fs-10">{{__('message.auth.password')}}</label>
                        @error('email')
                        <span class="invalid-feedback kt-font-regular" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! RecaptchaV3::field('login') !!}
                    @error('g-recaptcha-response')
                    <span class="invalid-feedback kt-font-regular d-block mb-4" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="kt-login__extra mb-4 w-100 d-flex justify-content-between">
                        <div class=" pl-0">
                            <label class="kt-checkbox mb-0">
                                <input class="form-check-input" type="checkbox" name="remember"
                                       id="remember" {{ old('remember') ? 'checked' : '' }}> {{__('message.auth.remember_me')}}
                                <span></span>
                            </label>
                        </div>
                        <div class="kt-align-right align-self-center">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="kt-login__link text-underline">{{__('message.auth.forgot_password')}}</a>
                            @endif
                        </div>
                    </div>
                    <div class="kt-login__actions">
                        <button type="submit"
                                class="btn btn-brand kt-login__btn-primary text-uppercase w-100">{{__('message.auth.login')}}</button>
                    </div>
                </form>

                <div class="kt-login__account">
                <span class="kt-login__account-msg">
                    {{ __('message.auth.not_have_account') }}
                </span>
                    <a href="{{ route('register') }}" id="kt_login_signup"
                       class="kt-link kt-link--light kt-login__account-link text-underline">{{ __('message.auth.sign_up') }}</a>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.clients.download')

@endsection

@section('recaptcha')
    {!! RecaptchaV3::initJs() !!}
@endsection
