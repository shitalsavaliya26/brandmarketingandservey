@extends('layouts.backend.subscriber.main')
@section('title', 'Add a Notification Topic')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('backend/css/plugins/dropzone/dropzone.css')}}">
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
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('notification-topic.index',request()->route('brandId'))}}"class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Notification Topics</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Add a Notification Topic</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add a Notification Topic in {{ $brand->name }} ({{ $brand->country->name }}) </h4>
                {!! Form::open(['route' => ['notification-topic.store',request()->route('brandId')],'autocomplete'=>'false','files'=>true,'id'=>'notification-topic-form','method'=>'post']) !!}
                @include('backend.subscriber.notification-topic.form')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script type="text/javascript" src="{{asset('backend/js/plugins/dropzone/dropzone.js')}}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    $(document).ready(function(){
        var dropzone_image_id = 0;
        $("#productDropZonenew").dropzone({
            autoQueue: false,
            maxFilesize: 2,
            acceptedFiles: "jpeg,.jpg,.png,.gif",
            uploadMultiple: false,
            parallelUploads: 5,
            paramName: "file",
            addRemoveLinks: true,
            dictFileTooBig: 'File is larger than 2MB',
            timeout: 10000,
            init: function() {
                this.on("success", function(file, responseText) {

                });
                this.on("removedfile", function(file) {
                    $(".remove_image_" + file.name.replace(/[\. ,:-]+/g, "_").replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '_')).first().remove();
                });
                this.on("addedfile", function(file) {
                   
                    if(file.size > 2036889)
                    {
                        this.removeFile(file);
                    }
                   else{
                    var _this=this,
                    reader = new FileReader();
                    reader.onload = function(event) {
                        base64 = event.target.result;
                        _this.processQueue();
                        var hidden_field = "<input hidden type='text' class='remove_image_"+ file.name.replace(/[\. ,:-]+/g, "_").replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '_') + "' name='form[file][" + dropzone_image_id + "]' value=" + base64 + ">";
                        var image = "<img  name='" + file.name + "' src='" + base64 + "' height=100>"
                        $("#image_data").append(hidden_field);

                        dropzone_image_id++;
                    };
                    reader.readAsDataURL(file);
                 }
                });
                this.on("error", function(file, response) {
                    toastr.error(response);
                    this.removeFile(file);
                    // Dropzone.forElement('#productDropZonenew').removeAllFiles(true);
                    
                  
                });
            },
            accept: function (file, done) {

                done();
            }
        });

        $("input[name=attachment_type]").on("change", function() {
            var type = $(this).val();
            if(type == 'image'){
                $(".attachment-pdf").hide();
                $(".attachment-image").show();
                $(".attachment-link").hide();
            }else if(type == 'pdf'){
                $(".attachment-pdf").show();
                $(".attachment-image").hide();
                $(".attachment-link").hide();
            }else if($('input[name="attachment_type"]:checked').val() == 'link'){
               $(".attachment-pdf").hide();
               $(".attachment-image").hide();
               $(".attachment-link").show();
            }
       });

        if($('input[name="attachment_type"]:checked').val() == 'image'){
            $(".attachment-pdf").hide();
            $(".attachment-image").show();
            $(".attachment-link").hide();
        }else if($('input[name="attachment_type"]:checked').val() == 'pdf'){
           $(".attachment-pdf").show();
           $(".attachment-image").hide();
           $(".attachment-link").hide();
        }else if($('input[name="attachment_type"]:checked').val() == 'link'){
           $(".attachment-pdf").hide();
           $(".attachment-image").hide();
           $(".attachment-link").show();
        }else{
            $(".attachment-pdf").hide();
            $(".attachment-image").hide();
            $(".attachment-link").hide();
        }
    });
    function removeimg(id)
    {
        $('#'+id).css("display","none");
        var imgs = $('#removeimg').val();
        if(imgs != '')
        {
            imgs = imgs+','+id;
            $('#removeimg').val(imgs);
        }else
        {
            $('#removeimg').val(id);
        }
    }
</script>
@endsection
