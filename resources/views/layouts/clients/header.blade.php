<!-- begin:: Header -->
<div class="padding-header">
<div class="kt-header no-box-shadow container px-0">
    <div class="custom-nav">
        <!-- Nav PC -->
        <nav class="nav__pc" aria-label="nav__pc">
            <div class="d-flex align-items-center h-100">
                <a href="#"><img src="{{ asset('/media/PerioDx-black.svg') }}" alt="PerioDx-black.svg" style="margin-right: 60px; vertical-align: baseline"></a>
                <nav class="nav header-nav" aria-label="header-nav">
                    <a class="nav-link" href="#">{{ __('message.header.pricing') }}</a>
                    <a class="nav-link" href="#">{{ __('message.header.news') }}</a>
                </nav>
            </div>
            <div class="d-flex align-items-center h-100">
               <div class="pr-4" >
                   <a class="w-100 link-dropdown"  href="#" role="button" id="dropdownLanguage" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M21 12C21 16.9706 16.9706 21 12 21M21 12C21 7.02944 16.9706 3 12 3M21 12H3M12 21C7.02944 21 3 16.9706 3 12M12 21C13.6569 21 15 16.9706 15 12C15 7.02944 13.6569 3 12 3M12 21C10.3431 21 9 16.9706 9 12C9 7.02944 10.3431 3 12 3M3 12C3 7.02944 7.02944 3 12 3" stroke="#757575" stroke-width="1.7" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                   </a>
                   <div class="dropdown-menu dropdown-menu-language" aria-labelledby="dropdownLanguage">
                    <a class="dropdown-item text-black d-flex justify-content-between align-items-center {{ app()->getLocale() == 'ja' ? 'active' : ''}}" href="{!! route('language', ['ja']) !!}">
                        <span class="pl-2 dropdown-aside-text">{{ __('message.ja') }}</span>
                        <svg style="display: none" width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1972 0.874937C12.5226 1.20037 12.5226 1.72801 12.1972 2.05345L5.12609 9.12452C4.80065 9.44995 4.27302 9.44995 3.94758 9.12452L0.412046 5.58898C0.0866095 5.26355 0.0866095 4.73591 0.412046 4.41047C0.737483 4.08503 1.26512 4.08503 1.59056 4.41047L4.53684 7.35675L11.0186 0.874937C11.3441 0.5495 11.8717 0.5495 12.1972 0.874937Z" fill="white"/>
                        </svg>
                    </a>
                    <a class="dropdown-item text-black d-flex justify-content-between align-items-center {{ app()->getLocale() == 'en' ? 'active' : ''}}" href="{!! route('language', ['en']) !!}">
                        <span class="pl-2 dropdown-aside-text">{{ __('message.en') }}</span>
                        <svg style="display: none" width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1972 0.874937C12.5226 1.20037 12.5226 1.72801 12.1972 2.05345L5.12609 9.12452C4.80065 9.44995 4.27302 9.44995 3.94758 9.12452L0.412046 5.58898C0.0866095 5.26355 0.0866095 4.73591 0.412046 4.41047C0.737483 4.08503 1.26512 4.08503 1.59056 4.41047L4.53684 7.35675L11.0186 0.874937C11.3441 0.5495 11.8717 0.5495 12.1972 0.874937Z" fill="white"/>
                        </svg>
                    </a>
                </div>

               </div>
                <a class="nav-link text-gray text-gray-hover px-4" style="border-left: 1px solid #DDDDDD;" href="{{ route('login') }}">{{ __('message.auth.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-brand btn-elevate kt-login__btn-primary text-uppercase h-42px px-4" style="min-width:108px">
                    {{ __('message.auth.sign_up') }}
                </a>
            </div>
        </nav>
        <div class="nav-mobile__action">
            <a href="#" class="d-flex align-items-center"><img src="{{ asset('/media/PerioDx-black.svg') }}" alt="PerioDx-black.svg"></a>

            <div class="d-flex align-items-center">
                <div class="mr-3 pr-3 d-flex link-dropdown" style="height: 32px; border-right: 1px solid #DDDDDD;">
                    <a class="w-100 d-flex align-items-center"  href="#" role="button" id="dropdownLanguage" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 12C21 16.9706 16.9706 21 12 21M21 12C21 7.02944 16.9706 3 12 3M21 12H3M12 21C7.02944 21 3 16.9706 3 12M12 21C13.6569 21 15 16.9706 15 12C15 7.02944 13.6569 3 12 3M12 21C10.3431 21 9 16.9706 9 12C9 7.02944 10.3431 3 12 3M3 12C3 7.02944 7.02944 3 12 3" stroke="#757575" stroke-width="1.7" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-language" aria-labelledby="dropdownLanguage">
                     <a class="dropdown-item text-black d-flex justify-content-between align-items-center {{ app()->getLocale() == 'ja' ? 'active' : ''}}" href="{!! route('language', ['ja']) !!}">
                         <span class="pl-2 dropdown-aside-text">{{ __('message.ja') }}</span>
                         <svg style="display: none" width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1972 0.874937C12.5226 1.20037 12.5226 1.72801 12.1972 2.05345L5.12609 9.12452C4.80065 9.44995 4.27302 9.44995 3.94758 9.12452L0.412046 5.58898C0.0866095 5.26355 0.0866095 4.73591 0.412046 4.41047C0.737483 4.08503 1.26512 4.08503 1.59056 4.41047L4.53684 7.35675L11.0186 0.874937C11.3441 0.5495 11.8717 0.5495 12.1972 0.874937Z" fill="white"/>
                         </svg>
                     </a>
                     <a class="dropdown-item text-black d-flex justify-content-between align-items-center {{ app()->getLocale() == 'en' ? 'active' : ''}}" href="{!! route('language', ['en']) !!}">
                         <span class="pl-2 dropdown-aside-text">{{ __('message.en') }}</span>
                         <svg style="display: none" width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1972 0.874937C12.5226 1.20037 12.5226 1.72801 12.1972 2.05345L5.12609 9.12452C4.80065 9.44995 4.27302 9.44995 3.94758 9.12452L0.412046 5.58898C0.0866095 5.26355 0.0866095 4.73591 0.412046 4.41047C0.737483 4.08503 1.26512 4.08503 1.59056 4.41047L4.53684 7.35675L11.0186 0.874937C11.3441 0.5495 11.8717 0.5495 12.1972 0.874937Z" fill="white"/>
                         </svg>
                     </a>
                 </div>

                </div>
                <a class="nav-link text-gray text-gray-hover px-4 fs-13 nav-mobile__action--login" href="{{ route('login') }}">{{ __('message.auth.login') }}</a>
                <a href="{{ route('register') }}" style="padding: 0.4rem 1rem;"
                   class="btn btn-brand btn-elevate kt-login__btn-primary fs-13 h-32px px-4">
                    {{ __('message.auth.sign_up') }}
                </a>

                <label class="nav-mobile__btn openable">
                    <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 0C0.447715 0 0 0.447715 0 1C0 1.55228 0.447715 2 1 2H19C19.5523 2 20 1.55228 20 1C20 0.447715 19.5523 0 19 0H1ZM0 6C0 5.44772 0.447715 5 1 5H19C19.5523 5 20 5.44772 20 6C20 6.55228 19.5523 7 19 7H1C0.447715 7 0 6.55228 0 6ZM0 11C0 10.4477 0.447715 10 1 10H19C19.5523 10 20 10.4477 20 11C20 11.5523 19.5523 12 19 12H1C0.447715 12 0 11.5523 0 11Z" fill="#757575"/>
                    </svg>
                </label>
                <label class="nav-mobile__btn closeable d-none">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.70711 13.7071C1.31658 14.0976 0.683417 14.0976 0.292893 13.7071C-0.0976311 13.3166 -0.0976311 12.6834 0.292893 12.2929L5.5858 6.99998L0.292893 1.70707C-0.0976311 1.31655 -0.0976311 0.683383 0.292893 0.292859C0.683417 -0.0976658 1.31658 -0.0976658 1.70711 0.292859L7.00002 5.58577L12.2929 0.292927C12.6834 -0.0975981 13.3165 -0.0975981 13.7071 0.292927C14.0976 0.683451 14.0976 1.31662 13.7071 1.70714L8.41423 6.99998L13.7071 12.2929C14.0977 12.6834 14.0977 13.3166 13.7071 13.7071C13.3166 14.0976 12.6835 14.0976 12.2929 13.7071L7.00002 8.4142L1.70711 13.7071Z" fill="#757575"/>
                    </svg>
                </label>
            </div>
        </div>

        <nav class="nav-mobile" aria-label="nav-mobile">

            <ul class="nav-mobile__list">
                <li><a href="#" class="text pl-0">{{ __('message.header.pricing') }}</a></li>
                <li><a href="#" class="text pl-0">{{ __('message.header.news') }}</a></li>
                <li style="padding-top: 24px">
                    <a href="/register" class="w-100 btn btn-brand kt-login__btn-primary text-uppercase d-flex justify-content-center">{{ __('message.auth.sign_up')}}</a>
                </li>
                <li>
                    <a href="" class="btn kt-login__btn-primary text-uppercase border mt-3 w-100 d-flex justify-content-center">{{ __('message.auth.login')}}</a>
                </li>
            </ul>
        </nav>

    </div>
</div>
</div>
<!-- end:: Header -->
