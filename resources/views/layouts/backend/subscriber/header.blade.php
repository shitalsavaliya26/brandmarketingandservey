<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('subscriber.dashboard') }}"><img onerror="this.src='{{asset('backend/images/logo.png')}}'" src="{{asset('uploads/subscriber/desktop_logo/'.auth()->user()->desktop_logo)}}" class="mr-2" alt="logo" style="width:110px; height:34px"/></a>

        <a class="navbar-brand brand-logo-mini" href="{{ route('subscriber.dashboard') }}"><img onerror="this.src='{{asset('backend/images/logo-mini.png')}}'" src="{{asset('uploads/subscriber/desktop_logo/'.auth()->user()->desktop_logo)}}" alt="logo" style="width:40px; height:34px"/></a>
        
        {{-- <a class="navbar-brand brand-logo-mini" href="{{ route('subscriber.dashboard') }}"><img onerror="this.src='{{asset('backend/images/logo-mini.png')}}'" src="{{asset('uploads/subscriber/mobile_logo/'.auth()->user()->logo)}}" alt="logo" style="width:40px; height:34px"/></a> --}}
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
            @if (!empty(auth()->user()->currency_id))
            <li class="nav-item">
                <span class="nav-link btn-primary" id="walletBalance" style="color: white; padding: 9px;border-radius: 4px;"><b>Free Credit : {{ auth()->user()->currency->symbol ?? " " }} {{number_format(auth()->user()->bonus_amount, 2, '.', ',')}}</b></span>
            </li>

            <li class="nav-item">
                <span class="nav-link btn-primary" id="walletBalance" style="color: white; padding: 9px;border-radius: 4px;"><b>Account Balance : {{ auth()->user()->currency->symbol ?? " " }} {{number_format(auth()->user()->wallet_amount, 2, '.', ',')}}</b></span>
            </li>
            @endif
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img  onerror="this.src='{{asset('backend/images/faces/face28.png')}}'" src="{{asset('uploads/subscriber/mobile_logo/'.auth()->user()->logo)}}"  alt="profile"/>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a href="{{ route('subscriber.logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti-power-off text-primary"></i>Logout
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>