@extends('layouts.backend.superadmin.main')
@section('title', 'Add a Subscriber')
@section('content')

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add a Subscriber</h4>
                {!! Form::open(['route' => 'subscriber.store','autocomplete'=>'false','files'=>true,'id'=>'subscriber-form','method'=>'post']) !!}
                    @include('backend.superadmin.subscriber.form')
                {!! Form::close() !!}       
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection