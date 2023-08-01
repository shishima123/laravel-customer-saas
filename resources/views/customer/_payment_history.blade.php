<div class="kt-portlet no-box-shadow payment-table px-4">
    <div class="kt-portlet__head border-bottom-0 kt-portlet__head-history-padding">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {{ __('message.user.payment_history') }}
            </h3>
        </div>
    </div>

    <!--begin::Form-->
    <div class="pb-4 payment-history-desktop">
        <table id="payment-history-table" class="table" data-url="{{ route('customers.payment-history', [$customer]) }}" aria-hidden="true">
            <caption>{{ __('message.user.payment_history') }}</caption>
        </table>
    </div>
    <!--end::Form-->

    <div class="payment-history-mobile">
        @forelse($paymentHistories as $paymentHistory)
            <div class="kt-portlet border kt-portlet-payment flex-grow-0">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label flex-wrap py-3">
                        <h3 class="kt-portlet__head-title mr-3">
                            {{ $paymentHistory->id }}
                        </h3>
                        <div>
                            @if($paymentHistory->is_succeeded_status)
                                <span class="kt-badge kt-badge--success kt-badge--inline text-uppercase fs-10">{{ __('message.user.succeeded') }}</span>
                            @else
                                <span class="kt-badge kt-badge--danger kt-badge--inline text-uppercase fs-10">{{ __('message.user.payment_failed') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-actions">
                            <div class="dropdown dropdown-inline">
                                <a href="#" class="btn btn-sm btn-icon btn-icon-md" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="flaticon-more-1" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right p-0" style="width: 200px; min-width: 200px">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item " style="padding-top: 5px">
                                            <a href="{{ route('customers.invoice.detail', ['customer' => $paymentHistory->customer_id, 'invoice' => $paymentHistory->invoice_id]) }}" class="kt-nav__link js-btn-show-detail">
                                                <span class="kt-nav__link-text text-uppercase">{{ __('message.user.view_invoice') }}</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item" style="padding-top: 5px">
                                            <a href="{{ route('customers.invoice.download', ['customer' => $paymentHistory->customer_id, 'invoice' => $paymentHistory->invoice_id]) }}" class="kt-nav__link">
                                                <span class="kt-nav__link-text text-uppercase">{{ __('message.download') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="px-0">
                        <div class="row">
                            <div class="col-12 mb-2 fw-400 fs-15-original">
                                {{ __('message.user.premium_plan_1_month') }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <p class="mb-0 fw-500 fs-18-original">{{ $paymentHistory->amount_format }}</p>
                                <p class="mb-0 text-gray fw-400 fs-12">{{ $paymentHistory->charge_date_format }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray text-center py-5">{{ __('message.no_data_available_in_table') }}</p>
        @endforelse
        {{ $paymentHistories->onEachSide(0)->links('layouts.pagination_custom', ['showGoto' => true]) }}
    </div>
</div>
@push('script')
    <script src="{{ asset('js/user/payment-history-table.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
@endpush
