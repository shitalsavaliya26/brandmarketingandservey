@extends('layouts.backend.subscriber.main')
@section('title', 'Update Brand')
@section('css')
    <style>
    .select2-container--default .select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        cursor: text;
        padding-bottom: 6px;
        padding-right: 5px;
        position: relative;
      }
    </style>
@endsection
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
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Edit Brand</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit {{ $brand->name }} ({{ $brand->country->name }})</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($brand->id); @endphp
                {!! Form::model($brand,['route' => ['sinlecollapsebrandedit',$id],'autocomplete'=>'false','files'=>true,'id'=>'brand-form-edit']) !!}
                    {{-- @method('patch') --}}
                    @include('backend.subscriber.brand.singlecollapseeditform')
                {!! Form::close() !!}       
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    var brandExits = "{{ route('brandExits') }}";
    var group_id = "{{ $brand->group_id }}";
</script>
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection
