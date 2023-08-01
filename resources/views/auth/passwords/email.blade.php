@extends('layouts.clients.app', ['showHeader' => true, 'showFooter' => true])
@section('title', __('message.auth.forgot_password'))

@section('content')
    @include('layouts.clients.heading', ['heading' => __('message.auth.forgot_password'), 'description' => __('message.auth.forgot_password_description')])
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-0">
        <div class="kt-login__container h-100 mt-5">
            <div class="kt-login__signin container pb-4 pt-3">
                <form class="kt-form" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-item">
                        <input id="email" type="text" class="form-control animated @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" animated required>
                        <label for="email" class="text-uppercase fs-10">{{__('message.auth.your_email')}}</label>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                    <div class="kt-login__actions" style="margin-top: 24px;">
                        <button type="submit"
                                class="btn btn-brand btn-elevate kt-login__btn-primary text-uppercase w-100">{{__('message.auth.reset_password')}}</button>
                        <a href="{{ route('login') }}" class="go-back text-underline">
                            {{__('message.auth.try_to_sign_in')}}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

