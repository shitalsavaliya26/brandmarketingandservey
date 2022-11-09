@extends('layouts.backend.superadmin.main')
@section('title', 'View Subscriber')
@section('css')
 <style>
label {
    font-weight: bold;
}
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('superadmin.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('subscriber.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Subscribers</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('subscriber.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">View Subscriber</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">View Subscriber</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>First Name : </label>
                                <span>{{@$subscriber->firstname}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Last Name : </label>
                                <span>{{@$subscriber->lastname}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Organization Name : </label>
                                <span>{{$subscriber->organization_name}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Contact Number : </label>
                                <span>{{$subscriber->calling_code}}-{{$subscriber->contact_number}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Website URL : </label>
                                <span>{{$subscriber->website_url}}</span>
                            </div>
                        </div>
                    </div> --}}
                   

                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Email : </label>
                                <span>{{$subscriber->email}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Currency : </label>
                                <span>{{@$subscriber->currency->symbol}} {{@$subscriber->currency->code}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Country : </label>
                                <span>{{@$subscriber->country->name}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Status : </label>
                                <span>{{$subscriber->status!='0'?'Active':'Inative'}}</span>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Mobile Logo : </label>
                                <a href="@if($subscriber->logo != '' && $subscriber->logo != null) {{asset('uploads/subscriber/mobile_logo/'.$subscriber->logo)}} @else # @endif " target="_blank">
                                    <img class="img-thumbnail img-lg mb-2"  onerror="this.src='{{asset('backend/images/no-found.png')}}'" src="{{asset('uploads/subscriber/mobile_logo/'.@$subscriber->logo)}}" height="100" width="auto" style="max-width: 100%" />
                                </a>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Logo : </label>
                                <a href="@if($subscriber->desktop_logo != '' && $subscriber->desktop_logo != null) {{asset('uploads/subscriber/desktop_logo/'.$subscriber->desktop_logo)}} @else # @endif " target="_blank">
                                    <img class="mb-2"  onerror="this.src='{{asset('backend/images/no-found.png')}}'" src="{{asset('uploads/subscriber/desktop_logo/'.@$subscriber->desktop_logo)}}" height="100" width="auto" style="max-width: 100%;height: 60px;" />
                                </a>
                            </div>
                        </div>
                    </div>  
                </div>
                <a class="btn btn-light" href="{{route('subscriber.index')}}">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
