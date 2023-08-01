@extends('layouts.clients.app')
@section('title', __('message.user.setup_information_header'))
@php
    $user = auth()->user();
@endphp
@section('content')
    <div class="kt-grid__item login-heading-section">
        <div class="m-auto container d-flex justify-content-center align-items-center title-no-header">
            <div class="text-center d-flex flex-column justify-content-center align-items-center">
                <h1 class="fs-52 fw-500">{{ __('message.user.setup_information_header') }}</h1>
                <p class="fw-400 fs-18 heading-section-content">{{ __('message.user.setup_information_content') }}</p>
                @include('layouts.clients.step-bar', ['length' => 3, 'activeStep' => 1])
            </div>
        </div>
    </div>
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-5">
        <div class="kt-login__container mt-0 w-100" style="max-width: 736px;">
            <div class="kt-login__signin container pb-5 kt-login__signin-pt pt-0">
                <form class="kt-form" method="POST" action="{{ route('customers.setup-information') }}">
                    @method('PUT')
                    @csrf
                    <h3 class="fw-500 fs-24 mb-4">{{ __('message.user.your_info') }}</h3>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-item">
                                <input id="name" type="text"
                                       class="form-control animated @error('name') is-invalid @enderror" name="name"
                                       value="{{ old('name') ?: @$user->userable->name }}" animated autocomplete="off">
                                <label for="name" class="text-uppercase fs-10">{{__('message.user.name')}}</label>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-item">
                                <input id="email" type="email" name="email"
                                       class="form-control animated @error('email') is-invalid @enderror"
                                       value="{{ old('email') ?: @$user->email }}" animated autocomplete="off">
                                <label for="email"
                                       class="text-uppercase fs-10 required">{{ __('message.user.email') }}</label>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-item">
                                <input id="phone_number" type="text" name="phone_number"
                                       class="form-control animated @error('phone_number') is-invalid @enderror"
                                       value="{{ old('phone_number') ?: @$user->userable->phone_number }}" animated
                                       autocomplete="off">
                                <label for="phone_number"
                                       class="text-uppercase fs-10">{{__('message.user.phone')}}</label>
                                @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h3 class="fw-500 fs-24 mb-4">{{ __('message.user.your_clinic_info') }}</h3>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-item">
                                <input id="company_name" type="text" name="company_name"
                                       class="form-control animated @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name') ?: @$user->userable->company->name }}" animated
                                       autocomplete="off">
                                <label for="company_name"
                                       class="text-uppercase fs-10 required">{{ __('message.user.company_name') }}</label>
                                @error('company_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-item">
                                <input id="add1" type="text" name="add1"
                                       class="form-control animated @error('add1') is-invalid @enderror"
                                       value="{{ old('add1') ?: @$user->userable->company->address->add1 }}" animated
                                       autocomplete="off">
                                <label for="add1"
                                       class="text-uppercase fs-10 required">{{ __('message.user.address') }}</label>
                                @error('add1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-item">
                                <select id="city_id"
                                        class="form-control animated @error('city_id') is-invalid @enderror"
                                        name="city_id" select2="true" allow-clear="true" animated>
                                    <option value=""></option>
                                    @foreach($cities as $key => $city)
                                        <option value="{{ $city->id }}" {{ $city->id == (old('city_id') ?: @$user->userable->company->address->city_id) ? 'selected' : ''}}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                <label for="city_id"
                                       class="text-uppercase fs-10">{{ strtoupper(__('message.user.city')) }}</label>
                                @error('city_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-item">
                                <input id="state" type="text" name="state"
                                       class="form-control animated @error('state') is-invalid @enderror"
                                       value="{{ old('state') ?: @$user->userable->company->address->state }}" animated
                                       autocomplete="off">
                                <label for="state"
                                       class="text-uppercase fs-10">{{__('message.user.sate_province')}}</label>
                                @error('state')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-item">
                                <input id="zipcode" type="text" name="zipcode"
                                       class="form-control animated @error('zipcode') is-invalid @enderror"
                                       value="{{ old('zipcode') ?: @$user->userable->company->address->zipcode }}"
                                       animated autocomplete="off">
                                <label for="zipcode" class="text-uppercase fs-10">{{__('message.user.zipcode')}}</label>
                                @error('zipcode')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="kt-login__actions text-right">
                        <button type="submit"
                                class="btn btn-brand btn-elevate kt-login__btn-primary text-uppercase h-48px"
                                style="max-width: 160px">
                            {{ __('message.next') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function () {
            Common.prototype.setSelect2()
        })
    </script>
@endpush
