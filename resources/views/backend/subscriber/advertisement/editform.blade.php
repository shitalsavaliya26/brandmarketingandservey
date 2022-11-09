<div class="row mb-3">
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-5">
                <div class="m-dropzone dropzone m-dropzone--primary double-border"  id="productDropZonenew" action="/" method="post">
                    <div class="m-dropzone__msg dz-message needsclick" >
                        <h3 class="m-dropzone__msg-title">Drop photo or pdf here</h3>
                        <span class="m-dropzone__msg-desc">Or click to choose a photo or pdf</span>
                    </div>
                    <div id="image_data"></div>
                    <div id="image-holder"></div>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <p class="text-danger">You can upload only 1MB Image/Pdf</p>
        </div>
        <div class="form-group">
            <div id="image_preview"></div>
        </div>
        <div class="form-group mt-5">
            @if(@$advertisement)
                @foreach($advertisement as $image)

                    @if ($image->type == "1")
                    <span id="{{ $image->id }}" class="position-relative">
                        <img src="{{$image->image }}"  width="100" height="100" style="margin-left: 21px; margin-bottom: 10px; "/>
                        <a onclick="removeimg({{ $image->id }})"  m-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" aria-describedby="tooltip_xr8lyasjaw" style="position: absolute; top:-57px; right:-7px; cursor: pointer; color: red;text-decoration: none;" >×</a>
                    </span>
                    @else
                    <span id="{{ $image->id }}" class="position-relative">
                        <a href="{{$image->image }}" target="_blank"><img src="{{ asset('/backend/images/pdfimg.png') }}"  width="100" height="100" style="margin-left: 21px; margin-bottom: 10px; "/><a>
                        <a onclick="removeimg({{ $image->id }})"  m-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" aria-describedby="tooltip_xr8lyasjaw" style="position: absolute; top:-57px; right:-7px; cursor: pointer; color: red;text-decoration: none;" >×</a>
                    </span>
                    @endif
                    
                @endforeach
                <input type="hidden" name="remove_img" id="removeimg">
            @endif
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('brand.index')}}">Cancel</a>


