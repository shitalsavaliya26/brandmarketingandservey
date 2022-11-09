<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item @if(\Request::is('admin/dashboard')) {{'active'}} @endif">
      <a class="nav-link" href="{{route('superadmin.dashboard')}}">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item @if(\Request::is('admin/subscriber') || \Request::is('admin/subscriber/*')) {{'active'}} @endif">
      <a class="nav-link" href="{{route('subscriber.index')}}">
        <i class="ti-user menu-icon"></i>
        <span class="menu-title">Subscribers</span>
      </a>
    </li>
    <li class="nav-item @if(\Request::is('subscriber/price-setting') || \Request::is('subscriber/price-setting/*')) {{'active'}} @endif">
      <a class="nav-link" href="{{route('price-setting.index')}}">
        <i class="ti-money menu-icon"></i>
        <span class="menu-title">Price Setting</span>
      </a>
    </li>

    <li class="nav-item @if(\Request::route()->getName() == "withdrawal-request.index") {{'active'}} @endif">
      <a class="nav-link" href="{{route('withdrawal-request.index')}}">
        <i class="ti-hand-open menu-icon"></i>
        <span class="menu-title">Withdrawal Request</span>
      </a>
    </li>

    <li class="nav-item @if(\Request::route()->getName() == "user_withdraw_requests.index") {{'active'}} @endif">
      <a class="nav-link" href="{{route('user_withdraw_requests.index')}}" >
        <i class="ti-hand-open menu-icon"></i>
        <span class="menu-title">User Withdrawal Request</span>
      </a>
    </li>

    <li class="nav-item @if(\Request::is('admin/currency') || \Request::is('admin/currency/*') || \Request::is('admin/currency-rate')) {{'active'}} @endif">
      <a class="nav-link" href="{{route('currency.index')}}">
        <i class="ti-money menu-icon"></i>
        <span class="menu-title">Currency</span>
      </a>
    </li>
  </ul>
</nav>

<form id="superadminlogout-form" action="{{ route('superadmin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>