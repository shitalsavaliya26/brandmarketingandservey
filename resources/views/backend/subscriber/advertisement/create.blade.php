@extends('layouts.backend.subscriber.main')
@section('title', 'Add an Advertisement')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('backend/css/plugins/dropzone/dropzone.css')}}">
@endsection
@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add an Advertisement</h4>
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
            maxFilesize: 1,
            acceptedFiles: "jpeg,.jpg,.png,.gif",
            uploadMultiple: false,
            parallelUploads: 5,
            paramName: "file",
            addRemoveLinks: true,
            dictFileTooBig: 'File is larger than 1MB',
            timeout: 10000,
            init: function() {
                this.on("success", function(file, responseText) {      

                });
                this.on("removedfile", function(file) {
                    $(".remove_image_" + file.name.replace(/[\. ,:-]+/g, "_").replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '_')).first().remove();
                });
                this.on("addedfile", function(file) {

                    if(file.size > 1018444.5)
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
                        this.removeFile(file);
                        // toastr.options = {
                        //     "preventDuplicates": true,
                        //     "preventOpenDuplicates": true,
                        //     closeButton: true,
                        //     extendedTimeOut: 0,
                        //     timeOut: 0,
                        //     tapToDismiss: false
                        // };
                        toastr.error(response);
                        // Dropzone.forElement('#productDropZonenew').removeAllFiles(true)
                });
            },
            accept: function (file, done) {

                done();
            }
        });
    });         
</script>
@endsection