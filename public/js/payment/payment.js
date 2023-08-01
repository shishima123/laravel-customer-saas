const appearance = {
    theme: 'stripe',
    variables: {
        colorPrimary: '#0570de',
        colorBackground: '#ffffff',
        colorText: '#30313d',
        colorDanger: '#df1b41',
        fontFamily: 'Noto Sans JP, system-ui, sans-serif',
        fontSmooth: 'always',
        borderRadius: '0',
    },
    rules: {
        '.Label': {
            textTransform: 'uppercase',
        }
    },
    labels: 'floating',
};

// Pass the appearance object to the Elements instance
const elements = stripe.elements({clientSecret, appearance});

const paymentElement = elements.create('payment',
    { terms: {
            card: 'never',
        }
    }
)
paymentElement.mount("#payment-element");

paymentElement.on('ready', function(_event) {
    $('#overlay-element').hide()
    setTimeout(function() {
        $('#btnSection').toggleClass('d-none d-flex')
    }, 300)
});

const form = document.getElementById('payment-form')

form.addEventListener('submit', async (e) => {
    e.preventDefault()
    showProcess()
    const { setupIntent, error } = await stripe.confirmSetup({
        elements,
        confirmParams: {
            return_url: _returnUrl,
        },
        // Uncomment below if you only want redirect for redirect-based payments
        redirect: "if_required",
    })

    if(error) {
        var errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message;
        showPaymentFail()
    } else {
        let token = document.createElement('input')
        token.setAttribute('type', 'hidden')
        token.setAttribute('name', 'token')
        token.setAttribute('value', setupIntent.payment_method)
        form.appendChild(token)

        let formData = new FormData(form)
        let url = $(form).attr('action')
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            url: url,
            method: "POST",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (_data) {
                showPaymentSuccess()
            },
            error: function (_error) {
                showPaymentFail()
            },
        })
    }
})

const SwalMixin = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-brand btn-wide text-uppercase',
        cancelButton: 'btn btn-wide',
        actions: 'd-flex px-4 pt-3',
        popup: 'max-width-400',
        content: 'text-dark fs-15 fw-500',
        title: 'fs-24 text-dark fw-500 mb-2',
    },
    buttonsStyling: false,
    allowOutsideClick: false

})

function showProcess() {
    SwalMixin.fire({
        title: trans('message.checkout'),
        text: trans('message.payment.payment_checkout_process'),
        padding: '25px 0 20px 0',
        imageUrl: '/media/payment-process.gif',
        imageWidth: 150,
        imageHeight: 150,
        imageAlt: 'payment success',
        showCancelButton: false,
        showConfirmButton: false,
        reverseButtons: true,
    })
}

function showPaymentSuccess() {
    SwalMixin.fire({
        title: trans('message.payment.thank_you'),
        text: trans('message.payment.payment_checkout_success'),
        padding: '30px 0 40px',
        imageUrl: '/media/payment-success.gif',
        imageWidth: 110,
        imageHeight: 110,
        imageAlt: 'payment success',
        showCancelButton: false,
        confirmButtonText: trans('message.payment.starting_using').toUpperCase(),
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-brand btn-wide text-uppercase m-auto',
            cancelButton: 'btn btn-wide',
            actions: 'd-flex px-4 pt-3',
            popup: 'max-width-400',
            content: 'text-dark fs-15 fw-500',
            title: 'fs-24 text-dark fw-500 mb-2',
        }
    }).then(function (result) {
        if (result.value) {
            window.location.replace(_userRoute)
        }
    });
}

function showPaymentFail() {
    SwalMixin.fire({
        title: trans('message.payment.payment_failed'),
        text: trans('message.payment.payment_checkout_fail'),
        padding: '30px 0 40px',
        imageUrl: '/media/payment-error.gif',
        imageWidth: 110,
        imageHeight: 110,
        imageAlt: 'payment success',
        showCancelButton: true,
        confirmButtonText: trans('message.try_again').toUpperCase(),
        cancelButtonText: trans('message.cancel').toUpperCase(),
        reverseButtons: true
    }).then(function (result) {
        if (result.value) {
            window.location.reload()
        }
    });
}