<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<!-- begin::Head -->
	<head>

		<!--begin::Base Path (base relative path for assets of this page) -->
		<base href="../">

		<!--end::Base Path -->
		<meta charset="utf-8" />
		<title> @yield('title') | {{ config('app.name', 'Enable Startup') }}</title>
		<meta name="description" content="{{ config('app.description') }}">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
		<!--begin::Fonts -->
		<script src="{{ asset('js/plugins/webfont.js') }}"></script>
		<script>
			WebFont.load({
				google: {
					"families": ["Noto Sans JP:300,400,500,600,700", "Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
				},
				active: function() {
					sessionStorage.fonts = true;
				}
			});
		</script>

		<!--end::Fonts -->

		<!--begin::Global Theme Styles(used by all pages) -->
		<link href="{{ asset('css/plugins/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/plugins/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/plugins/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/plugins/kt-pricing.css?v='.config('services.resource_version')) }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/style.css?v='.config('services.resource_version')) }}" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles -->
		@stack('style')
	</head>
	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading kt-aside--minimize">
		<div class="kt-grid kt-grid--hor kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
				<!-- begin:: Aside -->
                @include('layouts.aside')
				<!-- end:: Aside -->
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper {{ isset($noHeader) ? 'pt-0' : '' }}">

					<!-- begin:: Header -->
					@include('layouts.header')
					<!-- end:: Header -->

					<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">

						<!-- begin:: Subheader -->
						@yield('search')
						@yield('subheader')

						<!-- begin:: Content -->
						<div class="kt-content {{ isset($noHeader) ? 'pt-0' : '' }} @yield('custom-class-content')">
							<!-- begin:: Content -->
							@yield('content')
							<!-- end:: Content -->
						</div>
					</div>

					<!-- begin:: Footer -->
{{--					@include('layouts.footer')--}}
					<!-- end:: Footer -->
				</div>
			</div>
		</div>
		<!-- end:: Page -->

		<!-- begin:: Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up" aria-hidden="true"></i>
		</div>
		<!-- End:: Scrolltop -->

        <!-- begin::Global Config(global config for global JS script) -->
		<div id="overlay">
			<div class="cv-spinner">
				<span class="spinner"></span>
			</div>
		</div>
		@yield('end-body')
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
		<!-- end::Global Config -->

		<!--begin::Global Theme Bundle(used by all pages) -->
		<script src="{{ asset('js/plugins/vendors.bundle.js') }}" type="text/javascript"></script>
		<script src="{{ asset('js/plugins/scripts.bundle.js') }}" type="text/javascript"></script>
		<script src="{{ asset('js/plugins/datatables.bundle.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('js/common.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
		<script src="{{ asset('js/main.js?v='.config('services.resource_version')) }}" type="text/javascript"></script>
		<script>
			window.translations = {!! $translations !!};
			window.locale = "{{ app()->getLocale() }}"
		</script>
		<!--begin::Page Scripts -->
		<script>
            // disable notify alert if history traversal
			if (document.addEventListener) {
					window.addEventListener('load', function (event) {
						var historyTraversal = event.persisted || (window.performance &&
							window.performance.navigation.type == 2)
						if (!historyTraversal){
							Common.prototype.Notify()
						}
					},
				false);
			}

			// set box shadow for subheader when scroll
			let $ktSubheader  = $('#kt_subheader')
			window.onscroll = function(ev)
			{
				let windowWidth = $(window).width();
				if (windowWidth <= 959 ) {
					return;
				}
				var B= document.body; //IE 'quirks'
				var D= document.documentElement; //IE with doctype
				D= (D.clientHeight)? D: B;

				if (D.scrollTop == 0)
				{
					$ktSubheader.removeClass('header-shadow')
				} else {
					if (!$ktSubheader.hasClass('header-shadow')) {
						$ktSubheader.addClass('header-shadow')
					}
				}
			};
			window.addEventListener('resize', function() {
				if (!$ktSubheader.hasClass('header-shadow')) {
					return;
				}
				let windowWidth = $(window).width();
				if (windowWidth <= 959 ) {
					$ktSubheader.removeClass('header-shadow')
				}
			});
		</script>
		@stack('script')
		<!--end::Page Scripts -->
	</body>
	<!-- end::Body -->
</html>
