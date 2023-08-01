"use strict"

$(function () {
    let common = new Common()
    common.setSelect2()
    common.showMoreButton()
    initMarkRemove()
    initCancelSubscription(true)

    let $navbarWrapper = $('.user-detail-navbar-wrapper')
    common.addBorderToNavMobile($navbarWrapper)

    $('#btnCancel').on('click', function () {
        window.location.reload()
    })

    $('#btnSubmit').on('click', function(e) {
        e.preventDefault()
        let $that = $(this);
        common.confirmActionAlert(
            function() {
                $that.closest('form').submit()
            }
        )
    })

    $('#btnResume').on('click', function(e) {
        e.preventDefault()
        let $that = $(this);

        swal.fire({
            title: trans('message.payment.resume_subscriptions'),
            showCancelButton: true,
            confirmButtonText: trans('message.notify.agree').toUpperCase(),
            cancelButtonText: trans('message.notify.disagree').toUpperCase(),
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-brand btn-wide button-min-width',
                cancelButton: 'btn btn-secondary btn-wide button-min-width',
                title: 'fs-24',
                actions: 'justify-content-between'
            },
            buttonsStyling: false,
            html:
                `<div class="kt-radio-list mt-4">
                    <label class="kt-radio kt-radio--bold kt-radio--brand text-dark fw-500 mb-4">
                        <input type="radio" name="resume" value="current_method" checked> ${trans('message.payment.resume_with_current_payment')}
                        <span></span>
                    </label>
                    <label class="kt-radio kt-radio--bold kt-radio--brand text-dark fw-500">
                        <input type="radio" name="resume" value="new_method"> ${trans('message.payment.resume_with_new_payment')}
                        <span></span>
                    </label>
                </div>`,
            preConfirm: function() {
                return Promise.resolve({
                    option: $('input[name="resume"]:checked').val(),
                })
            }
        }).then(function (result) {
            if (result.value) {
                if (result.value.option === 'current_method') {
                    let action = $that.attr('href')
                    resumeWithCurrentMethod(action)
                } else {
                    let url = '/payments/resume'
                    window.location.replace(url)
                }
            }
        });
    })
});

function resumeWithCurrentMethod(action) {
    let form = document.createElement("form")
    form.action = action
    form.method = "POST"
    let _token = document.createElement("input")
    _token.name = "_token"
    _token.value = $('meta[name="csrf-token"]').attr('content')
    form.appendChild(_token);
    document.body.appendChild(form);
    form.submit();
}
