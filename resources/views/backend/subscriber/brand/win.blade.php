@extends('layouts.backend.subscriber.main')
@section('title', 'Win')
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
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.create')}}" class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Win</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) Win</h4>
                {!! Form::open(['route' =>
                ['win.store',request()->route('brandId')],'autocomplete'=>'false','files'=>true,'id'=>'win-form','method'=>'post'])
                !!}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="d-block">Attachment Type</label>
                                <div class="form-check d-inline-block">
                                    <label class="form-check-label">
                                        {!! Form::radio('attachment_type',
                                        'image',@$win->attachment_type=='image'?true:false,[],['class'=>'form-control
                                        attachment_type']) !!}
                                        Images
                                    </label>
                                </div>
                                <div class="form-check d-inline-block ml-3">
                                    <label class="form-check-label">
                                        {!! Form::radio('attachment_type',
                                        'pdf',@$win->attachment_type=='pdf'?true:false,[],['class'=>'form-control
                                        attachment_type']) !!}
                                        PDF
                                    </label>
                                </div>
                                <div class="form-check d-inline-block ml-3">
                                    <label class="form-check-label">
                                        {!! Form::radio('attachment_type', 
                                        'link',@$win->attachment_type=='link'?true:false,[],['class'=>'form-control attachment_type']) !!}
                                        Link
                                    </label>
                                </div>
                            </div>
                        </div>
                        <span class="help-block text-danger">{{ $errors->first('attachment_type') }}</span>
                    </div>
                    <div class="col-12 col-md-10 col-xl-6 link">
                        <div class="form-group row">
                            <div class="col-12 attachment">
                            <label class=""></label>
                                @if(isset($win))
                                <input type="text" class="form-control" placeholder="Enter Link" id="attachment"
                                    name="attachment" value="{{@$win->attachment_type=='link' ? $win->attachment: ''}}">
                                @else
                                <input type="text" class="form-control" placeholder="Enter Link" id="attachment"
                                    name="attachment">
                                @endif
                                <span class="help-block text-danger">{{ $errors->first('attachment') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-10 col-xl-6 image">
                        <div class="form-group row">
                            <div class="col-10">
                                <label class=""></label>
                                <div class="m-dropzone dropzone m-dropzone--primary double-border"  id="productDropZonenew" action="/" method="post">
                                    <div class="m-dropzone__msg dz-message needsclick" >
                                        <h3 class="m-dropzone__msg-title">Drop photo here</h3>
                                        <span class="m-dropzone__msg-desc">Or click to choose a photo</span>
                                    </div>
                                    <div id="image_data"></div>
                                    <div id="image-holder"></div>
                                </div>
                                <span class="help-block text-danger">{{ $errors->first('form') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div id="image_preview"></div>
                        </div>
                        <div class="form-group">
                            @if(isset($win) && @$win->images!="")
                            @foreach($win->images as $image)
                            <span id="{{ $image->id }}">
                                <img src="{{$image->image }}" width="100" height="100" style="margin-left: 21px; margin-bottom: 10px; " />
                                <a onclick="removeimg({{ $image->id }})" m-portlet-tool="remove"
                                    class="m-portlet__nav-link m-portlet__nav-link--icon"
                                    aria-describedby="tooltip_xr8lyasjaw"
                                    style="top: 177px;position: absolute; color: red;text-decoration: none;">×</a>
                            </span>
                            @endforeach
                            <input type="hidden" name="remove_img" id="removeimg">
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-10 col-xl-6 pdf">
                        <div class="form-group row">
                            @if(isset($win) && @$win->attachment_type=="pdf")
                            <div class="col-12">
                                <label class=""></label>
                                {!! Form::file('pdf',['class'=>'form-control','placeholder'=>'Enter pdf']) !!}
                                <span class="help-block text-danger">{{ $errors->first('pdf') }}</span>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{asset('uploads/win/'.$win->slug . '.pdf')}}" target="_blank">
                                    View Attachments
                                </a>
                            </div>
                            @else
                            <div class="col-12">
                                <label class=""></label>
                                {!! Form::file('pdf',['class'=>'form-control','placeholder'=>'Enter pdf']) !!}
                                <span class="help-block text-danger">{{ $errors->first('pdf') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mr-2" name="save" value="save">Save</button>
                @if ($countrycount > 1)
                    <button type="submit" class="btn btn-primary mr-2" name="saveall" value="saveall">Save For All Countries</button>
                @endif
                <a class="btn btn-light" href="{{route('brand.index')}}">Cancel</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script type="text/javascript" src="{{asset('backend/js/plugins/dropzone/dropzone.js')}}"></script>
<script>
    Dropzone.autoDiscover = false;
    $(document).ready(function () {
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
            init: function () {
                this.on("success", function (file, responseText) {

                });
                this.on("removedfile", function (file) {
                    $(".remove_image_" + file.name.replace(/[\. ,:-]+/g, "_").replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '_')).first().remove();
                });
                this.on("addedfile", function (file) {
                    var _this = this,
                        reader = new FileReader();
                    reader.onload = function (event) {
                        base64 = event.target.result;
                        _this.processQueue();
                        var hidden_field = "<input hidden type='text' class='remove_image_" + file.name.replace(/[\. ,:-]+/g, "_").replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '_') + "' name='form[file][" + dropzone_image_id + "]' value=" + base64 + ">";
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
        $('input[type=radio][name=attachment_type]').change(function () {
            if (this.value == 'link') {
                $(".link").show();
                $(".image").hide();
                $(".pdf").hide();
            }
            else if (this.value == 'image') {
                $(".image").show();
                $(".link").hide();
                $(".pdf").hide();
            }
            else if (this.value == 'pdf') {
                $(".pdf").show();
                $(".image").hide();
                $(".link").hide();
            }

        });
        if ($('input[name="attachment_type"]:checked').val() == 'link') {
            $(".link").show();
            $(".image").hide();
            $(".pdf").hide();
        } else if ($('input[name="attachment_type"]:checked').val() == 'image') {
            $(".image").show();
            $(".link").hide();
            $(".pdf").hide();
        } else if ($('input[name="attachment_type"]:checked').val() == 'pdf') {
            $(".pdf").show();
            $(".image").hide();
            $(".link").hide();
        }else{
            $(".pdf").hide();
            $(".image").hide();
            $(".link").hide();
        }
    });

    function removeimg(id) {
        $('#' + id).css("display", "none");
        var imgs = $('#removeimg').val();
        if (imgs != '') {
            imgs = imgs + ',' + id;
            $('#removeimg').val(imgs);
        } else {
            $('#removeimg').val(id);
        }
    }
</script>
@endsection