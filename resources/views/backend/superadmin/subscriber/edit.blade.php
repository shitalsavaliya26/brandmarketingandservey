@extends('layouts.backend.superadmin.main')
@section('title', 'Update Subscriber')
@section('content')

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Subscriber</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($subscriber->id); @endphp
                {!! Form::model($subscriber,['route' => ['subscriber.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'subscriber-form-edit']) !!}
                    @method('patch')
                    @include('backend.superadmin.subscriber.form')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script type="text/javascript">

    function removeimg(id)
        {
            $('#'+id).css("display","none");
            var imgs = $('#removeimg').val();
            if(imgs != '')
            {
                imgs = imgs+','+id;
                $('#removeimg').val(imgs);
            }else
            {
                $('#removeimg').val(id);
            }
            $('.logo-display').hide();

        }
    </script>
@endsection
