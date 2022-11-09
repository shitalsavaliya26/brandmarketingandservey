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
                    <div class="col-lg-8 mx-auto">
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
                            <form method="POST" action='{{ route('subscriber.register') }}' name="register-form"
                                id="register-form" class="pt-3" enctype="multipart/form-data">
                                @csrf
                                <!-- <div class="row">
                  <div class="col-md-6">
                      <div class="form-group row">
                          <div class="col-12">
                              <label class="">First Name</label>
                              <input type="text" id="firstname" class="form-control" name="firstname" autocomplete="firstname" autofocus placeholder="Enter first name">
                              <span class="help-block text-danger">{{ $errors->first('firstname') }}</span>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group row">
                          <div class="col-12">
                              <label class="">Last Name</label>
                              <input type="text" id="lastname" class="form-control" name="lastname" autocomplete="lastname" autofocus placeholder="Enter last name">
                              <span class="help-block text-danger">{{ $errors->first('lastname') }}</span>
                          </div>
                      </div>
                  </div>
                </div> -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Organization Name</label>
                                                <input type="text" id="organization_name"
                                                    class="form-control capital-input" name="organization_name"
                                                    autocomplete="off" autofocus placeholder="Enter organization name">
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('organization_name') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Contact Number</label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="calling_code"
                                                            name="calling_code">
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->calling_code }}">
                                                                    {{ $country->calling_code }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span
                                                            class="help-block text-danger">{{ $errors->first('calling_code') }}</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" id="contact_number" class="form-control"
                                                            name="contact_number" autocomplete="off" autofocus
                                                            placeholder="Enter contact number">
                                                        <span
                                                            class="help-block text-danger">{{ $errors->first('contact_number') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Email</label>
                                                <input type="text" id="email" class="form-control"
                                                    name="email" autocomplete="off" autofocus
                                                    placeholder="Enter email">
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('email') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Password</label>
                                                <input type="password" id="password" class="form-control"
                                                    name="password" autocomplete="off" autofocus
                                                    placeholder="Enter password">
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('password') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Mobile Logo (Standard Size: 1:1)</label>
                                                <input type="file" id="logo" class="form-control"
                                                    name="logo" id="logo">
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('logo') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Desktop Logo (Standard Size: 3:1)</label>
                                                <input type="file" id="desktop_logo" class="form-control"
                                                    name="desktop_logo" id="desktop_logo">
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('desktop_logo') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="">Country</label>
                                                <select class="form-control" id="country_id" name="country_id">
                                                    <option value=''>--Select Country--</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}">{{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('country_id') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group row">
                                          <div class="col-12">
                                              <label class="">Currency</label>
                                              <select class="form-control" id="currency_id" name="currency_id" >
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
                                <button type="submit" class="btn mt-2 btn-primary mr-2">Sign up</button>

                                <div class="d-flex justify-content-center mt-3">
                                    <hr style="margin-right:20px;color:#4B49AC;background-color:#4B49AC" width="25%">
                                    <p class="d-inline text-navy-1 font-22 mt-1">OR</p>
                                    <hr style="margin-left:20px;color:#4B49AC;background-color:#4B49AC" width="25%">
                                </div>

                                <div class="row mt-5">
                                    <div class="col-md-6">
                                        <a type="button" class="btn btn-block btn-facebook auth-form-btn"
                                        href="{{ route('subscriber.redirect', 'facebook') }}">
                                        <i class="ti-facebook mr-2"></i>Sign up using facebook
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a type="button" class="btn btn-block btn-google auth-form-btn"
                                        href="{{ route('subscriber.redirect', 'google') }}">
                                        <i class="ti-google mr-2"></i>Sign up using google
                                        </a>
                                  </div>
                                </div>
                            </form>
                            <br>
                            Already have an account? <a href="{{ route('subscriber.login') }}" class="auth-link">Sign
                                in instead</a>
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
       ;


        $(document).ready(function() {
            $(".message-alert").fadeTo(3000, 500).slideUp(500, function() {
                $(".message-alert").slideUp(500);
            });
        });

       
        /* change country to get currency */
        var fetchurl = "{{ route('currencyget', ':id') }}"
        $('#country_id').on('change', function() {
            var val = this.value;
            if(val != ''){
                url = fetchurl.replace(':id', val);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            $("#currency_id").val(response.data);
                            $("#currency_id").blur(); 
                        }
                        else{
                            $("#currency_id").val(1);
                            $("#currency_id").blur();
                        }
                    },
                });
            }
        });

        // $('#desktop_logo').change(function() {
        //   $('#desktop_logo').removeData('imageWidth');
        //   $('#desktop_logo').removeData('imageHeight');
        //   var file = this.files[0];
        //   var tmpImg = new Image();
        //   tmpImg.src=window.URL.createObjectURL( file ); 
        //   tmpImg.onload = function() {
        //       width = tmpImg.naturalWidth,
        //       height = tmpImg.naturalHeight;
        //       $('#desktop_logo').data('imageWidth', width);
        //       $('#desktop_logo').data('imageHeight', height);
        //   }
        // });

        // $('#logo').change(function() {
        //   $('#logo').removeData('imageWidth');
        //   $('#logo').removeData('imageHeight');
        //   var file = this.files[0];
        //   var tmpImg = new Image();
        //   tmpImg.src=window.URL.createObjectURL( file ); 
        //   tmpImg.onload = function() {
        //       width = tmpImg.naturalWidth,
        //       height = tmpImg.naturalHeight;
        //       $('#logo').data('imageWidth', width);
        //       $('#logo').data('imageHeight', height);
        //   }
        // });
    </script>
    <!-- endinject -->
</body>

</html>
