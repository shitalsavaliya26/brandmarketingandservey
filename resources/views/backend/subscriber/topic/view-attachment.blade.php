@extends('layouts.backend.subscriber.main')
@section('title', 'Attachment')
@section('css')
    <link href="{{asset('backend/css/plugins/blueimp/css/blueimp-gallery.min.css')}}" rel="stylesheet">
@endsection
@section('content')

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Attachment</h4>
                @if(isset($attachments) && isset($image) && count($attachments) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            @foreach($attachments as $image)
                                <!-- <span>
                                    <img src="{{ $image->attachment}}"  width="100" height="100" style="margin-left: 21px;"/>
                                </span> -->
                                <a class="blueimp-link" href="{{$image->attachment}}" data-gallery="" target="_blank" title='{{$image->name}}'>
                                    <img onerror="this.src='{{asset('backend/images/no-found.png')}}'" src="{{$image->attachment}}" style="margin-left: 21px; margin-bottom: 10px; max-width: 200px" height="100"  width="100" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @elseif(isset($attachments) && isset($audio) && count($attachments) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            @foreach($attachments as $audio)
                                <audio controls>
                                    <source src="{{$audio->attachment}}" type="audio/mpeg">
                                </audio>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                    <h6>No attachment Found!</h6>
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
    $("audio").on("play", function() {
    var id = $(this).attr('id');

    $("audio").not(this).each(function(index, audio) {
        audio.pause();
    });
});

</script>
@endsection
