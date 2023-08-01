@extends('layouts.clients.app')
@section('title', __('message.text.password.change_pass'))

@section('content')
    <div class="kt-grid__item login-heading-section flex-grow-1 mt-0">
        <div class="m-auto d-flex justify-content-center align-items-center h-100">
            <div class="text-center d-flex flex-column justify-content-center align-items-center">
                <h1 class="fs-52 fw-500">{{ __('message.auth.welcome') }}</h1>
                <p class="fw-400 fs-18 heading-section-content">{{ __('message.auth.create_new_password_first') }}</p>
                <span class="rectangle"></span>
            </div>
        </div>
    </div>
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-0 distance-shorter">
        <div class="kt-login__container h-100">
            <div class="kt-login__signin container pb-4 pt-3">
                <form class="kt-form" method="POST" action="{{ route('first-login-post') }}">
                    @csrf
                    <div class="form-item">
                        <input id="password" type="password"
                               class="form-control animated mt-2 @error('password') is-invalid @enderror"
                               name="password" animated required autocomplete="new-password">
                        <label for="password" class="text-uppercase fs-10">{{__('message.auth.new_password')}}</label>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
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

                    <div class="kt-login__actions">
                        <button type="submit"
                                class="btn btn-brand btn-elevate kt-login__btn-primary text-uppercase w-100">
                            {{ __('message.create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
