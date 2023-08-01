"use strict"

function PaymentHistory() {
    PaymentHistory.prototype.common = new Common();
}

PaymentHistory.prototype.load = function (tableId) {
    let columns = [
        {
            title: `${trans('message.user.transaction_id')}`,
            name: 'id',
            data: 'id',
            className: 'align-top',
            render: function (data, _type, _full, _meta) {
                return `<div>
                            <div class="payment-history-label">${trans('message.user.transaction_id')}</div>
                            <div class="text-primary">${data}</div>
                        </div>`
            }
        },
        {
            title: `${trans('message.user.invoice_id')}`,
            name: 'invoice_id',
            data: 'invoice_id',
            className: 'align-top',
            render: function (data, _type, _full, _meta) {
                return `<div>
                            <div class="payment-history-label">${trans('message.user.invoice_id')}</div>
                            <div>${data}</div>
                        </div>`
            }
        },
        {
            title: `${trans('message.user.detail')}`,
            name: 'subscription',
            data: 'subscription',
            className: 'align-top',
            render: function (_data, _type, _full, _meta) {
                return `<div>
                            <div class="payment-history-label">${trans('message.user.detail')}</div>
                            <div>${trans('message.user.premium_plan_1_month')}</div>
                        </div>`
            }
        },
        {
            title: `${trans('message.user.total_amount')}`,
            name: 'amount_format',
            data: 'amount_format',
            className: 'align-top',
            render: function (data, _type, _full, _meta) {
                return `<div>
                            <div class="payment-history-label">${trans('message.user.total_amount')}</div>
                            <div>${data}</div>
                        </div>`
            }
        },
        {
            title: `${trans('message.user.date')}`,
            name: 'charge_date_format',
            data: 'charge_date_format',
            className: 'align-top',
            render: function (data, _type, _full, _meta) {
                return `<div>
                            <div class="payment-history-label">${trans('message.user.date')}</div>
                            <div>${data}</div>
                        </div>`
            }
        },
        {
            title: `${trans('message.status')}`,
            data: 'status',
            name: 'status',
            className: 'align-top',
            render: function (data, _type, _full, _meta) {
                return `<div>
                            <div class="payment-history-label">${trans('message.status')}</div>
                            <div>
                               ${renderStatusBadge(data)}
                            </div>
                        </div>`
            },
        },
        {
            title: `${trans('message.actions')}`,
            data: 'id',
            name: 'id',
            render: function (_data, _type, full, _meta) {
                return `<div class="d-flex align-items-center">
                            <a class="pr-3 border-right text-uppercase fs-10 js-btn-show-detail" href="/customers/${full.customer_id}/invoice/${full.invoice_id}/detail">${trans('message.user.view_invoice')}</a>
                            <a class="pl-3 text-uppercase fs-10" href="/customers/${full.customer_id}/invoice/${full.invoice_id}/download">${trans('message.download')}</a>
                        </div>`
            },
        }
    ];
    this.common.table(
        tableId,
        columns,
        {},
        callbackFunction,
        null,
        false,
    );

    function callbackFunction(setting) {
        if ((setting._iDisplayStart + $(`${tableId} tbody tr`).length) < setting._iDisplayLength) {
            $('.dataTables_paginate:not(.mobile)').hide();
            $('.dataTables_pager:not(.mobile)').toggleClass('d-none d-flex');
        }
    }

    function renderStatusBadge(data) {
        if (data == 'succeeded') {
            return `<span class="kt-badge kt-badge--success kt-badge--inline text-uppercase fs-10">${trans('message.user.succeeded')}</span>`
        }
        return `<span class="kt-badge kt-badge--danger kt-badge--inline text-uppercase fs-10">${trans('message.user.payment_failed')}</span>`
    }
}

let table = new PaymentHistory();
table.load('#payment-history-table');

$(document).on('click', '.js-btn-show-detail', function (e) {
    e.preventDefault()
    $("#overlay").fadeIn(300);
    let url = $(this).attr('href')
    Common.prototype.simpleAjax(url, 'GET', {},
        function (res) {
            let model = $(res.html)
            model.appendTo('body').modal();
            model.on('shown.bs.modal', function () {
                model.removeAttr('tabindex', "");
            });

            model.on("hidden.bs.modal", function () {
                model.remove()
            });
        },
        function (_error) {
            setTimeout(function() {
                Common.prototype.showNotify(trans('message.notify.error.errors'))
            }, 300)
        },
        function () {
            $("#overlay").fadeOut(300);
        }
    )
})
