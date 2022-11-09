@extends('layouts.backend.subscriber.main')
@section('title', 'Update Poll')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('backend/css/plugins/dropzone/dropzone.css')}}">
@endsection
@section('content')
@php $bid= App\Helpers\CustomHelper::getEncrypted($mainpoll->brand_id) @endphp
@php $mid= App\Helpers\CustomHelper::getEncrypted($mainpoll->id) @endphp
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('polltitle.index',[$bid])}}"class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Polls</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a href="{{ route('poll.index',[$mid])}}" class="text-decoration-none text-dark"><h6 class="mt-1">{{ $mainpoll['title'] ?? "" }} Questions</h5></a></div>
                                <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                                <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Edit Question in {{ $mainpoll['title'] ?? "-" }}</h5></a></div>
                        
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Question in {{ $mainpoll['title'] ?? "-" }}</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($poll->id); @endphp
                {!! Form::model($poll,['route' => ['poll.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'poll-form-edit']) !!}
                @method('POST')
                @include('backend.subscriber.poll.form')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script>
    @if (count($options) > 0)
        var i = "{{count($options)}}";
    @else
        var i = 0;
    @endif

    $('#add').click(function() {
        i++;
        $('#daynamic_option').append('<div class="form-group row dynamic-added" id="row' + i + '"><div class="col-12"><label></label><div class="row"><div class="col-12 d-flex"><div class="col-10 d-inline-block" style="padding-left: unset"><input type="text" class="form-control capital-input m-input" id="option'
        + i + '" name="option[]" placeholder="Enter Option" autofocus ></div><div class="col-2 d-inline-block" id="button' + i + '"><div class="input-group"><button type="button" name="remove" id="' 
        + i + '" class="btn btn-danger btn_remove"><img src="{{asset('images/minus-2-16.png')}}" style="width: 15px; height: 15px"></button></div></div></div></div></div></div>');
    });
    var option_change = [];
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
        $('#button' + button_id + '').remove();

        var data = {};
        var option_id = $(this).attr("data-id");
        data = option_id;
        option_change.push(data); 
        $('input:hidden[name="hidden_option[]"]').val((option_change));
    });
    $(function() {
        if($('#answertype :selected').text() == 'Text Box'){
            $('#daynamic_option').hide();
            $('#option-div').hide();
        }else{
            $('#daynamic_option').show();
            $('#option-div').show();
        }
        $('#answertype').change(function(){
            if($('#answertype :selected').text() == 'Text Box') {
                $('#daynamic_option').hide();
                $('#option-div').hide();
            } else {
                $('#daynamic_option').show();
                $('#option-div').show();
            }
        });
    });
</script>
@endsection
