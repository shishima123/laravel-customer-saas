<div class="modal fade" id="invoiceDetailModal" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 p-4">
                <h5 class="modal-title fs-24" id="invoiceDetailTitle">{{ __('message.user.invoice_detail') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3 px-4">
                <div class="d-flex justify-content-between mb-2 flex-wrap">
                    <div>
                        <p class="mb-2 payment-history-label">{{ __('message.user.invoice_id') }}</p>
                        <p class="text-primary">{{ $invoice->id }}</p>
                    </div>
                    <a href="{{ route('customers.invoice.download', ['customer' => $customer, 'invoice' => $invoice->id]) }}"
                       class="btn btn-secondary py-0 btn-upper fs-10 text-primary"
                       style="height: 40px; line-height: 36px;">
                        <span>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 21H18" stroke="#2B65EB" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M12 3L12 17" stroke="#2B65EB" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M17 12L12 17L7 12" stroke="#2B65EB" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('message.download') }}
                    </a>
                </div>

                <div class="row mb-4">
                    <div class="col-12 col-lg-6 mb-3">
                        <p class="mb-1">{{ __('message.from') }}</p>
                        <p class="mb-1">{{ config('services.company.name') }}</p>
                        <p class="mb-1 text-gray">{{ config('services.company.add1') }}</p>
                        <p class="mb-1 text-gray">{{ config('services.company.add2') }}</p>
                        <p class="mb-1 text-gray">{{ config('services.company.phone') }}</p>
                    </div>

                    <div class="col-12 col-lg-6">
                        <p class="mb-1">{{ __('message.user.bill_to') }}</p>
                        <p class="mb-1">{{ $customer->name }}</p>
                        <p class="mb-1 text-gray">{{ $customer->company->name ?? ''}}</p>
                        <p class="mb-1 text-gray">{{ $customer->company->address->add1 ?? '' }}</p>
                        <p class="mb-1 text-gray">{{ $customer->phone_number }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between py-3 px-4">
                <div class="w-100 modal-payment-history__footer">
                    <div class="">
                        <p class="mb-2 payment-history-label">{{ __('message.user.detail') }}</p>
                        <p class="mb-0">{{ __('message.user.premium_plan_1_month') }}</p>
                    </div>
                    <div class="">
                        <p class="mb-2 payment-history-label">{{ __('message.user.total_amount') }}</p>
                        <p class="mb-0">{{ $invoice->total() }}</p>
                    </div>
                    <div class="">
                        <p class="mb-2 payment-history-label">{{ __('message.user.date') }}</p>
                        <p class="mb-0">{{ $invoice->date()->toFormattedDateString() }}</p>
                    </div>
                    <div class="">
                        <p class="mb-2 payment-history-label">{{ __('message.status') }}</p>
                        <p class="mb-0">
                            @if ($invoice->status == 'paid')
                                <span class="kt-badge kt-badge--success kt-badge--inline text-uppercase fs-10">{{ trans('message.user.succeeded') }}</span>
                            @else
                                <span class="kt-badge kt-badge--danger kt-badge--inline text-uppercase fs-10">{{ trans('message.user.payment_failed') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
