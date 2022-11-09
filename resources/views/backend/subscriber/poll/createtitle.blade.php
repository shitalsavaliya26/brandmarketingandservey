@extends('layouts.backend.subscriber.main')
@section('title', 'Create a Poll')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('backend/css/plugins/dropzone/dropzone.css')}}">
@endsection
@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Create a Poll</h4>
                {!! Form::open(['route' => ['polltitle.create',request()->route('brandId')],'autocomplete'=>'false','files'=>true,'id'=>'poll_title_form','method'=>'post']) !!}
                @include('backend.subscriber.poll.main')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection
