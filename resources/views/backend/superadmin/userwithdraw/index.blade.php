@extends('layouts.backend.superadmin.main')
@section('title', 'User Withdraw History')
@section('css')
    <style>
        .topup-status {
            padding: 6px 12px;
            border-radius: 15px;
        }
    </style>
    <link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('superadmin.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('user_withdraw_requests.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">User Withdraw History</h5></a></div>
                      
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success" role="alert" style="display:none;"></div>
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">User Withdraw History</h4>
                        <h4 class="card-title text-right"></h4>
                    </div>
                    <div class="align-items-center justify-content-between">
                        <h4 class="card-title">
                            <form method="get">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-3">
                                                <select class="form-control" name="status" id="subscriber-status-filter">
                                                    <option value="all"
                                                        @if (app('request')->input('status') == 'all') selected @endif>All</option>
                                                    <option value="rejected"
                                                        @if (app('request')->input('status') == 'rejected') selected @endif>Rejected</option>
                                                    <option value="approved"
                                                        @if (app('request')->input('status') == 'approved') selected @endif>Success</option>
                                                    <option value="pending"
                                                        @if (app('request')->input('status') == 'pending' || app('request')->input('status') == '') selected @endif>Pending</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-3 vertical-center d-flex"
                                                style="align-items: center;">
                                                <button type="submit"
                                                    class="btn btn-success btn-sm btn-xs-block d-inline search ">Search</button>
                                                <a class="btn btn-warning btn-sm btn-xs-block d-inline"
                                                    href="{{ route('user_withdraw_requests.index') }}"
                                                    style="margin-left: 20px;">Clear</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </h4>
                    </div>
                    @include('backend.superadmin.userwithdraw.table', $withdraws)
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="refuseModal" tabindex="-1" role="dialog" aria-labelledby="refuseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refuseModalLabel">Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="withdraw_id" name="hidden" />
                        <div class="form-group">
                            <label for="reason" class="col-form-label">Reason:</label>
                            <textarea class="form-control" id="reason"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="refuse-status-reason">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    <script type="text/javascript">
        function changeStatus(id, type, reason) {
            var urls = "{{ route('userwithdrawal.changestatus') }}";
            $.ajax({
                type: "GET",
                url: urls,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "type": type,
                    "id": id,
                    "reason": reason,
                },
                success: function(data) {
                    $(".alert-success").show();
                    $('.alert-success').html(data.success).fadeIn('slow');
                    $('.alert-success').delay(3000).fadeOut('slow');
                    $(".table").load(location.href + " .table");
                }
            });
        }

        $(".user-change-status").on("click", function() {
            var type = $(this).attr("id");
            var id = $(this).attr("data-id");
            swal({
                    title: 'Are you sure you want to ' + type + '?',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((change) => {
                    if (change) {
                        if (type == 'approve') {
                            changeStatus(id, type, reason = null);
                        }
                        if (type == 'refuse') {
                            $('#refuseModal').modal('show');
                            $('#withdraw_id').val()
                            $('#withdraw_id').val(id);
                        }
                    }
                });
        });

        $("#refuse-status-reason").on("click", function() {
            var reason = $('#reason').val();
            if (!reason.trim()) {
                alert("Please enter reason");
            } else {
                $('#reason').val(null);
                $('#refuseModal').modal('hide');
                var type = 'refuse';
                var id = $('#withdraw_id').val();
                changeStatus(id, type, reason);
            }
        });

        $('#refuseModal').on('show.bs.modal', function(event) {
            let id = $(event.relatedTarget).data('id');
            $('input[name="hidden"]').val(id);
        });
    </script>

@endsection
