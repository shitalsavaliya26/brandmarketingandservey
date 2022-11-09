<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Attachment Type</label>
            <div class="col-sm-3">
                <div class="form-check">
                    <label class="form-check-label">
                        {!! Form::radio('attachment_type', 'link',@$win->attachment_type!='link'?true:false,[],['class'=>'form-control attachment_type']) !!} 
                        Link
                    </label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-check">
                    <label class="form-check-label">
                        {!! Form::radio('attachment_type', 'image',@$win->attachment_type=='image'?true:false,[],['class'=>'form-control attachment_type']) !!}
                        Image
                    </label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-check">
                    <label class="form-check-label">
                        {!! Form::radio('attachment_type', 'pdf',@$win->attachment_type=='pdf'?true:false,[],['class'=>'form-control attachment_type']) !!}
                        PDF
                    </label>
                </div>
            </div>
            <span class="help-block text-danger">{{ $errors->first('attachment_type') }}</span>
        </div>
    </div>
    <div class="col-md-6 link">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Attachment</label>
            <div class="col-sm-9 attachment">
                {!! Form::text('attachment',old('attachment'),['class'=>'form-control','placeholder'=>'Enter Link']) !!}
                <span class="help-block text-danger">{{ $errors->first('attachment') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6 image">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Attachment</label>
            <div class="col-sm-9">
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
            @if(isset($win) && $win->images!="")
                @foreach($win->images as $image)
                    <span id="{{ $image->id }}" >
                        <img src="{{$image->image }}"  width="100" height="100" style="margin-left: 21px; margin-bottom: 10px "/>
                        <a onclick="removeimg({{ $image->id }})"  m-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" aria-describedby="tooltip_xr8lyasjaw" style="position: absolute; color: red;text-decoration: none;" >×</a>
                    </span>
                @endforeach
                <input type="hidden" name="remove_img" id="removeimg">
            @endif
        </div>
    </div>
    <div class="col-md-6 pdf">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">PDF</label>
            @if(isset($win) && $win->type=="pdf")
                <div class="col-sm-9">
                    {!! Form::file('pdf',['class'=>'form-control','placeholder'=>'Enter pdf']) !!}
                    <span class="help-block text-danger">{{ $errors->first('pdf') }}</span>
                </div>
                <div class="col-sm-6" >
                    <a href="#" target="_blank">
                        <img class="img-thumbnail img-lg mb-2" src="{{$win->image}}" height="100" width="auto" style="max-width: 100%" />
                    </a>
                </div>
            @else
            <div class="col-sm-9">
                {!! Form::file('pdf',['class'=>'form-control','placeholder'=>'Enter pdf']) !!}
                <span class="help-block text-danger">{{ $errors->first('pdf') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('win.index', [request()->route('brandId')])}}">Cancel</a>
