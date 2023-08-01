<div class="user-detail-navbar-wrapper" style="min-width: 256px;">
    <nav class="user-detail-navbar user-detail-navbar-default" role="navigation">
        <div class="side-menu-container">
            <ul class="user-detail-navbar-nav">
                <li class="{{ active_menu(['systems.admin-account-get'], 'active') }}">
                    <a href="{{ route('systems.admin-account-get', [$user]) }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z"
                                  stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.2165 19.3323C15.9348 17.9008 14.0725 17 11.9998 17C9.92718 17 8.06492 17.9008 6.7832 19.3323"
                                  stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 14C13.6569 14 15 12.6569 15 11C15 9.34315 13.6569 8 12 8C10.3431 8 9 9.34315 9 11C9 12.6569 10.3431 14 12 14Z"
                                  stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ __('message.user.account') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>