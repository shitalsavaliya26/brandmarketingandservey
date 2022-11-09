@extends('layouts.backend.subscriber.main')
@section('title', 'Create Advertisement')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('backend/css/plugins/dropzone/dropzone.css')}}">
@endsection
@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Create Advertisement</h4>
                {!! Form::open(['route' => 'advertisement.store','autocomplete'=>'false','files'=>true,'id'=>'advertisement-form','method'=>'post']) !!}
                    @include('backend.subscriber.advertisement.form')
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
            maxFilesize: 20,
            acceptedFiles: "jpeg,.jpg,.png,.gif",
            uploadMultiple: false,
            parallelUploads: 5,
            paramName: "file",
            addRemoveLinks: true,
            dictFileTooBig: 'Image is larger than 20MB',
            timeout: 10000,
            init: function() {
                this.on("success", function(file, responseText) {      

                });
                this.on("removedfile", function(file) {
                    $(".remove_image_" + file.name.replace(/[\. ,:-]+/g, "_").replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '_')).first().remove();
                });
                this.on("addedfile", function(file) {
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
                });
            },
            accept: function (file, done) {

                done();
            }
        });
    });         
</script>
@endsection