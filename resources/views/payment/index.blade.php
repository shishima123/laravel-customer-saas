@extends('layouts.clients.app')
@section('title', __('message.payment.choose_your_plan_header'))
@php
    $user = auth()->user();
    $plans = config('constant.plans');
@endphp
@section('content')
    <div class="kt-grid__item login-heading-section">
        <div class="m-auto container d-flex justify-content-center align-items-center title-no-header">
            <div class="text-center d-flex flex-column justify-content-center align-items-center">
                <h1 class="fs-52 fw-500">{{ __('message.payment.choose_your_plan_header') }}</h1>
                <p class="fw-400 fs-18 heading-section-content">{{ __('message.payment.choose_your_plan_content') }}</p>
                @include('layouts.clients.step-bar', ['length' => 3, 'activeStep' => 2])
            </div>
        </div>
    </div>
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-3 pt-md-5">
        <div class="kt-login__container mt-0 w-100 container-pricing kt-item-mobile">
            <div class="kt-login__signin container pb-5">
                <div class="kt-pricing-4">
                    <div class="kt-pricing-4__top">
                        <div class="kt-pricing-4__top-container kt-pricing-4__top-container--fixed">
                            <div class="kt-pricing-4__top-body">

                                <!--begin::Pricing Items-->
                                <div class="kt-pricing-4__top-items">

                                    <!--begin::Pricing Items-->
                                    <div class="kt-pricing-4__top-item"></div>
                                    <div class="kt-pricing-4__top-item free">
                                        <h2 class="kt-pricing-4__subtitle">{{ __('message.payment.free') }}</h2>
                                        <span class="kt-pricing-4__price">{{ __('message.payment.free_price') }}</span>
                                        <div class="kt-pricing-4__features custom-height">
                                            <span></span><br>
                                            <span class="d-none d-md-block">&nbsp;</span>
                                        </div>
                                        <div class="kt-pricing-4__btn">
                                            <a href="{{ route('customers.index') }}"
                                               class="btn btn-outline-secondary text-uppercase kt-pricing__btn-outline">
                                                {{ __('message.payment.try_for_free') }}
                                            </a>
                                        </div>

                                        <!--begin::Mobile Pricing Table-->
                                        <div class="kt-pricing-4__top-items-mobile">
                                            <div class="kt-pricing-4__top-btn">
                                                <a href="{{ route('customers.index') }}"
                                                   class="btn btn-outline-secondary text-uppercase kt-pricing__btn-outline w-100">
                                                    {{ __('message.payment.try_for_free') }}
                                                </a>
                                            </div>
                                            @foreach($plans as $plan)
                                                <div class="kt-pricing-4__top-item-mobile">
                                                    <span>
                                                        @if($plan['free'])
                                                            <img src="{{ asset("media/Check.svg") }}" alt="check">
                                                        @else
                                                            <img src="{{ asset("media/Close.svg") }}" alt="close">
                                                        @endif
                                                    </span>
                                                    <span class="{{ !$plan['free'] ? "color-grey" : ''}}">{{ isset($plan['txt_mobile']) ? __($plan['txt_mobile']) : __($plan['text']) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!--end::Mobile Pricing Table-->
                                    </div>
                                    <!--end::Pricing Items-->

                                    <!--begin::Pricing Items-->
                                    <div class="kt-pricing-4__top-item premium">
                                        <h2 class="kt-pricing-4__subtitle kt-font-primary">{{ __('message.payment.premium') }}</h2>
                                        <span class="kt-pricing-4__price">{{ __('message.payment.premium_price') }}</span>
                                        <span class="kt-pricing-4__label">/{{ __('message.payment.month') }}</span>
                                        <div class="kt-pricing-4__features custom-height">
                                            <span>{{ __('message.payment.renew_subscription') }}</span><br>
                                            <span>{{ __('message.payment.cancel_at_any_time') }}</span>
                                        </div>

                                        <div class="kt-pricing-4__btn">
                                            <form action="{{ route('payments.create') }}" method="GET">
                                                <input type="hidden" name="plan"
                                                       value="{{ \App\Enums\PlanType::PREMIUM->value }}">
                                                <button type="submit" class="btn btn-brand text-uppercase">
                                                    {{ __('message.payment.get_started') }}
                                                </button>
                                            </form>
                                        </div>

                                        <!--begin::Mobile Pricing Table-->
                                        <div class="kt-pricing-4__top-items-mobile">
                                            <div class="kt-pricing-4__top-btn">
                                                <form action="{{ route('payments.create') }}" method="GET">
                                                    <input type="hidden" name="plan"
                                                           value="{{ \App\Enums\PlanType::PREMIUM->value }}">
                                                    <button type="submit" class="btn btn-brand text-uppercase w-100">
                                                        {{ __('message.payment.get_started') }}
                                                    </button>
                                                </form>
                                            </div>
                                            @foreach($plans as $plan)
                                                <div class="kt-pricing-4__top-item-mobile">
                                                    <span>
                                                        @if($plan['premium'])
                                                            <img src="{{ asset("media/Check.svg") }}" alt="check">
                                                        @else
                                                            <img src="{{ asset("media/Close.svg") }}" alt="close">
                                                        @endif
                                                    </span>
                                                    <span>{{ __($plan['text']) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!--end::Mobile Pricing Table-->
                                    </div>
                                    <!--end::Pricing Items-->
                                </div>
                                <!--end::Pricing Items-->
                            </div>
                        </div>
                    </div>
                    <div class="kt-pricing-4__bottom">
                        <div class="kt-pricing-4__bottok-container kt-pricing-4__bottok-container--fixed">
                            @foreach($plans as $plan)
                                <div class="kt-pricing-4__bottom-items">
                                    <div class="kt-pricing-4__bottom-item">
                                        {{ __($plan['text']) }}
                                    </div>
                                    <div class="kt-pricing-4__bottom-item">
                                        @if($plan['free'])
                                            @if(isset($plan['note']))
                                                <div class="d-flex justify-content-center">
                                                    <img src="{{ asset("media/Check.svg") }}" alt="check">
                                                    <span class="ml-1 fs-15-original">{{__($plan['note'])}}</span>
                                                </div>
                                            @else
                                                <img src="{{ asset("media/Check.svg") }}" alt="check">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="kt-pricing-4__bottom-item premium {{ !next($plans) ? 'kt-pricing-4_border-bottom' : '' }}">
                                        @if($plan['premium'])
                                            <img src="{{ asset("media/Check.svg") }}" alt="check">
                                        @else
                                            <img src="{{ asset("media/Close.svg") }}" alt="close">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <p class="kt-pricing-4_text-vat">{{ __('message.payment.price_not_vat') }}</p>
            </div>
        </div>
    </div>
@endsection
