function initMarkRemove(tableId = null) {
    $(document).on('change', 'input[name="status"]', function () {
        let $that = $(this)
        let state = !$(this).prop("checked")
        Common.prototype.confirmActionAlert(function () {
            let url = `/customers/update-status/${$that.attr('data-user-id')}`
            let data = {
                status: Number(state)
            }
            $.ajax({
                method: 'POST',
                url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data,
                success: function (res) {
                    if (tableId) {
                        Common.prototype.showNotify(res.success, 'success', null, 'top-end', 2500, function() {
                            $(tableId).DataTable().ajax.reload(null, false)
                        });
                    } else {
                        Common.prototype.flashNotifyAfterReload(res.success)
                        window.location.reload()
                    }
                },
                error: function (errors) {
                    Common.prototype.showNotify(errors.responseJSON.error, 'error');
                    $that.prop("checked", !state)
                }
            })
        }, function () {
            $that.prop("checked", !state)
        }, null, '')
    })
}

function initCancelSubscription(reload = false) {
    $(document).on('click', '.js-btn-show-cancel-subscription', function (e) {
        e.preventDefault()
        let html = `
        <div class="modal fade" id="cancelModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form action="/payments/${$(this).attr('data-customer-id')}/cancel" method="post">
                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0 px-4 pt-4 pb-0">
                            <h5 class="modal-title fs-24" id="exampleModalCenterTitle"> ${trans('message.payment.cancel_subscription')} </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body py-3 px-4 text-center">
                            <p class="mb-0">${trans('message.payment.modal_cancel_subscription_text_1')}</p>
                            <p class="">${trans('message.payment.modal_cancel_subscription_text_2')}</p>
                            <p class="p-3 bg-primary-5 text-primary mb-0">${trans('message.payment.modal_cancel_subscription_text_3', {'date': $(this).attr('data-next-cycle-date')})}</p>
                        </div>
                        <div class="modal-footer border-top-0 justify-content-between p-4">
                            <button type="button" class="btn btn-wide text-uppercase" data-dismiss="modal" style="width: 160px;">${trans('message.cancel')}</button>
                            <button type="submit" data-customer-id="${$(this).attr('data-customer-id')}" class="btn btn-brand btn-wide text-uppercase js-btn-cancel-subscription" style="width: 160px;">${trans('message.confirm')}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>`
        let model = $(html)
        model.appendTo('body').modal();
        model.on('shown.bs.modal', function () {
            model.removeAttr('tabindex', "");
        });

        model.on("hidden.bs.modal", function () {
            model.remove()
        });
    })

    $(document).on('click', '.js-btn-cancel-subscription', function (e) {
        e.preventDefault()
        let $form = $(this).closest('form')
        if (reload) {
            $form.submit()
        }

        let url = $form.attr('action')
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            url: url,
            method: "POST",
            type: "POST",
            contentType: false,
            processData: false,
            success: function (_data) {
                Common.prototype.showNotify(trans('message.notify.success.cancel_subscription'), 'success', null, 'top-end', 2500, function() {
                    $('#user-table').DataTable().ajax.reload(null, false)
                });
            },
            error: function (error) {
                Common.prototype.showNotify(trans('message.notify.error.errors'), 'error')
                console.log(error);
            },
            complete: function() {
                $('#cancelModel').modal('hide')
            }
        })
    })
}
