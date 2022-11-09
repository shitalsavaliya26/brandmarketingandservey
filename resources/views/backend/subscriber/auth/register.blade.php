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
        .error{
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
                            <h6 class="font-weight-light">Sign up to continue.</h6>
                            <form method="POST" action='{{ route('subscriber.register') }}' class="pt-3"
                                id="registerform">
                                @csrf
                                <div class="form-group">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" autocomplete='off'
                                        placeholder="E-Mail Address" name="email" value="{{ old('email') }}"
                                        autofocus>
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
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Password" name="password">
                                    @error('password')
                                        <span class="help-block text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- <div class="form-group">
                                    <input id="confirm_password" type="password" autocomplete='off'
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Confirm Password" name="confirm_password">
                                    @error('confirm_password')
                                        <span class="help-block text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN
                                        UP </button>
                                </div>
                                 <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                          
                                        </label>
                                    </div>
                                    <a href="{{ route('subscriber.resendverification') }}" class="auth-link">Resend verification mail</a>
                                </div> 
                                <div class="d-flex justify-content-center mt-4 mb-4">
                                    <hr style="margin-right:20px;color:#4B49AC;background-color:#4B49AC" width="25%">
                                    <p class="d-inline text-navy-1 font-22 mt-1">OR</p>
                                    <hr style="margin-left:20px;color:#4B49AC;background-color:#4B49AC" width="25%">
                                </div>
                                <div class="mb-2">
                                    <a type="button" class="btn btn-block btn-facebook auth-form-btn"
                                        href="{{ route('subscriber.redirect', 'facebook') }}?page=register">
                                        <i class="ti-facebook mr-2"></i>Sign up using facebook
                                    </a>
                                </div>
                                <div class="mb-2">
                                    <a type="button" class="btn btn-block btn-google auth-form-btn"
                                        href="{{ route('subscriber.redirect', 'google') }}?page=register">
                                        <i class="ti-google mr-2"></i>Sign up using google
                                    </a>
                                </div>
                                <div class="text-center mt-4 font-weight-light">
                                    Already have an account? <a href="{{ route('subscriber.login') }}"
                                        class="auth-link">Sign
                                        in instead</a>
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
    <script src="{{ asset('backend/js/plugins/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('backend/js/plugins/validate/additional-methods.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".message-alert").fadeTo(3000, 500).slideUp(500, function() {
                $(".message-alert").slideUp(500);
            });
        });
        jQuery.validator.addMethod("noSpace", function(value, element) {
            // return value.indexOf(" ") < 0 && value != ""; 
            return value.indexOf(" ") < 0 && value != "";
        }, "No space please.");


        // register form validation
        if ($("#registerform").length > 0) {
            $("#registerform").validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        noSpace: true,
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        noSpace: true,
                    },
                    // confirm_password: {
                    //     required: true,
                    //     minlength: 8,
                    //     equalTo: "#password",
                    //     noSpace: true,
                    // },
                },
                messages: {
                    email: {
                        required: "Email is required.",
                        email: "Please enter valid email."
                    },
                    password: {
                        required: "Password is required.",
                        minlength: "Your password must be at least 8 characters long.",
                    },
                    // confirm_password: {
                    //     required: "Confirm password is required.",
                    //     equalTo: "Please enter the same password as above."
                    // },
                },
            })
        }
    </script>
    <!-- endinject -->
</body>

</html>
