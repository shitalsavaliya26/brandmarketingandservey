<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Link</label>
            <div class="col-sm-9">
                {!! Form::text('link',old('link'),['class'=>'form-control','placeholder'=>'Enter Link']) !!}
                <span class="help-block text-danger">{{ $errors->first('link') }}</span>
            </div>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('buy.index', [request()->route('brandId')])}}">Cancel</a>
