<div class="row">
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-12">
                <label>Question</label>
                <div class="row">
                    <div class="col-12 d-flex">
                        <div class="col-10 d-inline-block" style="padding-left: unset">
                            {!! Form::text('question', old('question'), ['class' => 'form-control capital-input', 'placeholder' => 'Enter question']) !!}
                            <span class="help-block text-danger">{{ $errors->first('question') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <input type="hidden" name="hidden_option[]">
    @if (count($options) <= 0)
        <div class="col-md-12" id="option-div">
            <div class="form-group row">
                <div class="col-12">
                    <label>Options</label>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <div class="col-10 d-inline-block" style="padding-left: unset">
                                <input type="text" class="form-control m-input capital-input"
                                    id="option" name="option[]"
                                    placeholder="Enter Option">
                                <span class="help-block text-danger">{{ $errors->first('option.0') }}</span>
                            </div>
                            <div class="col-2 d-inline-block">
                                <div class="input-group">
                                    <button type="button" name="add" id="add" class="btn btn-primary">
                                        <img src="{{asset('images/plus-16.png')}}" style="width: 15px; height: 15px">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="daynamic_option">
            <div class="form-group row">
                <div class="col-12">
                    <label></label>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <div class="col-10 d-inline-block" style="padding-left: unset">
                                <input type="text" class="form-control m-input capital-input"
                                    id="option" name="option[]"
                                    placeholder="Enter Option">
                                <span class="help-block text-danger">{{ $errors->first('option.1') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-12" id="daynamic_option">
            @php $i = 0;  @endphp
            @foreach ($options as $option)
                @php  $i++  @endphp
                <div class="form-group row dynamic-added" id=row{{$i}}>
                    <div class="col-12">
                        @if($i == 1)
                            <label>Options</label>
                        @else
                            <label></label>
                        @endif

                        <div class="row">
                            <div class="col-12 d-flex">
                                <div class="col-10 d-inline-block" style="padding-left: unset">
                                    <input type="text" class="form-control m-input capital-input"
                                        id="option" name="option[{{$option->id}}]" value="{{ $option->option}}" data-id="{{$option->id}}"
                                        placeholder="Enter Option">
                                    <span class="help-block text-danger">{{ $errors->first('option.1') }}</span>
                                </div>
                                @if($options[1]->id == $option->id)
                                    <div class="col-2 d-inline-block">
                                        <div class="input-group">
                                            <button type="button" name="add" id="add" class="btn btn-primary">
                                                <img src="{{asset('images/plus-16.png')}}" style="width: 15px; height: 15px">
                                            </button>
                                        </div>
                                    </div>

                                @elseif($i >= 2)
                                    <div class="col-2 d-inline-block" id=button{{$i}}>
                                        <div class="input-group">
                                            <button type="button" name="remove" id={{$i}} class="btn btn-danger btn_remove" data-id="{{$option->id}}">
                                                <img src="{{asset('images/minus-2-16.png')}}" style="width: 15px; height: 15px">
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
<button type="submit" class="btn btn-primary mr-2" name="save" value="save">Save</button>
@if ($countrycount > 1)
    <button type="submit" class="btn btn-primary mr-2" name="saveall" value="saveall">Save For All Countries</button>
@endif
<a class="btn btn-light" href="{{route('polltitle.index', [request()->route('brandID')])}}">Cancel</a>



