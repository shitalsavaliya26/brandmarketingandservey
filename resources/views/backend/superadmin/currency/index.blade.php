@extends('layouts.backend.superadmin.main')
@section('title', 'Currency')
@section('css')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
<style>
    .subscriber-name {
        height: 47px;
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
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('currency.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Currency</h5></a></div>
                      
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success" role="alert" style="display:none;">

                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Currency</h4>
                    <h4 class="card-title text-right">
                        @if ($apicalltoday > 0)
                            <a class="btn btn-info mx-2 btn-sm btn-xs-block" href="javascript:void(0)"> <i
                                class="ti-reload menu-icon" id="currencyapialert"></i></a>
                            @else
                            <a class="btn btn-info mx-2 btn-sm btn-xs-block" href="javascript:void(0)" id="currencyapicall"> <i
                                class="ti-reload menu-icon"></i></a>
                            @endif
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('currencyRateEdit')}}">Edit Currency Rate</a>
                    </h4>   
                </div>
                <div class="align-items-center justify-content-between">
                    <h4 class="card-title">
                        <form method="get" >
                            <div class="row">
                                <div class="col-sm-12 col-md-3">
                                    <input type="search" name="name" id="subscriber-name-filter" class="form-control subscriber-name" placeholder="Search" value="{{ app('request')->input('name') }}">
                                </div>
                                <div class="col-sm-12 col-md-3 vertical-center d-flex" style="align-items: center;">
                                    <button type="submit" class="btn btn-success btn-sm btn-xs-block d-inline search ">Search</button>
                                    <a class="btn btn-warning btn-sm btn-xs-block d-inline" href="{{route('currency.index')}}" style="margin-left: 20px;">Clear</a>
                                </div>
                            </div>
                        </form>
                    </h4>
                </div>
                @include('backend.superadmin.currency.table',$currency)
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>

<script type="text/javascript">
$(document).on("click", "#currencyapialert", function(event) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "Today's limit is over!",
                })
        });
</script>

@endsection
