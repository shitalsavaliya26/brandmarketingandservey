<!DOCTYPE html>
<html lang="en">

<head>
  <title> @yield('title')</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Example Superadmin</title>
  <!-- plugins:css -->
  <link href="{{ asset('backend/vendors/feather/feather.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/vendors/ti-icons/css/themify-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/vendors/css/vendor.bundle.base.css') }}" rel="stylesheet">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link href="{{ asset('backend/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/vendors/ti-icons/css/themify-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/js/select.dataTables.min.css') }}" type="text/css"  rel="stylesheet">
  <link href="{{ asset('backend/vendors/mdi/css/materialdesignicons.min.css') }}" type="text/css" rel="stylesheet">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="{{ asset('backend/css/slick-theme.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/css/vertical-layout-light/style.css') }}" rel="stylesheet">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('backend/images/favicon/32fevicon.png')}}" />
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" id="theme-styles">
  <link href="{{ asset('backend/css/custom.css'). '?v=' . time() }}" rel="stylesheet" type="text/css" />
  <style>
    .error{
      color:red;
    }
    .header-data{
      background: #4747A1;
      padding: 10px;
      border-radius: 15px;
    }
  </style>
  @yield('css')
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    @include('layouts.backend.superadmin.header')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      @include('layouts.backend.superadmin.sidebar')
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          @if(Session::has('success'))
            <div class="alert alert-success" role="alert">
              <strong>Success!</strong> {{Session::get('success')}}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif
          @if(Session::has('error'))
            <div class="alert alert-danger" role="alert">
              <strong>Error!</strong> {{Session::get('error')}}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif
          @yield('content')
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        @include('layouts.backend.superadmin.foot')
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <div id="loader"></div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="{{ asset('backend/vendors/js/vendor.bundle.base.js') }}"></script>
  
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{ asset('backend/vendors/chart.js/Chart.min.js') }}"></script>
  <script src="{{ asset('backend/vendors/datatables.net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('backend/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
  <script src="{{ asset('backend/js/dataTables.select.min.js') }}"></script>
  <script src="{{ asset('backend/js/slick.min.js') }}"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{ asset('backend/js/off-canvas.js') }}"></script>
  <script src="{{ asset('backend/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('backend/js/template.js') }}"></script>
  <script src="{{ asset('backend/js/settings.js') }}"></script>
  <script src="{{ asset('backend/js/todolist.js') }}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{ asset('backend/js/dashboard.js') }}"></script>
  <script src="{{ asset('backend/js/Chart.roundedBarCharts.js') }}"></script>
  <script src="{{asset('backend/js/plugins/validate/jquery.validate.min.js')}}"></script>
	<script src="{{asset('backend/js/plugins/validate/additional-methods.min.js')}}"></script>
	<script src="{{asset('backend/js/plugins/validate/additional-methods.min.js')}}"></script>
  <!-- End custom js for this page-->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
  <script src="{{asset('backend/js/custom.js'). '?v=' . time() }}"></script>

  @yield('script')
  @include('layouts.backend.superadmin.jsroute')


</body>
</html>
