<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Brand</label>
            <div class="col-sm-9">
                <select class="form-control" id="brand_id" name="brand_id">
                    <option value="null">--Select Brand--</option>
                    @foreach($brands as $brand)
                        <option value="{{$brand->id}}" {{isset($advertisement) ? ($advertisement->brand_id == $brand->id  ? 'selected' : '') : ''}}>{{$brand->name}}</option>
                    @endforeach
                </select>
                <span class="help-block text-danger">{{ $errors->first('name') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Images</label>
            <div class="col-sm-9">
                <div class="m-dropzone dropzone m-dropzone--primary"  id="productDropZonenew" action="/" method="post">
                    <div class="m-dropzone__msg dz-message needsclick" >
                        <h3 class="m-dropzone__msg-title">Upload Image</h3>
                        <span class="m-dropzone__msg-desc">Images only</span>
                    </div>
                    <div id="image_data"></div>
                    <div id="image-holder"></div>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <div id="image_preview"></div>
        </div>
        <div class="form-group">
            @if(@$advertisement)
                @foreach($advertisementImages as $image)
                    <span id="{{ $image->id }}" >
                        <img src="{{ $image->image }}"  width="100" height="100" style="margin-left: 21px;"/>
                        <a onclick="removeimg({{ $image->id }})"  m-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" aria-describedby="tooltip_xr8lyasjaw" style="position: absolute; color: red;text-decoration: none;" >×</a>
                    </span>
                @endforeach
                <input type="hidden" name="remove_img" id="removeimg">
            @endif
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('advertisement.index')}}">Cancel</a>


