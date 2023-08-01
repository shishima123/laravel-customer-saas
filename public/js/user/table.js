"use strict"

function UserTable() {
    UserTable.prototype.common = new Common();
}

UserTable.prototype.load = function (tableId, newButton = false) {
    let columns = [
        {
            title: `${trans('message.user.user_id')}`,
            name: 'user_number',
            data: 'user_number',
            render: function (data, _type, full, _meta) {
                return `<a class="text-dark text-nowrap" href="/customers/${full.id}">${data || ''}</a>`
            }
        },
        {
            title: `${trans('message.user.user_name')}`,
            name: 'name',
            data: 'name',
            render: function (data, _type, full, _meta) {
                if (data) {
                    return `<a class="text-dark text-nowrap" href="/customers/${full.id}">${Common.prototype.textTruncate(data, 30) || ''}</a>`
                }
                return ''
            }
        },
        {
            title: `${trans('message.user.email')}`,
            name: 'email',
            data: 'email',
            render: function (data, _type, full, _meta) {
                return `<a class="text-nowrap" href="/customers/${full.id}">${data}</a>`
            }
        },
        {
            title: `${trans('message.user.phone')}`,
            name: 'phone_number',
            data: 'phone_number',
            render: function (data, _type, _full, _meta) {
                return data || ''
            }
        },
        {
            title: `${trans('message.user.company_name')}`,
            name: 'company',
            data: 'company',
            render: function (data, _type, _full, _meta) {
                if (data && data.name) {
                    return Common.prototype.textTruncate(data.name, 30) || ''
                }
                return ''
            }
        },
        {
            title: `${trans('message.user.mark_removed')}`,
            data: 'user',
            name: 'user',
            className: 'text-center',
            render: function (data, _type, full, _meta) {
                return `<div class="d-flex align-items-center">
                            ${renderBtnCancelSubscription(full)}
                            <span class="kt-switch kt-switch--sm mb-0 d-flex" data-toggle="kt-tooltip" data-placement="top" data-boundary="window" data-skin="brand" title="${trans('message.user.tooltip_mark_remove')}">
                                <label class="mb-0">
                                    <input ${data.status ? '' : 'checked'} type="checkbox" name="status" data-user-id="${data.id}">
                                    <span></span>
                                </label>
                            </span>
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
        null,
        initCompleteCallback
    );

    function callbackFunction() {
        $('.tooltip-inner').closest('.tooltip').remove()
        Swal.close()
        KTApp.initTooltips()
    }

    function renderBtnCancelSubscription(full) {
        if (full.is_premium && !full.on_grace_period) {
            return `<div class="p-2 mr-3 btn-cancel-subscription js-btn-show-cancel-subscription"
                         data-customer-id="${full.id}"
                         data-next-cycle-date="${full.next_cycle_date_format}"
                         data-toggle="kt-tooltip"
                         data-placement="top"
                         title="${trans('message.user.tooltip_cancel_payment')}"
                         data-boundary="window"
                         data-skin="brand">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 13L12 17L12 21" stroke="#757575" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 20.0645C5.03656 18.5918 3 15.5337 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 15.5337 18.9634 18.5918 16 20.0645" stroke="#757575" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>`
        }
        return '<div class="mr-3" style="width: 39px"></div>'
    }

    function initCompleteCallback() {
        $('#generalSearch').on('keyup change', function () {
            clearTimeout($.data(this, 'timer'));
            let value = $(this).val()
            var wait = setTimeout(function () {
                $(tableId).DataTable().search(value).draw();
            }, 300);
            $(this).data('timer', wait);
        });
    }

    if (newButton) {
        $(newButton).on('click', function (e) {
            e.preventDefault();
            UserTable.prototype.modalPanel(tableId)
        })
    }
}

UserTable.prototype.modalPanel = async function (_tableId, _id = '') {
    let record = {};
    let url, title;
    url = '/customers';
    title = trans('message.user.add_new_user');

    this.common.modal({
        title,
        record,
        url,
        closeButton: false,
        btnSaveText: trans('message.create').toUpperCase(),
        highlightInput: true,
        items: [
            {
                xtype: 'text',
                label: trans('message.user.email'),
                name: 'email',
                required: true,
                parent_class: 'form-item'
            },
            {
                xtype: 'text',
                label: trans('message.user.name'),
                name: 'name',
                parent_class: 'form-item'
            },
            {
                xtype: 'text',
                label: trans('message.user.phone'),
                name: 'phone_number',
                parent_class: 'form-item'
            },
            {
                xtype: 'text',
                label: trans('message.user.company_name'),
                name: 'company_name',
                required: true,
                parent_class: 'form-item'
            },
        ],
        showCompleteAjax: false,
        onSuccess: function (result) {
            Common.prototype.flashNotifyAfterReload(result.message)
            window.location.replace(result.url)
        },
        error: function (e) {
            console.log(e)
        }
    });
}
