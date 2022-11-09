<div class="row">
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-12">
                <label>Title</label>
                <div class="row">
                    <div class="col-12 d-flex">
                        <div class="col-10 d-inline-block" style="padding-left: unset">
                            {!! Form::text('title', old('title'), ['class' => 'form-control capital-input', 'placeholder' => 'Enter title']) !!}
                            <span class="help-block text-danger">{{ $errors->first('title') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if (\Request::route()->getName() == "polltitle.create")
<button type="submit" class="btn btn-primary mr-2">Save</button> 
<a class="btn btn-light" href="{{route('polltitle.index', [request()->route('brandId')])}}">Cancel</a>
@else
@php $id= App\Helpers\CustomHelper::getEncrypted($mainpoll['brand_id']); @endphp
<button type="submit" class="btn btn-primary mr-2">Update</button> 
<a class="btn btn-light" href="{{route('polltitle.index', [$id])}}">Cancel</a>
@endif


