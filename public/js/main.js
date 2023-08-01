
"use strict"

$(function () {
    MainJs();
})

function MainJs() {
    MainJs.prototype.common = new Common();
    MainJs.prototype.logout();
    MainJs.prototype.handleHttpCodeError();
    MainJs.prototype.handleNavMobile();
}

MainJs.prototype.logout = function () {
    $('#log-out').on('click', function (e) {
        e.preventDefault();
        let form = $(this).parent('form')
        const logout = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-brand btn-wide',
                cancelButton: 'btn btn-secondary btn-wide'
            },
            buttonsStyling: false
        })

        logout.fire({
            title: trans('message.notify.are_sure'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: trans('message.yes'),
            cancelButtonText: trans('message.no'),
            reverseButtons: true
        }).then(function (result) {
            if (result.value) {
                form.submit();
            }
        });
    });
}

MainJs.prototype.handleHttpCodeError = function () {
    $(document).ajaxError(function( _event, jqxhr, _settings, _exception ) {
        if (jqxhr.status == 401) {
            window.location.reload();
        }
    })
}

MainJs.prototype.handleNavMobile = function () {
    $('.nav-mobile__btn').on('click', function() {
        $('.nav-mobile').addClass('show')
    })
    $('.nav-mobile-close__btn').on('click', function() {
        $('.nav-mobile').removeClass('show')
    })

    $(document).click((e) => {
        if (!$(e.target).closest('.nav-mobile').length && (!$(e.target).is('svg') && !$(e.target).is('path') && !$(e.target).is('.nav-mobile__btn'))) {
            $('.nav-mobile').removeClass('show')
        }
    });
}

$('#btnShowAdminMobileNav').click(function() {
    $('.kt-aside').addClass('kt-aside__mobile-show')
    disableScroll()
})

$('#btnCloseAdminMobileNav').click(function() {
    $('.kt-aside').removeClass('kt-aside__mobile-show')
    enableScroll()
})

// disable window scroll
// left: 37, up: 38, right: 39, down: 40,
// spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
var keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
    e.preventDefault();
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

// modern Chrome requires { passive: false } when adding event
var supportsPassive = false;
try {
    window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
        get: function () { supportsPassive = true; }
    }));
} catch(e) {}

var wheelOpt = supportsPassive ? { passive: false } : false;
var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

// call this to Disable
function disableScroll() {
    window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
    window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
    window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
    window.addEventListener('keydown', preventDefaultForScrollKeys, false);
}

// call this to Enable
function enableScroll() {
    window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
    window.removeEventListener('touchmove', preventDefault, wheelOpt);
    window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
}
