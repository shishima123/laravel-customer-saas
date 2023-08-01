@extends('layouts.clients.app', ['showHeader' => true, 'showFooter' => true])
@section('title', __('message.auth.reset_password'))

@section('content')
    @include('layouts.clients.heading', ['heading' => __('message.auth.reset_password')])
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-0">
        <div class="kt-login__container h-100">
            <div class="kt-login__signin container pb-4 pt-3">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-12">
                        <form class="kt-form" method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group " style="display: none">
                                <input id="password" type="text"
                                       class="form-control animated @error('email') is-invalid @enderror" name="email"
                                       value={{@$email}}>
                            </div>

                            <div class="form-item">
                                <input id="password" type="password"
                                       class="form-control animated mt-2 @error('password') is-invalid @enderror"
                                       name="password" animated required autocomplete="new-password">
                                <label for="email"
                                       class="text-uppercase fs-10">{{__('message.auth.new_password')}}</label>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>

                            <div class="form-item">
                                <input id="password-confirm" type="password" class="form-control animated mt-2"
                                       name="password_confirmation" animated required autocomplete="new-password">
                                <label for="email"
                                       class="text-uppercase fs-10">{{__('message.auth.new_password_confirm')}}</label>
                            </div>

                            <div class="kt-login__actions">
                                <button type="submit"
                                        class="btn btn-brand btn-elevate kt-login__btn-primary text-uppercase w-100">
                                    {{ __('message.auth.reset_password') }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
