@extends('layouts.backend.subscriber.main')
@section('title', 'Update Poll')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('backend/css/plugins/dropzone/dropzone.css')}}">
@endsection
@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Poll</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($mainpoll->id); @endphp
                {!! Form::model($mainpoll,['route' => ['polltitle.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'poll_title_form']) !!}
                @method('POST')
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
