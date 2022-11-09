<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">First Name</label>
                {!! Form::text('firstname',old('firstname'),['class'=>'form-control capital-input','placeholder'=>'Enter first name']) !!}
                <span class="help-block text-danger">{{ $errors->first('firstname') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Last Name</label>
                {!! Form::text('lastname',old('lastname'),['class'=>'form-control capital-input','placeholder'=>'Enter last name']) !!}
                <span class="help-block text-danger">{{ $errors->first('lastname') }}</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Organization Name</label>
                {!! Form::text('organization_name',old('organization_name'),['class'=>'form-control capital-input','placeholder'=>'Enter organization name']) !!}
                <span class="help-block text-danger">{{ $errors->first('organization_name') }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group row">
            @if(isset($subscriber) && $subscriber->logo!="")
                <div class="col-12">
                    <label class="">Logo</label>
                    {!! Form::file('logo',['class'=>'form-control','placeholder'=>'Enter Logo']) !!}
                    <span class="help-block text-danger">{{ $errors->first('logo') }}</span>
                </div>
                <div class="col-12 logo-display">
                    <a href="{{asset('uploads/subscriber/mobile_logo/'.@$subscriber->logo)}}" target="_blank">
                        <span id="{{ $subscriber->id }}">
                            <img class="img-thumbnail img-lg mb-2" src="{{asset('uploads/subscriber/mobile_logo/'.@$subscriber->logo)}}" height="100" width="auto" style="max-width: 100%" />
                            <a onclick="removeimg({{ $subscriber->id }})"  m-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" aria-describedby="tooltip_xr8lyasjaw" style="position: absolute; color: red;text-decoration: none;" >×</a>
                        </span>
                    </a>
                </div>
                <input type="hidden" name="remove_img" id="removeimg">

            @else
            <div class="col-12">
                <label class="">Logo</label>
                {!! Form::file('logo',['class'=>'form-control','placeholder'=>'Enter Logo']) !!}
                <span class="help-block text-danger">{{ $errors->first('logo') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Contact Number</label>
                {!! Form::text('contact_number',old('contact_number'),['class'=>'form-control','placeholder'=>'Enter contact number']) !!}
                <span class="help-block text-danger">{{ $errors->first('contact_number') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Website URL</label>
                {!! Form::text('website_url',old('website_url'),['class'=>'form-control','placeholder'=>'Enter website url']) !!}
                <span class="help-block text-danger">{{ $errors->first('website_url') }}</span>
            </div>
        </div>
    </div>
</div>                    
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Email</label>
                {!! Form::text('email',old('email'),['class'=>'form-control','placeholder'=>'Enter email']) !!}
                <span class="help-block text-danger">{{ $errors->first('email') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Country</label>
                <select class="form-control" id="country_id" name="country_id">
                    <option value="null">--Select Country--</option>
                    @foreach($countries as $country)
                        <option value="{{$country->id}}" @if(isset($subscriber)) {{$subscriber->country_id == $country->id  ? 'selected' : ''}} @endif>{{$country->name}}</option>
                    @endforeach
                </select>
                <span class="help-block text-danger">{{ $errors->first('country_id') }}</span>
            </div>
        </div>
    </div>
</div>
@if(!isset($subscriber))
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-12">
                <label class="d-block mb-0">Status</label>
                <div class="form-check d-inline-block">
                    <label class="form-check-label">
                        {!! Form::radio('status', '1',@$subscriber->status!='0'?true:false,[],['class'=>'form-control']) !!} 
                        Active
                    </label>
                </div>
                <div class="form-check d-inline-block ml-3">
                    <label class="form-check-label">
                        {!! Form::radio('status', '0',@$subscriber->status!='0'?false:true,[],['class'=>'form-control']) !!}
                        Inactive
                    </label>
                </div>
            </div>
            <span class="help-block text-danger">{{ $errors->first('status') }}</span>
        </div>
    </div>
</div>
@endif
<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('subscriber.index')}}">Cancel</a>
