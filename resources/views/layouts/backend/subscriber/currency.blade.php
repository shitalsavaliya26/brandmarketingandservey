<!DOCTYPE html>
<html lang="en">

<head>
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
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link href="{{ asset('backend/css/vertical-layout-light/style.css') }}" rel="stylesheet">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('backend/images/favicon/32fevicon.png') }}" />
    <style>
        .error {
            color: red;
        }

    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        @if (Session::has('success'))
                            <div class="alert alert-success message-alert" role="alert">
                                <strong>Success!</strong> {{ Session::get('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (Session::has('error'))
                            <div class="alert alert-danger message-alert" role="alert">
                                <strong>Error!</strong> {{ Session::get('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{ asset('/backend/images/logo.png') }}" alt="logo">
                            </div>
                            <h6 class="font-weight-bold">Create an account!</h6>
                            <form method="POST" action='{{ route('currency.form',[$id]) }}' name="currency-form"
                                id="currency-form" class="pt-3" enctype="multipart/form-data">
                                @csrf  
                                <input type="hidden" id="user_id" name="user_id" value="{{ $id }}">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div class="form-group row">
                                          <div class="col-12">
                                              <label class="">Currency</label>
                                              <select class="form-control" id="currency_id11" name="currency_id" >
                                                  <option value=''>--Select Currency--</option>
                                                  @foreach ($currencies as $currency)
                                                      <option value="{{ $currency->id }}">{{ $currency->code }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                              <span
                                                  class="help-block text-danger">{{ $errors->first('currency_id') }}</span>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
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
    <script src="{{ asset('backend/js/plugins/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('backend/js/plugins/validate/additional-methods.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/js/custom/member.js') . '?v=' . time() }}"></script>
    <script>
        $(document).ready(function() {
            $(".message-alert").fadeTo(3000, 500).slideUp(500, function() {
                $(".message-alert").slideUp(500);
            });
        });
    </script>
    <!-- endinject -->
</body>

</html>
