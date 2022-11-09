@extends('layouts.backend.subscriber.main')
@section('title', 'Polls')
@section('css')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection
@section('content')
@php $bid= App\Helpers\CustomHelper::getEncrypted($mainpoll->brand_id) @endphp
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('polltitle.index',[$bid])}}"class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }})
                            Polls</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $mainpoll['title'] ?? "" }} Questions</h5></a></div>
                    
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
                    <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) {{ $mainpoll['title'] ?? "" }} Questions </h4>
                    <h4 class="card-title text-right">
                        @php 
                        $mainpollid = App\Helpers\CustomHelper::getEncrypted($mainpoll['id']);
                        $brand_id = App\Helpers\CustomHelper::getEncrypted($mainpoll['brand_id']);
                        @endphp

                        @if ($userpollcount > 0)
                        <p class="text-danger">You can't add more questions because user have already submitted this poll.</p>
                        @else
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('poll.create', [$mainpollid])}}">Add Question</a>
                        @endif
                        <a class="mx-3 btn btn-warning btn-sm btn-xs-block" href="{{route('polltitle.index', [$brand_id])}}"><i class="ti-arrow-left menu-icon"></i> Back</a>
                    </h4>
                </div>
               
                <div class="d-flex align-items-center justify-content-between">
                    <form class="form-inline" method="post" action="{{ route('polltotal.quantity') }}" id="total_quantity_form">
                        @csrf
                        <input type="hidden" id="poll_title_id" name="poll_title_id" value="{{ $mainpollid }}">
                        <div class="form-group mx-sm-3 mb-2">
                        <label class="mx-3">Set Target</label>
                          <input type="text" class="form-control" min="0" id="total_quantity" name="total_quantity" placeholder="Set Target" value="{{ $mainpoll['total_quantity'] }}">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Save</button>
                    </form>
                </div>
               
                @if(isset($polls) && count($polls) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th width="20%" class="text-left">Question</th>
                                {{-- <th width="20%">Status</th> --}}
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody class="text-center">
                            @foreach($polls as $row)
                                <tr>
                                    <td class="text-left">{{@$row->question}}</td>
                                    {{-- <td>
                                        <input name="status" id="status{{$row->id}}" onChange="changeStatus({{$row->id}}, {{$row->status}});" data-id="{{$row->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $row->status== '1' ? 'checked' : '' }}>
                                    </td> --}}
                                    <td>
                                        @php 
                                        $id= App\Helpers\CustomHelper::getEncrypted($row->id);
                                        $mainpollid = App\Helpers\CustomHelper::getEncrypted($mainpoll['id']);
                                        @endphp
                                        {!! Form::open(['route' => ['poll.destroy',$row->id], 'class'=>'d-flex align-items-center justify-content-center', 'onsubmit'=>"return confirmDelete(this,'Are you sure to want delete ?');",'id'=>'formPollDel'.$row->id]) !!}

                                            @if ($userpollcount > 0)
                                            <a class="ml-3 text-decoration-none icon-red"  title="Update Poll"> 
                                                <i class="ti-pencil icon"></i>
                                            </a>
                                            <a class="ml-3 text-decoration-none icon-red" title="Delete Poll"> 
                                                <i class="ti-trash icon"></i>
                                            </a>
                                            @else
                                            <a class="ml-3 text-decoration-none"  href="{{route('poll.edit',[$id, request()->route('brandId')])}}" title="Update Poll"> 
                                                <i class="ti-pencil icon"></i>
                                            </a>
                                            @method('delete')
                                            <a class="ml-3 text-decoration-none"  type="submit" onclick="$('#formPollDel{{$row->id}}').submit()" title="Delete Poll"> 
                                                <i class="ti-trash icon"></i>
                                            </a>
                                            @endif
                                            <a class="ml-3 text-decoration-none"  href="{{route('poll.history',[$id])}}" title="Poll History"> 
                                                <i class="ti-list icon"></i>
                                            </a>
                                            <a class="ml-3 text-decoration-none"  href="{{route('poll.result',[$id])}}" title="Poll Result"> 
                                                <i class="ti-stats-up icon"></i>
                                            </a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5">{{ $polls->render('vendor.default_paginate') }}</td>
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
$("#total_quantity_form").validate({
    rules: {
        total_quantity: {
            digits: true
        }
    },
    messages: {
        total_quantity: {
            // required: "Please enter total quantity.",
        }
    },
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
