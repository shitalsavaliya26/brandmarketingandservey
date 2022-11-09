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
                            <h6 class="font-weight-light">Resend verification link.</h6>
                            <form method="POST" action='{{ route('subscriber.resendverification') }}' class="pt-3"
                                id="resendverificationform">
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
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SEND</button>
                                </div> 
                                <div class="text-center mt-4 font-weight-light">
                                    Already verified an account? <a href="{{ route('subscriber.login') }}"
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
            return value.indexOf(" ") < 0 && value != "";
        }, "No space please.");


        // register form validation
        if ($("#resendverificationform").length > 0) {
            $("#resendverificationform").validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        noSpace: true,
                    },
                },
                messages: {
                    email: {
                        required: "Email is required.",
                        email: "Please enter valid email."
                    },
                },
            })
        }
    </script>
    <!-- endinject -->
</body>

</html>
