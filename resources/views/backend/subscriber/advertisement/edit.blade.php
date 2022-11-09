@extends('layouts.backend.subscriber.main')
@section('title', 'Advertisement')
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
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Advertisement</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) Advertisement</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($brandId); @endphp
                {!! Form::model($advertisement,['route' => ['advertisement.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'advertisement-form-edit']) !!}
                    @method('patch')
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
            acceptedFiles: "jpeg,.jpg,.png,.gif,.pdf",
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
                    toastr.error(response);
                    this.removeFile(file);
                       
                        // Dropzone.forElement('#productDropZonenew').removeAllFiles(true)
                });
            },
            accept: function (file, done) {

                done();
            }
        });
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
