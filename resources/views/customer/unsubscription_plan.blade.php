@extends('layouts.layout')

@section('title', __('message.payment.unsubscribe'))
@php
    $plans = config('constant.plans');
@endphp

@section('custom-class-content', 'px-2')

@section('content')
    <div class="kt-grid__item login-heading-section">
        <div class="m-auto container d-flex justify-content-center align-items-center pt-5">
            <div class="text-center d-flex flex-column justify-content-center align-items-center">
                <h1 class="fs-52 fw-500">{{ __('message.payment.change_your_plan_header') }}</h1>
                <p class="fw-400 fs-18 heading-section-content">{{ __('message.payment.change_your_plan_content') }}</p>
                <span class="rectangle"></span>
            </div>
        </div>
    </div>
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper pt-3 pt-md-5">
        <div class="kt-login__container mt-0 w-100 m-auto" style="max-width: 832px;">
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
                                            <span>{{ __('message.payment.for_30_days') }}</span><br>
                                            <span class="d-none d-md-block">&nbsp;</span>
                                        </div>
                                        <div class="kt-pricing-4__btn">
                                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary text-uppercase kt-pricing__btn-outline disabled">
                                                {{ __('message.payment.not_available') }}
                                            </a>
                                        </div>

                                        <!--begin::Mobile Pricing Table-->
                                        <div class="kt-pricing-4__top-items-mobile">
                                            <div class="kt-pricing-4__top-btn">
                                                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary text-uppercase kt-pricing__btn-outline w-100 disabled">
                                                    {{ __('message.payment.not_available') }}
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
                                            <button type="button"
                                                    id="btnUnSubscribe"
                                                    class="btn kt-pricing__btn-outline text-uppercase"
                                                    data-toggle="modal"
                                                    data-target="#cancelModel">
                                                {{ __('message.payment.un_subscribe') }}
                                            </button>
                                        </div>

                                        <!--begin::Mobile Pricing Table-->
                                        <div class="kt-pricing-4__top-items-mobile">
                                            <div class="kt-pricing-4__top-btn">
                                                <button type="button"
                                                        id="btnUnSubscribe"
                                                        class="btn kt-pricing__btn-outline text-uppercase w-100"
                                                        data-toggle="modal"
                                                        data-target="#cancelModel">
                                                    {{ __('message.payment.un_subscribe') }}
                                                </button>
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

                <div class="text-center fs-15 fw-500">
                    <a  href="{{ route('customers.index') }}">{{ __('message.payment.go_to_profile') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('end-body')
    <div class="modal fade" id="cancelModel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 430px">
            <form action="{{ route('payments.cancel', ['customer' => auth()->user()->userable->id, 'redirect_to_profile' => true]) }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header border-bottom-0 px-4 pt-4 pb-0">
                        <h5 class="modal-title fs-24">{{ trans('message.payment.cancel_subscription_question') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-3 px-4">
                        <p class="fw-400">{{ trans('message.payment.modal_cancel_subscription_text_4') }}</p>
                        <p class="p-3 bg-primary-5 text-primary mb-0 fw-400 text-center">{{ trans('message.payment.modal_cancel_subscription_text_3', ['date' => auth()->user()->userable->next_cycle_date_format ?? '']) }}</p>
                    </div>
                    <div class="modal-footer border-top-0 justify-content-center p-4 flex-wrap">
                        <button type="button" class="btn btn-wide text-uppercase text-gray" data-dismiss="modal">{{ __('message.discard') }}</button>
                        <button type="submit"
                                class="btn btn-outline-secondary text-danger btn-wide text-uppercase js-btn-cancel-subscription button-min-width" >{{ __('message.payment.cancel_subscription') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
