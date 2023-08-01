<div class="user-detail-navbar-wrapper" style="min-width: 256px;">
    <nav class="user-detail-navbar user-detail-navbar-default" role="navigation">
        <div class="side-menu-container">
            <ul class="user-detail-navbar-nav">
                <li class="{{ active_menu(['customers.show', 'customers.profile'], 'active') }}">
                    <a href="{{ auth()->user()->isAdmin() ? route('customers.show', $customer) : route('customers.profile')}}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 13C10.1046 13 11 12.1046 11 11C11 9.89543 10.1046 9 9 9C7.89543 9 7 9.89543 7 11C7 12.1046 7.89543 13 9 13Z"
                                  stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 6H4C3.44772 6 3 6.44772 3 7V17C3 17.5523 3.44772 18 4 18H20C20.5523 18 21 17.5523 21 17V7C21 6.44772 20.5523 6 20 6Z"
                                  stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 18C6 16.8954 7.34315 16 9 16C10.6569 16 12 16.8954 12 18" stroke="#B0B0B0"
                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 14L14 14" stroke="#B0B0B0" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M18 11L15 11" stroke="#B0B0B0" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                        <span>{{ __('message.user.user_profile') }}</span>
                    </a>
                </li>
                <li class="{{ active_menu(['customers.account'], 'active') }}">
                    <a href="{{ auth()->user()->isAdmin() ? route('customers.account', $customer) : route('customers.account', ['profile'])}}">
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
                <li class="{{ active_menu(['customers.payment-history'], 'active') }}">
                    <a href="{{  auth()->user()->isAdmin() ? route('customers.payment-history', $customer) : route('customers.payment-history', ['profile']) }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5H4C3.44772 5 3 5.44772 3 6V18C3 18.5523 3.44772 19 4 19H20C20.5523 19 21 18.5523 21 18V6C21 5.44772 20.5523 5 20 5Z" stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 15H11" stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 11H21" stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 9H21" stroke="#B0B0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ __('message.user.payment_history') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
