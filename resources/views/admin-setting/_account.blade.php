<div class="kt-portlet no-box-shadow">
    <div class="kt-portlet__head border-bottom-0">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {{ __('message.user.account') }}
            </h3>
        </div>
    </div>

    <!--begin::Form-->
    <form class="kt-form" action="{{ route('systems.admin-change-password-post', $user) }}" method="POST">
        @csrf
        <div class="kt-portlet__body pt-0 pb-3">
            <div class="form-group row mb-2">
                <div class="col-lg-12">
                    <label class="text-uppercase text-gray fs-12 d-block fw-500">{{ __('message.user.email') }}</label>
                    <span>{{ $user->email }}</span>
                    <div class="kt-separator kt-separator--space-lg kt-separator--portlet-fit mt-4 mb-1"></div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-xl-4 col-lg-6">
                    <label for="password" class="form-label">{{ __('message.auth.new_password') }}</label>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xl-4 col-lg-6">
                    <label for="password_confirmation" class="form-label">{{ __('message.auth.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="{{ __('message.user.no_information') }}" autocomplete="off">
                    @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot border-top-0 py-0">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12">
                        <button id="btnSubmit" type="submit" class="btn btn-brand button-min-width text-uppercase h-48px">{{ __('message.text.password.change_pass') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
</div>
