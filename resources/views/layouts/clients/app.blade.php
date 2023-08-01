<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- begin::Head -->
<head>

    <!--begin::Base Path (base relative path for assets of this page) -->
    <base href="../">

    <!--end::Base Path -->
    <meta charset="utf-8"/>
    <title> @yield('title') | {{ config('app.name', 'Enable Startup') }}</title>
    <meta name="description" content="{{ config('app.description') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"/>
    <!--begin::Fonts -->
    <script src="{{ asset('js/plugins/webfont.js') }}"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Noto Sans JP:300,400,500,600,700", "Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
            },
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>

    @yield('recaptcha')
    <!--end::Fonts -->

    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="{{ asset('css/plugins/vendors.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/plugins/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/plugins/kt-pricing.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/style.css?v='.config('services.resource_version')) }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/plugins/login.css?v='.config('services.resource_version')) }}" rel="stylesheet"
          type="text/css"/>
    <!--end::Global Theme Styles -->
    @stack('style')
</head>
<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

<!-- begin:: Page -->
<div class="kt-grid kt-grid--ver kt-grid--root">
    <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v3 kt-login--signin" id="kt_login">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
            @isset($showHeader)
                @include('layouts.clients.header')
            @endunless
            @yield('content')
            @isset($showFooter)
                @include('layouts.clients.footer')
            @endisset
        </div>
    </div>
</div>

<script>
    var _successMessage = "{{ session()->has('success') ? session('success') : "" }}",
        _successContentMessage = `{!!  session()->has('success_content') ? session('success_content') : "" !!}`,
        _errorMessage = "{{ session()->has('error') ? session('error') : "" }}",
        _errorContentMessage = `{!! session()->has('error_content') ? session('error_content') : "" !!}`,
        _warningMessage = "{{ session()->has('warning') ? session('warning') : "" }}",
        _warningContentMessage = `{!! session()->has('warning_content') ? session('warning_content') : "" !!}`;
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#22b9ff",
                "light": "#ffffff",
                "dark": "#282a3c",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>

<script src="{{ asset('js/plugins/vendors.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/plugins/scripts.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/common.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
<script src="{{ asset('js/main.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
<script>
    window.translations = {!! $translations !!};
    window.locale = "{{ app()->getLocale() }}"
</script>
<script>
    // disable notify alert if history traversal
    if (document.addEventListener) {
        window.addEventListener('load', function (event) {
                var historyTraversal = event.persisted || (window.performance &&
                    window.performance.navigation.type == 2)
                if (!historyTraversal) {
                    Common.prototype.Notify()
                }
            },
            false);
    }
</script>
@stack('script')
</body>
</html>
