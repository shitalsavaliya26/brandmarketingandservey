@extends('layouts.backend.subscriber.main')
@section('title', 'Topics')
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
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Topics</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">{{ $brand ->name }} ({{ $brand->country->name }}) Topics</h4>
                    <h4 class="card-title text-right">
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('topic.create', [request()->route('brandId')])}}">Add Topic</a>
                    </h4>   
                </div>
                @if(isset($topics) && count($topics) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th width="25%" class="text-left">Name</th>
                                <th width="25%">Number of submissions</th>
                                <th width="25%">Status</th>
                                <th width="25%">Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($topics as $row)
                                <tr class="text-center">
                                    <td class="text-left">{{@$row->name}}</td>
                                    <td>{{count(@$row->messages)}}</td>
                                    <td>
                                        <input name="status" id="status{{$row->id}}" onChange="changeStatus({{$row->id}}, {{$row->status}});" data-id="{{$row->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $row->status== '1' ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                                        {!! Form::open(['route' => ['topic.destroy',$row->id], 'class'=>'d-flex align-items-center justify-content-center', 'onsubmit'=>"return confirmDelete(this,'Are you sure to want delete ?')",'id'=>'formTopicDel'.$row->id]) !!}
                                            <a class="text-decoration-none"  href="{{route('topic.edit',[$id, request()->route('brandId')])}}" title="Update Topic">
                                                <i class="ti-pencil icon"></i>
                                            </a>
                                            @method('delete')
                                            <a class="ml-3 text-decoration-none"  type="submit" onclick="$('#formTopicDel{{$row->id}}').submit()" title="Delete Topic">
                                                <i class="ti-trash icon"></i>
                                            </a>
                                            <a class="ml-3 text-decoration-none"  href="{{route('topic.message-history',[$id])}}" title="Message History">
                                                <i class="ti-list icon"></i>
                                            </a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4">{{ $topics->render('vendor.default_paginate') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>Add a topic</h6>
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
    function changeStatus(topic_id, status,reuse = null){
        swal({
              title: `Are you sure you want to change the status?`,
              icon: "warning",
              buttons: true,
              dangerMode: true,
          })
          .then((change) => {
            if (change) {
                var url = "{{ route('topic.change-status') }}";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "status": status,
                        "topic_id": topic_id,
                    },
                    success: function(data){
                        $(".alert-success").show();
                        $('.alert-success').html(data.success).fadeIn('slow');
                        $('.alert-success').delay(3000).fadeOut('slow');
                    }
                });
            }else{
                if(status == 1){
                    $('#status'+topic_id).parent('.toggle').removeClass('btn-danger off');
                    $('#status'+topic_id).parent('.toggle').addClass('btn-sucess');
                }else{
                    $('#status'+topic_id).parent('.toggle').addClass('btn-danger off');
                    $('#status'+topic_id).parent('.toggle').removeClass('btn-sucess');
                }

            }
        });
    }
</script>

@endsection
