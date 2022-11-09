<!DOCTYPE html>
<html lang="en">

<head>
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
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="{{ asset('backend/css/vertical-layout-light/style.css') }}" rel="stylesheet">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('backend/images/favicon/32fevicon.png')}}" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="{{asset('/backend/images/logo.png')}}" alt="logo">
              </div>
              <h6 class="font-weight-light">Sign in to continue.</h6>
              <form method="POST" action='{{ route("superadmin.login") }}' class="pt-3">
                @csrf
                <div class="form-group">
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    autocomplete='off' placeholder="E-Mail Address" name="email" value="{{ old('email') }}" autofocus>
                    @error('email')
                      <span class="help-block text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                    @error('error')
                      <span class="help-block text-danger" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                </div>
                <div class="form-group">
                  <input id="password" type="password" autocomplete='off'
                    class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                    name="password">
                    @error('password')
                      <span class="help-block text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input"> Keep me signed in
                    </label>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{ asset('backend/vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{ asset('backend/js/off-canvas.js') }}"></script>
  <script src="{{ asset('backend/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('backend/js/template.js') }}"></script>
  <script src="{{ asset('backend/js/settings.js') }}"></script>
  <script src="{{ asset('backend/js/todolist.js') }}"></script>
  <!-- endinject -->
</body>
</html>
