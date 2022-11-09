@extends('layouts.backend.subscriber.main')
@section('title', 'Message')
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
                        @php
                            $ids = App\Helpers\CustomHelper::getEncrypted($topic->brand_id)
                        @endphp
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('topic.index',[$ids])}}" class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand ->name }} ({{ $brand->country->name }}) Topics</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Message History</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $brand ->name }} ({{ $brand->country->name }}) Message History</h4>
                @if(isset($messages) && count($messages) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th width="20%" class="text-left">User</th>
                                <th width="20%">Message</th>
                                <th width="20%">Attachments</th>
                                <th width="20%">Date</th>
                                <th width="20%">Reply</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($messages as $row)
                                <tr class="text-center">
                                    <td class="text-left">{{@$row->user->firstname}} {{@$row->user->lastname}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#messageModal" data-val="{{implode(',', @$row->message)}}">
                                            View Message
                                        </button>
                                    </td>
                                    <td>
                                        @php
                                            if(isset($row['attachments']) ){
                                                $list = [];
                                                $audio = [];
                                                foreach ($row['attachments'] as $document) {
                                                    $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'JPG', 'JPEG', 'GIF', 'PNG', 'BMP', 'SVG', 'SVGZ', 'CGM', 'DJV', 'DJVU', 'ICO', 'IEF','JPE', 'PBM', 'PGM', 'PNM', 'PPM', 'RAS', 'RGB', 'TIF', 'TIFF', 'WBMP', 'XBM', 'XPM', 'XWD'];
                                                    $audioExtensions = ['mp3', 'MP3'];
                                                    $explodeDocument = explode('.', $document->attachment);
                                                    $extension = end($explodeDocument);
                                                    if(in_array($extension, $imageExtensions)){
                                                        @$list[] = $document['attachment'];
                                                    }

                                                    if(in_array($extension, $audioExtensions)){
                                                        @$audio[] = $document['attachment'];
                                                    }
                                                }
                                            }
                                            $string_version = (isset($list) && count($list) > 0) ? implode(',', $list) :'';
                                            $string_version_audio = (isset($audio) && count($audio) > 0) ? implode(',', $audio) :'';
                                        @endphp
                                        @if(($string_version != '' && $string_version != null) ||( $string_version_audio != '' && $string_version_audio != null))
                                            <a href="{{route('topic.view-attachment',[App\Helpers\CustomHelper::getEncrypted($row->id)])}}" class="btn btn-primary" target="_blank">
                                                View Attachments
                                            </a>
                                        @elseif(count($row['attachments']) > 0)
                                            <a href="{{$row['attachments'][0]['attachment']}}" class="btn btn-primary" target="_blank">
                                                View Attachments
                                            </a>
                                        @else
                                            <a href="javascript:void(0);" class="btn btn-light">
                                                Attachments not found!
                                            </button>
                                        @endif
                                    </td>
                                    <td> {{ \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i:s A')}}</td>
                                    <td>{{($row->reply != null) ? implode(",",$row->reply) : '' }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5">{{ $messages->render('vendor.default_paginate') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>No message history found</h6>
                @endif

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Message Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachmentModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Message Detail</h5>
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

<script>
    $('#messageModal').on('show.bs.modal', function (event) {
        let string = $(event.relatedTarget).data('val');
        let array = string.split(',');

        $(".modal-body").empty();
        $.each(array, function(index, value) {
            $(".modal-body").append("<li>" + value + "</li>");
        });
    });

    function imgError(image) {
        image.onerror = "";
        image.src = "/backend/images/no-found.png";
        return true;
    }
    $('#attachmentModal').on('show.bs.modal', function (event) {
        let string = $(event.relatedTarget).data('val');
        let array = string.split(',');
        $(".news-slider").slick('unslick');
        $(".news-slider").empty();
        $.each(array, function(index, value) {
            $(".news-slider").append("<div class='row border-gray cus-card rounded m-0 mt-2'><div class='col-12'><img src="+ value +" class='img-fluid' onerror='imgError(this);' style=margin:auto;></div></div>");
        });

        $('.news-slider').slick({
            infinite: true,
            autoplay: true,
            dots: true,
            arrows: false,
        });
    });
</script>
@endsection
