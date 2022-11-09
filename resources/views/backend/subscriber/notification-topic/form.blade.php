<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Topic Name</label>
                {!! Form::text('name',old('name'),['class'=>'form-control capital-input','placeholder'=>'Enter name']) !!}
                <span class="help-block text-danger">{{ $errors->first('name') }}</span>
            </div>
        </div>
    </div>
    <!-- <div class="col-md-6">
        <div class="form-group row">
            @if(isset($notificationTopic) && $notificationTopic->image!="")
                <div class="col-12">
                    <label class="">Topic Logo</label>
                    {!! Form::file('image',['class'=>'form-control','placeholder'=>'Enter Image']) !!}
                    <span class="help-block text-danger">{{ $errors->first('image') }}</span>
                </div>
                <div class="col-12 mt-2" >
                    <a href="{{$notificationTopic->image}}" target="_blank">
                        <img class="img-thumbnail img-lg mb-2" src="{{$notificationTopic->image}}" height="100" width="auto" style="max-width: 100%" />
                    </a>
                </div>
            @else
            <div class="col-12">
                <label class="">Topic Logo</label>
                {!! Form::file('image',['class'=>'form-control','placeholder'=>'Enter image']) !!}
                <span class="help-block text-danger">{{ $errors->first('image') }}</span>
            </div>
            @endif
        </div>
    </div> -->
</div>
<div class="row mt-md-3">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="d-block">Attachment Type</label>
                <div class="form-check d-inline-block">
                    <label class="form-check-label">

                        {!! Form::radio('attachment_type', 'image',@$brand->attachment_type=='image'?true:false,[],['class'=>'form-control']) !!}
                        Images
                    </label>
                </div>
                <div class="form-check d-inline-block ml-3">
                    <label class="form-check-label">
                        {!! Form::radio('attachment_type', 'pdf',@$brand->attachment_type=='pdf'?true:false,[],['class'=>'form-control']) !!}
                        PDF
                    </label>
                </div>
                <div class="form-check d-inline-block ml-3">
                    <label class="form-check-label">
                        {!! Form::radio('attachment_type', 'link',@$brand->attachment_type=='link'?true:false,[],['class'=>'form-control']) !!}
                        Link
                    </label>
                </div>
            </div>
        </div>
            <label id="attachment_type-error" class="error" for="attachment_type"></label>
            <span class="help-block text-danger">{{ $errors->first('attachment_type') }}</span>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-10 col-xl-6 attachment-image mb-2">
        <div class="form-group row">
            <div class="col-10">
                <div class="m-dropzone dropzone m-dropzone--primary double-border"  id="productDropZonenew" action="/" method="post">
                    <div class="m-dropzone__msg dz-message needsclick" >
                        <h3 class="m-dropzone__msg-title">Drop photo here</h3>
                        <span class="m-dropzone__msg-desc">Or click to choose a photo</span>
                    </div>
                    <div id="image_data"></div>
                    <div id="image-holder"></div>
                </div>
                <span class="help-block text-danger">{{ $errors->first('form') }}</span>
                <label id="file-error" class="error" for="file"></label>
            </div>
        </div>
        <div class="form-group">
            <p class="text-danger">You can upload only 2MB Image.</p>
        </div>
        <div class="form-group">
            <div id="image_preview"></div>
        </div>
        <div class="form-group">
            @if(isset($notificationTopic) && $notificationTopic->images!="" && $notificationTopic->attachment_type == 'image')
                @foreach($notificationTopic->images as $image)
                    <span id="{{ $image->id }}" class="position-relative">
                        <img src="{{$image->image }}"  width="100" height="100" style="margin-left: 21px; margin-bottom: 10px;"/>
                        <a onclick="removeimg({{ $image->id }})"  m-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" aria-describedby="tooltip_xr8lyasjaw" style="position: absolute; top:-57px; right:-7px; cursor: pointer; color: red;text-decoration: none;" >×</a>
                    </span>
                @endforeach
                <input type="hidden" name="remove_img" id="removeimg">
            @endif
        </div>
    </div>
    <div class="col-12 col-md-10 col-xl-6 attachment-pdf mb-2">
        <div class="form-group row">
            @if(isset($notificationTopic) && $notificationTopic->attachment_type=="pdf")
                <div class="col-12">
                    {!! Form::file('pdf',['class'=>'form-control','placeholder'=>'Enter pdf']) !!}
                    <span class="help-block text-danger">{{ $errors->first('pdf') }}</span>
                </div>
                <div class="col-12" >
                    <a href="{{$notificationTopic->share_link}}" target="_blank">
                        View PDF
                    </a>
                </div>
            @else
            <div class="col-12">
                {!! Form::file('pdf',['class'=>'form-control','placeholder'=>'Enter pdf']) !!}
                <span class="help-block text-danger">{{ $errors->first('pdf') }}</span>
            </div>
            @endif
        </div>
        <div class="form-group">
            <p class="text-danger">You can upload only 2MB PDF.</p>
        </div>
    </div>
    <div class="col-12 col-md-10 col-xl-6 attachment-link mb-2">
        <div class="form-group row">
            @if(isset($notificationTopic) && $notificationTopic->attachment_type=="link")
                <div class="col-12">
                    {!! Form::text('link',$notificationTopic->images[0]->image,['class'=>'form-control','placeholder'=>'Enter link']) !!}
                    <span class="help-block text-danger">{{ $errors->first('link') }}</span>
                </div>
            @else
            <div class="col-12">
                {!! Form::text('link','',['class'=>'form-control','placeholder'=>'Enter link']) !!}
                <span class="help-block text-danger">{{ $errors->first('link') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary mr-2" name="save" value="save">Save</button>
@if ($countrycount > 1)
    <button type="submit" class="btn btn-primary mr-2" name="saveall" value="saveall">Save For All Countries</button>
@endif
<a class="btn btn-light" href="{{route('notification-topic.index', [request()->route('brandId')])}}">Cancel</a>
