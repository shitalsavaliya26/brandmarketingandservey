<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Example</title>
  <!-- plugins:css -->
  <link href="{{ asset('backend/vendors/feather/feather.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/vendors/ti-icons/css/themify-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/vendors/css/vendor.bundle.base.css') }}" rel="stylesheet">
  <!-- endinject -->
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
              <h4>Change Password</h4>
              <h6 class="font-weight-light">Enter the following to change your account</h6>
              <form method="POST" action='{{ route("password.change.post") }}' class="pt-3">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="New Password" name="password" value="{{ old('password') }}"autofocus>
                    @error('password')
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
                  <input id="password-confirm" type="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Confirm Password" name="password_confirmation" autofocus>
                    @error('password_confirmation')
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
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Change Password </button>
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
  <!-- inject:js -->
  <script src="{{ asset('backend/js/off-canvas.js') }}"></script>
  <script src="{{ asset('backend/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('backend/js/template.js') }}"></script>
  <script src="{{ asset('backend/js/settings.js') }}"></script>
  <script src="{{ asset('backend/js/todolist.js') }}"></script>
  <!-- endinject -->
</body>
</html>
