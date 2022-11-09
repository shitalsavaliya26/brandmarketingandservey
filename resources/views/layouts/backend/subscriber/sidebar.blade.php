<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item @if (\Request::is('subscriber/dashboard')) {{ 'active' }} @endif">
            <a class="nav-link" href="{{ route('subscriber.dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item @if (\Request::is('subscriber/brand') || \Request::is('subscriber/brand/*') || \Request::is('subscriber/advertisement/*') || \Request::is('subscriber/setting/*') || \Request::is('subscriber/topic/*') || \Request::is('subscriber/notification-topic/*') || \Request::is('subscriber/buy/*') || \Request::is('subscriber/win/*') || \Request::is('subscriber/get-message-history/*') || \Request::is('subscriber/poll/*') || \Request::is('subscriber/notification-topic-history/*') ||  (\Request::route()->getName() == "polltitle.index") || (\Request::route()->getName() == "polltitle.create") || (\Request::route()->getName() == "polltitle.update") ) {{ 'active' }} @endif">
            <a class="nav-link" href="{{ route('brand.index') }}">
                <i class="ti-bookmark menu-icon"></i>
                <span class="menu-title">My Brands</span>
            </a>
        </li>

        <li class="nav-item @if (\Request::is('subscriber/topup') || \Request::is('subscriber/topup/*')) {{ 'active' }} @endif">
            <a class="nav-link" href="{{ route('topup.index') }}">
                <i class="ti-money menu-icon"></i>
                <span class="menu-title">Top Up</span>
            </a>
        </li>
        <li class="nav-item @if (\Request::is('subscriber/profile') || \Request::is('subscriber/profile/*')) {{ 'active' }} @endif">
            <a class="nav-link" href="{{ route('subscriber.profile') }}">
                <i class="ti-user menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>
        <li class="nav-item @if (\Request::is('subscriber/withdraw') || \Request::is('subscriber/withdraw/*')) {{ 'active' }} @endif">
            <a class="nav-link" href="{{ route('withdraw.index') }}">
                <i class="ti-back-left menu-icon"></i>
                <span class="menu-title">Withdraw Funds</span>
            </a>
        </li>
    </ul>
</nav>

<form id="logout-form" action="{{ route('subscriber.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
