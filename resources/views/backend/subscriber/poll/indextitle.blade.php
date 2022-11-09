@extends('layouts.backend.subscriber.main')
@section('title', 'Polls')
@section('css')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection
@section('content')

<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Polls</h5></a></div>
                    
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
                <div class="alert alert-danger" role="alert" style="display:none;">
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Polls</h4>
                    <h4 class="card-title text-right">
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('create-New-Poll', [request()->route('brandId')])}}">Add Poll</a>
                    </h4>
                </div>
                @if(isset($polls) && count($polls) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th width="20%" class="text-left">Poll</th>
                                <th width="20%" class="text-left">Set Target</th>
                                <th width="20%" class="text-left">Status</th>
                                <th width="20%" class="text-left">Questions</th>
                                <th width="20%" class="text-left">Question Cost</th>
                                <th width="20%" class="text-left">Total Cost</th>
                                <th width="20%">Status</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody class="text-center">
                            @foreach($polls as $row)
                                <tr>
                                    @php
                                    $totalcost = App\UserPollHistory::where('poll_title_id', $row->id)->sum('total_cost_usd');

                                    @endphp

                                    <td class="text-left">{{@$row->title}}</td>          
                                    <td class="text-left">{{ (@$row->total_quantity > 0) ? @$row->total_quantity : "Unlimited" }}</td>
                                    <td class="text-left">{{@$row->total_used_quantity }}</td>
                                    <td class="text-left">{{@$row->total_questions}}</td>

                                    
                                    @php
                                        $totalq = $totalquestionusdprice->price*$row->total_questions  
                                    @endphp
                                   
                                    <td class="text-left">{{auth()->user()->currency->symbol}} {{number_format($totalq*auth()->user()->currency->rate,3)}}</td>

                                    @if ($row->total_used_quantity == 0)
                                    <td class="text-left">0</td>
                                    @else
                                    <td class="text-left">{{auth()->user()->currency->symbol}} {{number_format($row->total_cost,3)}}</td>

                                    @endif
                                    <td>
                                        <input name="status" id="status{{$row->id}}" onChange="changeStatus({{$row->id}}, {{$row->status}});" data-id="{{$row->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $row->status== '1' ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                                            <a class="ml-3 text-decoration-none"  href="{{route('poll.index',[$id])}}" title="Questions">
                                                <i class="ti-settings icon"></i>
                                            </a>

                                            {{-- <a class="ml-3 text-decoration-none"  href="{{route('polltitle.update',[$id])}}" title="Update Poll"> 
                                                <i class="ti-pencil icon"></i>
                                            </a> --}}
                                           
                                            <a  href="javascript:void(0)" data-id="{{$id}}" class="ml-3 text-decoration-none" id="deletemainpoll" title="Delete Poll"> 
                                                <i class="ti-trash icon"></i>
                                            </a>
                                           
                                            {{-- <a class="ml-3 text-decoration-none"  href="{{route('poll.history',[$id])}}" title="Poll History"> 
                                                <i class="ti-list icon"></i>
                                            </a>
                                            <a class="ml-3 text-decoration-none"  href="{{route('poll.result',[$id])}}" title="Poll Result"> 
                                                <i class="ti-stats-up icon"></i>
                                            </a> --}}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8">{{ $polls->render('vendor.default_paginate') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>Add a poll</h6>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>

<script type="text/javascript">

    /* delete main poll title */
    $(document).on("click","#deletemainpoll",function(e) {
        e.preventDefault();
        swal({
              title: `Are you sure you want to delete this poll?`,
              icon: "warning",
              buttons: true,
              dangerMode: true,
          })
          .then((change) => {
            if (change) {
                let id = $(this).data("id");
                $.ajax({
                    url: "{{ route('polltitle.delete') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    success: function(response) {
                        location.reload();
                    },
                });                
            }
        });
    });





     function changeStatus(poll_id, status,reuse = null){
        swal({
              title: `Are you sure you want to change the status?`,
              text: "If you change this, it will effect on other poll's status.",
              icon: "warning",
              buttons: true,
              dangerMode: true,
          })
          .then((change) => {
            if (change) {
                var url = "{{ route('poll.change-status') }}";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "status": status,
                        "poll_id": poll_id,
                    },
                    success: function(data){
                        if(data.success){
                            $(".alert-success").show();
                            $('.alert-success').html(data.success).fadeIn('slow');
                            $('.alert-success').delay(3000).fadeOut('slow');

                            $('.toggle').removeClass('btn-danger off');
                            $('.toggle').addClass('btn-danger off');
                            $('#status'+poll_id).parent('.toggle').addClass('btn-sucess');
                            $('#status'+poll_id).parent('.toggle').removeClass('btn-danger off');
                        }
                        if(data.error){
                            $(".alert-danger").show();
                            $('.alert-danger').html(data.error).fadeIn('slow');
                            $('.alert-danger').delay(3000).fadeOut('slow');
                            setStatus(status, poll_id);
                        }
                    },
                    error: function(e){

                    }
                });
            }else{
                setStatus(status, poll_id);
            }
        });
    }
    function setStatus(status, poll_id){
        if(status == 1){
            $('#status'+poll_id).parent('.toggle').removeClass('btn-danger off');
            $('#status'+poll_id).parent('.toggle').addClass('btn-sucess');
        }else{
            $('#status'+poll_id).parent('.toggle').addClass('btn-danger off');
            $('#status'+poll_id).parent('.toggle').removeClass('btn-sucess');
        }
    }
</script>
@endsection
