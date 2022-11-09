@extends('layouts.backend.subscriber.main')
@section('title', 'Shop')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }})  Shop</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) Shop</h4>
                @php @$id= App\Helpers\CustomHelper::getEncrypted(@$buy->id); @endphp
                {!! Form::open(['route' => ['buy.store',request()->route('brandId')],'autocomplete'=>'false','files'=>true,'id'=>'buy-form','method'=>'post']) !!}
                    @method('POST')
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="form-group row align-items-center">
                                <div class="col-12">
                                    <label class="">Link</label>
                                    @if(isset($buy))
                                        <input type="text" class="form-control" placeholder="Enter Link" name="link" id="link" value="{{$buy->link}}">
                                    @else
                                        <input type="text" class="form-control" placeholder="Enter Link" name="link" id="link" >
                                    @endif
                                    <span class="help-block text-danger">{{ $errors->first('link') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mr-2" name="save" value="save">Save</button>
                    @if ($countrycount > 1)
                    <button type="submit" class="btn btn-primary mr-2" name="saveall" value="saveall">Save For All Countries</button>
                     @endif
                    <a class="btn btn-light" href="{{route('brand.index')}}">Cancel</a>
                    
                {!! Form::close() !!}       
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection
