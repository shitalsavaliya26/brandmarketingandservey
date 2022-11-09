@extends('layouts.backend.subscriber.main')
@section('title', 'Attachment')
@section('css')
    <link href="{{asset('backend/css/plugins/blueimp/css/blueimp-gallery.min.css')}}" rel="stylesheet">
@endsection
@section('content')
@php $bid= App\Helpers\CustomHelper::getEncrypted($notificationTopic->brand_id) @endphp
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('notification-topic.index',[$bid])}}"class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Notification Topics</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Attachment</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) Attachment</h4>
                @if(isset($attachments) && count($attachments) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            @foreach($attachments as $image)
                                <a class="blueimp-link" href="{{$image->image}}" data-gallery="" target="_blank" title='{{$image->name}}'>
                                    <img onerror="this.src='{{asset('backend/images/no-found.png')}}'" src="{{$image->image}}" width="100" height="100" style="max-width: 200px; margin-left: 21px; margin-bottom: 10px;"/>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                    <h6>No attachment found!</h6>
                @endif

            </div>
        </div>
    </div>
</div>
<div id="blueimp-gallery" class="blueimp-gallery">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <div class="description"></div>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <ol class="indicator"></ol>
</div>
@endsection

@section('script')
<script src="{{asset('backend/js/plugins/blueimp/jquery.blueimp-gallery.min.js')}}"></script>
<script type="text/javascript">
    var options = {
        container: document.getElementsByClassName('blueimp-gallery-div'),
        slidesContainer: 'div',
        titleElement: 'h3',
        indicatorContainer: 'ol'
    }
    blueimp.Gallery($('a.blueimp-link'),options );
</script>
@endsection

