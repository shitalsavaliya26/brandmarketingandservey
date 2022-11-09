<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Name</label>
                {!! Form::text('name',old('name'),['class'=>'form-control capital-input','placeholder'=>'Enter name']) !!}
                <span class="help-block text-danger">{{ $errors->first('name') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            @if(isset($brand) && $brand->logo!="")
                <div class="col-12">
                    <label class="">Logo (Standard Size: 16:9) </label>
                    {!! Form::file('logo',['class'=>'form-control','placeholder'=>'Enter Logo']) !!}
                    <span class="help-block text-danger">{{ $errors->first('logo') }}</span>
                </div>
                <div class="col-12">
                    <a href="{{$brand->logo}}" target="_blank">
                        <img class="img-thumbnail img-lg my-2" src="{{$brand->logo}}" height="100" width="auto" style="max-width: 100%" />
                    </a>
                </div>
            @else
            <div class="col-12">
                <label class="">Logo (Standard Size: 16:9) </label>
                {!! Form::file('logo',['class'=>'form-control','placeholder'=>'Enter Logo']) !!}
                <span class="help-block text-danger">{{ $errors->first('logo') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="row mb-3 mt-md-2">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Website URL</label>
                {!! Form::text('website_url',old('website_url'),['class'=>'form-control','placeholder'=>'Enter Website URL']) !!}
                <span class="help-block text-danger">{{ $errors->first('website_url') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Country</label>
                {{-- @if(!isset($brand))
                    <select class="form-control" id="country_id" name="country_id">
                        <option value=''>--Select Country--</option>
                        @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                @else
                    <select class="form-control" id="country_id" name="country_id" disabled>
                        <option value=''>--Select Country--</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ $brand->country_id == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}</option>
                        @endforeach
                    </select>
                @endif --}}
                <select class="js-example-basic-multiple form-control" multiple="multiple" id="country_id"
                    name="country_id[]">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                <label id="country_id-error" class="error" for="country_id">
                    <div></div>
                </label>
              
                <span class="help-block text-danger">{{ $errors->first('country_id') }}</span>
            </div>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('brand.index')}}">Cancel</a>


