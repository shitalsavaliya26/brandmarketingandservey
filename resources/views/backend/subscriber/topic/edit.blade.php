@extends('layouts.backend.subscriber.main')
@section('title', 'Update Topic')
@section('css')
    <link rel="stylesheet" href="{{asset('backend/css/plugins/tagsinput/tagsinput.css')}}">
    <style type="text/css">
        .bootstrap-tagsinput {
            width: 100%;
            height: 200px;   
        }
        .label {
            line-height: 2 !important;
        }
        .bootstrap-tagsinput .tag{
            background-color:#4b49ac;
            border-radius: 10px;
            padding-left: 10px;
            padding-right: 10px;
        }
        .bootstrap-tagsinput {
            line-height: 34px;
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
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('topic.index',request()->route('brandId'))}}" class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand ->name }} ({{ $brand->country->name }}) Topics</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Edit Topic</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Topic in {{ $brand ->name }} ({{ $brand->country->name }})</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($topic->id); @endphp
                {!! Form::model($topic,['route' => ['topic.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'topic-form-edit']) !!}
                    @method('POST')
                    @include('backend.subscriber.topic.editform')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js').'?v='.time() }}"></script>
<script type="text/javascript" src="{{asset('backend/js/bootstrap-tagsinput.min.js')}}"></script>
<script type="text/javascript" src="{{asset('backend/js/bootstrapValidator.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#email_list_to').on('beforeItemAdd', function(event) {
            /* Validate email */
            if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(event.item)) {
                var checkExistance=$("input[name=email_list_cc]").val();
                if(checkExistance.search(event.item) == -1){
                    event.cancel = false;
                }else{
                    event.cancel = true;
                }
            } else {
                event.cancel = true;
            }
        });
        $('#email_list_cc').on('beforeItemAdd', function(event) {
            /* Validate email */
            if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(event.item)) {
                var checkExistance=$("input[name=email_list_to]").val();
                if(checkExistance.search(event.item) == -1){
                    event.cancel = false;
                }else{
                    event.cancel = true;
                }
            } else {
                event.cancel = true;
            }
        });
    });
</script>
@endsection
