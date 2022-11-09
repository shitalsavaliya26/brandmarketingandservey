<!DOCTYPE html>
<html lang="en">

<head>
  <title> @yield('title')</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Example Subscriber</title>
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
  <link href="{{ asset('backend/css/vertical-layout-light/style.css').'?v='.time() }}" rel="stylesheet">

  @if ((\Request::route()->getName() == "brand.create") || (\Request::route()->getName() == "main-brand.edit") || (\Request::route()->getName() == "brand.edit"))
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  @else
  <link href="{{asset('backend/vendors/select2/select2.min.css').'?v='.time() }}" rel="stylesheet">
  <link href="{{asset('backend/vendors/select2-bootstrap-theme/select2-bootstrap.min.css').'?v='.time()}}" rel="stylesheet">
  @endif
  
  <link href="{{ asset('backend/css/custom.css'). '?v=' . time() }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('backend/css/toastr.css') . '?v=' . time() }}" rel="stylesheet">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('backend/images/favicon/32fevicon.png')}}" />
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" id="theme-styles">



  {{-- select 2 --}}
 


  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <style>
    .error{
      color:red;
    }
  </style>
  @yield('css')
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    @include('layouts.backend.subscriber.header')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      @include('layouts.backend.subscriber.sidebar')
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
        <div id="loader"></div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        @include('layouts.backend.subscriber.foot')
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
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


  @if ((\Request::route()->getName() == "brand.create") || (\Request::route()->getName() == "main-brand.edit") || (\Request::route()->getName() == "brand.edit"))
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
  @else
  <script src="{{asset('backend/vendors/select2/select2.min.js')}}"></script>
  <script src="{{asset('backend/js/select2.js')}}"></script>
  @endif
  <script>
     var brandExits = "{{ route('brandExits') }}";
      var group_id = "";
  </script>
  <!-- End custom js for this page-->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('backend/js/custom.js').'?v='.time() }}"></script>
    <script src="{{ asset('backend/js/toastr.min.js') }}"></script>

  @yield('script')

  <script>
    var spinner = $('#loader');
    $(function() {
      $('form').submit(function(e) {
        spinner.show();
        var form_id = $(this).attr("id"); //get submit action from form
        if(form_id == "profile-form-edit"){
          setTimeout(() => spinner.hide(), 500);
        }else{
          if ( $("input").hasClass("error") ||  $("select").hasClass("error") ) {
            spinner.hide();
          }
        }
      });
    });
  </script>
</body>
</html>
