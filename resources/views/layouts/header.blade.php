<div id="" class="kt-header kt-grid__item  kt-header--fixed {{ isset($noHeader) ? 'kt-header__no-header' : '' }}">
    <!-- begin:: Header Menu -->
    <div class="kt-header__topbar">
        <!--begin: User Bar -->
        <div class="kt-header__topbar-item kt-header__topbar-item--user">
            <div class="kt-header__topbar-wrapper">
                <div class="kt-header__topbar-user px-0">
                    @yield('header-topbar-right')
                </div>
            </div>
        </div>
    </div>

    <!-- end:: Header Menu -->

    <!-- begin:: Header Topbar -->
    <div class="kt-header__topbar pr-0">
        <!--begin: User Bar -->
        <div class="kt-header__topbar-item kt-header__topbar-item--user">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                <div class="kt-header__topbar-user px-0">
                    <img alt="Avatar.svg" src="{{ asset('/media/Avatar.svg') }}">
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                <div class="kt-notification">
                    <div class="kt-notification__custom justify-content-end">
                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-label btn-label-brand btn-sm btn-bold w-100" style="height: 40px;"><i class="fa fa-sign-out-alt" aria-hidden="true"></i> {{__('message.auth.sign_out')}} </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-nav custom-nav__admin">
            <label class="nav-mobile__btn" id="btnShowAdminMobileNav">
                <svg width="28" height="16" viewBox="0 0 28 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.0013 0C1.26492 0 0.667969 0.596954 0.667969 1.33333C0.667969 2.06971 1.26492 2.66667 2.0013 2.66667H26.0013C26.7377 2.66667 27.3346 2.06971 27.3346 1.33333C27.3346 0.596954 26.7377 0 26.0013 0H2.0013ZM0.667969 8C0.667969 7.26362 1.26492 6.66667 2.0013 6.66667H26.0013C26.7377 6.66667 27.3346 7.26362 27.3346 8C27.3346 8.73638 26.7377 9.33333 26.0013 9.33333H2.0013C1.26492 9.33333 0.667969 8.73638 0.667969 8ZM0.667969 14.6667C0.667969 13.9303 1.26492 13.3333 2.0013 13.3333H26.0013C26.7377 13.3333 27.3346 13.9303 27.3346 14.6667C27.3346 15.403 26.7377 16 26.0013 16H2.0013C1.26492 16 0.667969 15.403 0.667969 14.6667Z" fill="#757575"/>
                </svg>
            </label>
        </div>
    </div>
        <!--end: User Bar -->

    <!-- end:: Header Topbar -->
</div>