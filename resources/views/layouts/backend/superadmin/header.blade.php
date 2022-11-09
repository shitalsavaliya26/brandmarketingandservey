<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('superadmin.dashboard') }}"><img src="{{asset('backend/images/logo.png')}}" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('superadmin.dashboard') }}"><img src="{{asset('backend/images/logo-mini.png')}}" alt="logo"/></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav list-inline mx-auto justify-content-center">
            <li class="nav-item">
                <div class>
                    <div class="prepend hover-cursor" id="navbar-search-icon">
                    </div>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item">
                <span class="nav-link btn-primary" id="walletBalance" style="color: white; padding: 9px;border-radius: 4px;"><b>Total Gross Revenue : ${{number_format($totalGrossRevenue, 3, '.', ',')}}</b></span>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img  onerror="this.src='{{asset('backend/images/faces/face28.png')}}'" src="{{asset('uploads/subscriber/mobile_logo/'.auth()->user()->logo)}}"  alt="profile"/>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a href="{{ route('superadmin.logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('superadminlogout-form').submit();">
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