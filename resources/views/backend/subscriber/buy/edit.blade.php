@extends('layouts.backend.subscriber.main')
@section('title', 'Update Shop')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Shop</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($buy->id); @endphp
                {!! Form::model($buy,['route' => ['buy.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'buy-form-edit']) !!}
                    @method('POST')
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
