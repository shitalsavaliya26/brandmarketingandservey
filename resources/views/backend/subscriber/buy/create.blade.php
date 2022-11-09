@extends('layouts.backend.subscriber.main')
@section('title', 'Add a Shop')

@section('content')

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add a Shop</h4>
                {!! Form::open(['route' => ['buy.store',request()->route('brandId')],'autocomplete'=>'false','files'=>true,'id'=>'buy-form','method'=>'post']) !!}
                @include('backend.subscriber.buy.form')
                {!! Form::close() !!}       
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection