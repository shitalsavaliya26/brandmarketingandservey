<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Name</label>
                {!! Form::text('name', old('name'), ['class' => 'form-control capital-input', 'placeholder' => 'Enter name','readonly' => true]) !!}
                <span class="help-block text-danger">{{ $errors->first('name') }}</span>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3 mt-md-2">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Website URL</label>
                {!! Form::text('website_url', old('website_url'), [
                    'class' => 'form-control',
                    'placeholder' => 'Enter Website URL',
                ]) !!}
                <span class="help-block text-danger">{{ $errors->first('website_url') }}</span>
            </div>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{ route('brand.index') }}">Cancel</a>