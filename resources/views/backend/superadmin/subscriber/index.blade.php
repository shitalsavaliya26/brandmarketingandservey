@extends('layouts.backend.superadmin.main')
@section('title', 'Subscribers')
@section('css')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
<style>
    .subscriber-name {
        height: 47px;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('superadmin.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Subscribers</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success" role="alert" style="display:none;">

                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Subscribers</h4>
                    <h4 class="card-title text-right">
                    </h4>
                </div>
                <div class="align-items-center justify-content-between">
                    <h4 class="card-title">
                        <form method="get" >
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                            <select class="form-control" name="status" id="subscriber-status-filter">
                                                <option value="null">--Select Status--</option>
                                                <option value="1"  @if(app('request')->input('status') == "1") selected @endif >Active</option>
                                                <option value="0"  @if(app('request')->input('status') == "0") selected @endif>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <input type="search" name="name" id="subscriber-name-filter" class="form-control subscriber-name" placeholder="Search" value="{{ app('request')->input('name') }}">
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <select class="form-control" name="country" id="subscriber-country-filter">
                                                <option value="null">--Select Country--</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}" @if($country->id == app('request')->input('country')) selected @endif>{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-3 vertical-center d-flex" style="align-items: center;">
                                            <button type="submit" class="btn btn-success btn-sm btn-xs-block d-inline search ">Search</button>
                                            <a class="btn btn-warning btn-sm btn-xs-block d-inline" href="{{route('subscriber.index')}}" style="margin-left: 20px;">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </h4>
                </div>
                @include('backend.superadmin.subscriber.table',$subscribers)
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="change-password-form" class="form-horizontal" name="change-password-form">
                    @csrf
                    <input type="hidden" id="subscriber_id" name="hidden"/>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class='form-control' placeholder="New Password"/>
                    </div>
                    <div class="form-group">
                        <input id="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation"/>
                    </div>
                    <div class="form-group">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="change-password">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>

<script type="text/javascript">
    function changeStatus(user_id, status,reuse = null){
        swal({
              title: `Are you sure you want to change the status?`,
              icon: "warning",
              buttons: true,
              dangerMode: true,
          })
          .then((change) => {
            if (change) {
                var url = "{{ route('subscriber.change-status') }}";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "status": status,
                        "user_id": user_id,
                    },
                    success: function(data){
                        $(".alert-success").show();
                        $('.alert-success').html(data.success).fadeIn('slow');
                        $('.alert-success').delay(3000).fadeOut('slow');
                    }
                });
            }else{
                if(status == 1){
                    $('#status'+user_id).parent('.toggle').removeClass('btn-danger off');
                    $('#status'+user_id).parent('.toggle').addClass('btn-sucess');
                }else{
                    $('#status'+user_id).parent('.toggle').addClass('btn-danger off');
                    $('#status'+user_id).parent('.toggle').removeClass('btn-sucess');
                }

            }
        });
    }
    
    $('#messageModal').on('show.bs.modal', function (event) {
        let id = $(event.relatedTarget).data('id');
        $('input[name="hidden"]').val(id);
        $(this).find("#password-error").remove();
        $(this).find("#password_confirmation-error").remove();
        $("#change-password-form")[0].reset();
    });

    $('#messageModal').on('hidden.bs.modal', function (event) {
        $('input[name="hidden"]').val(null);
        $(this).find("#password-error").remove();
        $(this).find("#password_confirmation-error").remove();
        $("#change-password-form")[0].reset();
    });

    $("#change-password").on("click", function(e){
        e.preventDefault();
        if ($('#change-password-form').valid()){
            var id = $('#subscriber_id').val();
            var password = $('#password').val();
            changePassword(id, password);
        }
    });

    function changePassword(id, password){
        var paswordURL = "{{ route('subscriber.change-password') }}";
        $.ajax({
            url: paswordURL,
            type: "POST",
            data: {
                _token: '{{csrf_token()}}',
                id: id,
                password: password,
            },
            dataType : 'json',
            success: function(result){
                $(".modal").modal('hide');
                $(".alert-success").show();
                $('.alert-success').html(result.success).fadeIn('slow');
                $('.alert-success').delay(3000).fadeOut('slow');
            },
            error: function (errorData) {
                $(".modal").modal('hide');
            }
        });
    }

</script>

@endsection
