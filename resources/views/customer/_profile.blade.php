<div class="kt-portlet no-box-shadow">
    <div class="kt-portlet__head border-bottom-0">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {{ __('message.user.user_profile') }}
            </h3>
        </div>
    </div>

    <!--begin::Form-->
    <form class="kt-form" action="{{ route('customers.update', $customer) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="kt-portlet__body pt-0 pb-3">
            <div class="row">
                <div class="col-lg-12 col-xl-8">
                    <div class="form-group row">
                        <div class="col-6 col-lg-4">
                            <label class="text-uppercase text-gray fs-12 d-block fw-500">{{ __('message.user.user_id') }}</label>
                            <span class="fw-400">{{ $customer->user_number }}</span>
                        </div>
                        <div class="col-6 col-lg-8 user-type">
                            <label class="text-uppercase text-gray fs-12 d-block fw-500">{{ __('message.user.user_type') }}</label>
                            <div>
                                @if ($customer->isPremium() ?? false)
                                    @php
                                        $nextCycleDate = $customer->next_cycle_date_format;
                                    @endphp
                                    <div class="user-type-section">
                                        <span class="text-premium">{{ __('message.payment.premium_user') }}</span>
                                        <span class="px-1 plus next-cycle-desktop">-</span>
                                        <span class="next-cycle-desktop fw-400">{{ __('message.payment.to') }} {{ $nextCycleDate }}</span>
                                        @if($customer->onGracePeriod())
                                            @if (auth()->user()->isUser())
                                                <span class="px-1 plus">-</span> <a id="btnResume" href="{{ route('payments.resume', [$customer]) }}" class="text-primary">{{ __('message.payment.resume_subscriptions') }}</a>
                                            @endif
                                        @else
                                            @if (auth()->user()->isAdmin())
                                                <span class="px-1 plus">-</span> <a href="#" class="text-danger js-btn-show-cancel-subscription" data-customer-id="{{$customer->id}}" data-next-cycle-date="{{ $nextCycleDate }}"><u>{{ __('message.payment.cancel_subscription') }}</u></a>
                                            @else
                                                <span class="px-1 plus">-</span> <a href="{{ route('customers.unsubscription-plan-get') }}" class="text-danger"><u>{{ __('message.payment.unsubscribe') }}</u></a>
                                                @if ($customer->hasIncompletePayment())
                                                    <div class="mt-1">
                                                        <p class="show-more-text fs-15 font-italic fw-400 mb-1 pr-1">{{ $customer->text_incomplete_payment }}</p>
                                                        <p class="btn-show-more" style="display:none">{{__('message.see_more')}}</p>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                    <span class="next-cycle-mobile fw-400">{{ __('message.payment.to') }} {{ $nextCycleDate }}</span>
                                @else
                                    <div class="user-type-section">
                                        <span>{{ __('message.payment.free_user') }}</span>
                                        @if (auth()->user()->isUser())
                                            <span class="px-1 plus">-</span> <a href="{{ route('payments.index') }}" class="text-primary">{{ __('message.payment.upgrade_account') }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="name" class="form-label">{{ __('message.user.user_name') }}</label>
                            <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') ?: $customer->name }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-5">
                        <div class="col-lg-6 mb-3">
                            <label for="email" class="form-label required">{{ __('message.user.email') }}</label>
                            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') ?: $customer->email }}" autocomplete="off">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="phone_number" class="form-label">{{ __('message.user.phone') }}</label>
                            <input id="phone_number" type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') ?: $customer->phone_number }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h3 class="kt-section__title title fw-500">{{ __('message.user.clinic_information') }}</h3>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="company_name" class="form-label required">{{ __('message.user.company_name') }}</label>
                            <input id="company_name" type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') ?: @$customer->company->name }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="add1" class="form-label required">{{ __('message.user.address') }}</label>
                            <input id="add1" type="text" name="add1" class="form-control @error('add1') is-invalid @enderror" value="{{ old('add1') ?: @$customer->company->address->add1 }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('add1')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 mb-3">
                            <label for="city_id" class="form-label">{{ __('message.user.city') }}</label>
                            <select id="city_id" class="form-control @error('city_id') is-invalid @enderror" name="city_id" select2="true" allow-clear="true"
                            select2-placeholder="{{ __('message.no_info')}}">
                                <option value=""></option>
                                @foreach($cities as $key => $city)
                                    <option value="{{ $city->id }}" {{ $city->id == (old('city_id') ?: @$customer->company->address->city_id) ? 'selected' : ''}}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="state" class="form-label">{{ __('message.user.sate_province') }}</label>
                            <input id="state" type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') ?: @$customer->company->address->state }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4">
                            <label for="zipcode" class="form-label">{{ __('message.user.zipcode') }}</label>
                            <input id="zipcode" type="text" name="zipcode" class="form-control @error('zipcode') is-invalid @enderror" value="{{ old('zipcode') ?: @$customer->company->address->zipcode }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('zipcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label for="billing_contact_email" class="form-label">{{ __('message.user.billing_contact_email') }}
                                <img data-toggle="kt-tooltip" data-placement="top" title="{{ __('message.user.tooltip_billing_contact') }}" data-skin="brand" src="/media/Circle_Warning.svg" alt="">
                            </label>
                            <input id="billing_contact_email" type="email" name="billing_contact_email" class="form-control text-primary @error('billing_contact_email') is-invalid @enderror" value="{{ old('billing_contact_email') ?: $customer->billing_contact_email }}" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                            @error('billing_contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="kt-portlet__foot border-top-0 py-0">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12 btn-profile-action">
                        <button type="reset" id="btnCancel" class="btn btn-secondary button-min-width text-uppercase h-48px text-dark">{{ __('message.cancel') }}</button>
                        <button id="btnSubmit" type="submit" class="btn btn-brand button-min-width text-uppercase h-48px">{{ __('message.save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
</div>
