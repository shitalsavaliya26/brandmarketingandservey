@extends('layouts.backend.subscriber.main')
@section('title', 'Notification Topics')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Notification Topics</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <!-- <div class="alert alert-success" role="alert">

                </div> -->
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) Notification Topics</h4>
                    <h4 class="card-title text-right">
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('notification-topic.create', [request()->route('brandId')])}}">Add Notification Topic</a>
                    </h4>
                </div>
                @if(isset($notificationTopics) && count($notificationTopics) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th class="text-left">Name</th>
                                <th>Subscribers</th>
                                <th>Distribution Cost</th>
                                <th>Subscription Cost</th>
                                <th>Image</th>
                                <th>Attachments</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($notificationTopics as $row)
                            <tr class="text-center">
                                <td class="text-left">{{@$row->name}}</td>
                                <td>{{@$row->subscribers->count()}}</td>
                                {{-- <td>${{number_format((float)$row->subscribers->count() * $price['message_distribution'], 3, '.', '')}}</td>
                                <td>${{number_format((float)$price['notification_topic_add'], 3, '.', '')}}</td> --}}
                                <td> {{ $symbol }} {{ (number_format((float)$row->subscribers->count() * $price['message_distribution']*$currencyrate, 3, '.', ''))}}</td>

                                
                                <td> {{ $symbol }} {{(number_format($price['notification_topic_add']*$currencyrate,3, '.', ''))}}</td>

                                <td>
                                    <a class="blueimp-link" href="{{$row->image}}" data-gallery="" target="_blank">
                                        <img onerror="this.src='{{asset('backend/images/no-found.png')}}'" src="{{$row->image}}" width="auto" height="50px">
                                    </a>
                                </td>
                                <td>
                                    @if($row->attachment_type == 'image')
                                        <?php
                                            $list = [];
                                            foreach ($row['images'] as $image) {
                                                $list[] = $image['image'];
                                            }
                                            $string_version = (isset($list) && count($list) > 0) ? implode(',', $list) :'';
                                        ?>
                                        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#messageModal" data-val="{{@$string_version}}">
                                            View Attachments
                                        </button> -->
                                        <a href="{{route('notification-topic.view-images',[App\Helpers\CustomHelper::getEncrypted($row->id)])}}" class="btn btn-primary" target="_blank">
                                            View Attachments
                                        </a>
                                    @elseif($row->attachment_type == 'pdf')
                                        <a href="{{asset('uploads/notificationimage/'.$row->slug.'.pdf')}}" class="btn btn-primary" target="_blank">
                                            View Attachments
                                        </a>
                                    @elseif($row->attachment_type == 'link')
                                        <a href="{{$row->share_link}}" class="btn btn-primary" target="_blank">
                                            View Attachments
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                                    {!! Form::open(['route' => ['notification-topic.destroy',$row->id],'onsubmit'=>"return confirmDelete(this,'Are you sure to want delete ?')",'id'=>'formNotificationTopicDel'.$row->id]) !!}
                                    <a class="text-decoration-none"  href="{{route('notification-topic.edit',[$id, request()->route('brandId')])}}" title="Update Notification Topic">
                                        <i class="ti-pencil icon"></i>
                                    </a>
                                    @method('delete')
                                    <a class="ml-3 text-decoration-none"  type="submit" onclick="$('#formNotificationTopicDel{{$row->id}}').submit()" title="Delete Topic">
                                        <i class="ti-trash icon"></i>
                                    </a>
                                    <a class="ml-3 text-decoration-none"  href="{{route('notification-topic.history',[$id, request()->route('brandId')])}}" title="Notification Topic History">
                                        <i class="ti-eye icon"></i>
                                    </a>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="7">{{ $notificationTopics->render('vendor.default_paginate') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                <h6>Add a notification topic</h6>
                @endif

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Images</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="attachment-modal-body">
                <div class="news-slider">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
//    function sendNotification(id){

//         var url = "{{ route('notification-topic.user-notification', ":id") }}";
//         url = url.replace(':id', id);

//         $.ajax({
//             type: 'POST',
//             url: url,
//             dataType: "json",
//             success: function (data) {
//                 $(".alert-success").show();
//                 $('.alert-success').html(data.success).fadeIn('slow');
//                 $('.alert-success').delay(3000).fadeOut('slow');

//             },
//             error:function(result){

//             },
//         });
    // }

    // $(".alert-success").hide();
    // function imgError(image) {
    //     image.onerror = "";
    //     image.src = "/backend/images/no-found.png";
    //     return true;
    // }

    // $('#messageModal').on('show.bs.modal', function (event) {
    //     let string = $(event.relatedTarget).data('val');
    //     let array = string.split(',');
    //     $(".news-slider").slick('unslick');
    //     $(".news-slider").empty();
    //     $.each(array, function(index, value) {
    //         $(".news-slider").append("<div class='row border-gray cus-card rounded m-0 mt-2'><div class='col-12'><img src="+ value +" class='img-fluid' onerror='imgError(this);' style=margin:auto;></div></div>");
    //     });

    //     $('.news-slider').slick({
    //         infinite: true,
    //         autoplay: true,
    //         dots: true,
    //         arrows: false,
    //     });
    // });

</script>
@endsection
