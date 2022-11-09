@extends('layouts.backend.superadmin.main')
@section('title', 'Price Settings')
@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('superadmin.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('price-setting.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Price Settings</h5></a></div>
                      
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Price Settings</h4>
                {!! Form::open(['route' => 'price-setting.store','autocomplete'=>'false','files'=>true,'id'=>'price-setting-form','method'=>'post']) !!}
                    @foreach($priceSettings as $row)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="title[]" value="{{$row->title}}" class='form-control' placeholder="Enter Title" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        @if ($row->title == "User Poll Winning Price")
                                        <span class="input-group-text bg-primary text-white">%</span>
                                        @else
                                        <span class="input-group-text bg-primary text-white">$</span>
                                        @endif  
                                      </div>
                                      <input type="text" name="price[{{$row->title}}]" id="price[{{$row->title}}]" value="{{$row->price}}" class='form-control price' placeholder="Enter Price" autocomplete="off" @if ($row->title == "User Poll Winning Price") max="100" @endif>
                                    </div>
                                  </div>     
                            </div>
                        </div><br>
                    @endforeach
                    <br>
                    <button type="submit" class="btn btn-primary mr-2">Save</button>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script>
   
    $("#price-setting-form").validate({
        rules: {
            "price[User Poll Winning Price]":{
                required: true,
                max:100,
            },
        },
        messages: {
            "price[User Poll Winning Price]": {
                required: "Please enter poll title",
            },
        },
    });



    $('.price').keypress(function(event) {
    var $this = $(this);
    if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
       ((event.which < 48 || event.which > 57) &&
       (event.which != 0 && event.which != 8))) {
           event.preventDefault();
    }

    var text = $(this).val();
    if ((event.which == 46) && (text.indexOf('.') == -1)) {
        setTimeout(function() {
            if ($this.val().substring($this.val().indexOf('.')).length > 4) {
                $this.val($this.val().substring(0, $this.val().indexOf('.') + 4));
            }
        }, 1);
    }

    if ((text.indexOf('.') != -1) &&
        (text.substring(text.indexOf('.')).length > 3) &&
        (event.which != 0 && event.which != 8) &&
        ($(this)[0].selectionStart >= text.length - 3)) {
            event.preventDefault();
    }      
});

$('.price').bind("paste", function(e) {
var text = e.originalEvent.clipboardData.getData('Text');
if ($.isNumeric(text)) {
    if ((text.substring(text.indexOf('.')).length > 3) && (text.indexOf('.') > -1)) {
        e.preventDefault();
        $(this).val(text.substring(0, text.indexOf('.') + 3));
   }
}
else {
        e.preventDefault();
     }
});
</script>
@endsection
