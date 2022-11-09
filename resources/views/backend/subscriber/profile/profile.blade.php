@extends('layouts.backend.subscriber.main')
@section('title', 'Profile')
@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('subscriber.profile')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Edit Profile</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Profile</h4>
                    @php $id= App\Helpers\CustomHelper::getEncrypted($profile->id); @endphp
                    {!! Form::model($profile, [
                        'route' => ['profile.update', $id],
                        'autocomplete' => 'false',
                        'files' => true,
                        'id' => 'profile-form-edit',
                        'method' => 'post',
                    ]) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Organization Name</label>
                                    {!! Form::text('organization_name', old('organization_name'), [
                                        'class' => 'form-control capital-input',
                                        'placeholder' => 'Enter organization name',
                                    ]) !!}
                                    <span class="help-block text-danger">{{ $errors->first('organization_name') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Email</label>
                                    @if (empty(Auth::user()->provider_name))
                                        {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Enter email','disabled' => true]) !!}
                                    @else
                                        {!! Form::text('email', old('email'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter email',
                                            'disabled' => true,
                                            // 'readonly' => true,
                                        ]) !!}
                                    @endif

                                    <span class="help-block text-danger">{{ $errors->first('email') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Name</label>
                                    {!! Form::text('firstname', old('firstname'), [
                                        'class' => 'form-control capital-input',
                                        'placeholder' => 'Enter name',
                                    ]) !!}
                                    <span class="help-block text-danger">{{ $errors->first('firstname') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Surname</label>
                                    {!! Form::text('lastname', old('lastname'), [
                                        'class' => 'form-control capital-input',
                                        'placeholder' => 'Enter surname',
                                    ]) !!}
                                    <span class="help-block text-danger">{{ $errors->first('lastname') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Country</label>
                                    <select class="form-control  js-example-basic-single" id="country_id" name="country_id">
                                        <option value="">--Select Country--</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ $profile->country_id == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block text-danger">{{ $errors->first('name') }}</span>
                                    <label id="country_id-error" class="error" for="country_id"></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Contact Number</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-control js-example-basic-single" id="calling_code" name="calling_code">
                                                @foreach ($callingcode as $code)
                                                    <option value="{{ $code }}"
                                                        {{ $code == auth()->user()->calling_code ? 'selected' : '' }}>
                                                        {{ $code }}</option>
                                                @endforeach
                                            </select>
                                            <span
                                                class="help-block text-danger">{{ $errors->first('calling_code') }}</span>
                                        </div>
                                        <div class="col-md-9">
                                            {!! Form::text('contact_number', old('contact_number'), [
                                                'class' => 'form-control',
                                                'placeholder' => 'Enter contact number',
                                            ]) !!}
                                            <span
                                                class="help-block text-danger">{{ $errors->first('contact_number') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        
                    </div>
                    <div class="row">
                        {{-- <div class="col-md-6">
                            <div class="form-group row">
                                @if (isset($profile) && $profile->logo != '')
                                    <div class="col-12">
                                        <label class="">Mobile Logo (Standard Size: 1:1)</label>
                                        {!! Form::file('logo', ['class' => 'form-control', 'placeholder' => 'Enter Logo', 'id' => 'logo']) !!}
                                        <span class="help-block text-danger">{{ $errors->first('logo') }}</span>
                                    </div>
                                    <div class="col-12">
                                        <img class="img-thumbnail img-lg my-2"
                                            onerror="this.src='{{ asset('backend/images/no-found.png') }}'"
                                            src="{{ asset('uploads/subscriber/mobile_logo/' . $profile->logo) }}"
                                            height="100" width="auto" style="max-width: 100%" />
                                    </div>
                                @else
                                    <div class="col-12">
                                        <label class="">Mobile Logo (Standard Size: 1:1)</label>
                                        {!! Form::file('logo', ['class' => 'form-control', 'placeholder' => 'Enter Logo', 'id' => 'logo']) !!}
                                        <span class="help-block text-danger">{{ $errors->first('logo') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group row">
                                @if (isset($profile) && $profile->desktop_logo != '')
                                    <div class="col-12">
                                        <label class="">Logo (Standard Size: 3:1)</label>
                                        {!! Form::file('desktop_logo', [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Logo',
                                            'id' => 'desktop_logo',
                                        ]) !!}
                                        <span class="help-block text-danger">{{ $errors->first('desktop_logo') }}</span>
                                    </div>
                                    <div class="col-12">
                                        <img class="img-thumbnail my-2"
                                            onerror="this.src='{{ asset('backend/images/no-found.png') }}'"
                                            src="{{ asset('uploads/subscriber/desktop_logo/' . $profile->desktop_logo) }}"
                                            height="100" width="auto" style="max-width: 100%;height: 60px;" />
                                    </div>
                                @else
                                    <div class="col-12">
                                        <label class="">Logo (Standard Size: 3:1)</label>
                                        {!! Form::file('desktop_logo', [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Logo',
                                            'id' => 'desktop_logo',
                                        ]) !!}
                                        <span class="help-block text-danger">{{ $errors->first('desktop_logo') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Currency</label>
                                    <select class="form-control" id="currency_id" name="currency_id"
                                        @if (!empty(auth()->user()->currency_id)) disabled @endif style="color: #495057;">
                                        <option value="">--Select Currency--</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}"
                                                {{ auth()->user()->currency_id == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->code }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block text-danger">{{ $errors->first('currency_id') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="">Currency</label>
                                    <select class="form-control" id="currency_id" name="currency_id"
                                        @if (!empty(auth()->user()->currency_id)) disabled @endif style="color: #495057;">
                                        <option value="">--Select Currency--</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}"
                                                {{ auth()->user()->currency_id == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->code }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block text-danger">{{ $errors->first('currency_id') }}</span>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <button type="submit" class="btn btn-primary mr-2 mt-3">Save</button>
                    @if ($profile->pause_account == '1')
                        <a class="btn btn-danger mr-2 mt-3" href="{{ route('subscriber.unpauseaccount', $id) }}">Reactive
                            Account</a>
                    @elseif($profile->pause_account == null)
                        <a class="btn btn-warning mr-2 mt-3" href="{{ route('subscriber.pauseaccount', $id) }}">Pause
                            Account</a>
                    @endif
                    <a class="btn btn-danger mt-3" href="{{ route('subscriber.deleteaccount', $id) }}" id="delete-account">Delete Account</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('backend/js/custom/member.js') . '?v=' . time() }}"></script>
    <script>
        // $('#desktop_logo').change(function() {
        //   $('#desktop_logo').removeData('imageWidth');
        //   $('#desktop_logo').removeData('imageHeight');
        //   var file = this.files[0];
        //   var tmpImg = new Image();
        //   tmpImg.src=window.URL.createObjectURL( file ); 
        //   tmpImg.onload = function() {
        //       width = tmpImg.naturalWidth,
        //       height = tmpImg.naturalHeight;
        //       $('#desktop_logo').data('imageWidth', width);
        //       $('#desktop_logo').data('imageHeight', height);
        //   }
        // });

        // $('#logo').change(function() {
        //   $('#logo').removeData('imageWidth');
        //   $('#logo').removeData('imageHeight');
        //   var file = this.files[0];
        //   var tmpImg = new Image();
        //   tmpImg.src=window.URL.createObjectURL( file ); 
        //   tmpImg.onload = function() {
        //       width = tmpImg.naturalWidth,
        //       height = tmpImg.naturalHeight;
        //       $('#logo').data('imageWidth', width);
        //       $('#logo').data('imageHeight', height);
        //   }
        // });

        /* change country to get currency */
        @if(Session::has('country'))
            var countrydata = "{{Session::get('country')}}";
            $('#country_id').val(countrydata).trigger('change');
        @endif

        @if(Session::has('calling_code'))
            var calling_code = "{{Session::get('calling_code')}}";
            $('#calling_code').val(calling_code).trigger('change');
        @endif

        @if(Session::has('currency'))
            @if (empty(auth()->user()->currency_id))  
            var currency = "{{Session::get('currency')}}";
            $('#currency_id').val(currency).trigger('change');
            @endif
        @endif
       
        var fetchurl = "{{ route('currencyget', ':id') }}"
        $('#country_id').on('change', function() {
            $("#country_id").blur();
            var val = this.value;
            if(val != ''){
                url = fetchurl.replace(':id', val);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: "json",
                    success: function (response) {    
                        // console.log(response);
                        // $("#calling_code").val(response.callingcode);
                        // $("#calling_code").blur(); 
                        // $('#calling_code [value=' + response.callingcode + ']').attr('selected', true);
                        $('#calling_code').val(response.callingcode).trigger('change');

                        @if (empty(auth()->user()->currency_id))  
                        if (response.success) {
                            $("#currency_id").val(response.data);
                            $("#currency_id").blur(); 
                        }
                        else{
                            $("#currency_id").val(1);
                            $("#currency_id").blur();
                        }
                        @endif
                    },
                });
            }
        });
      


        $(document).on("click","#delete-account",function(event) {
            event.preventDefault();
            var href = $(this).attr('href');
            Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                type: "GET",
                url: href,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if(data != 0){

                        Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Account deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                        })

                        setTimeout((function() {
                            location.reload();
                        }), 2000);


                    }
                    else{
                        location.reload();
                    }
                    
                }
            });
            }
            })
        });
    </script>
@endsection
