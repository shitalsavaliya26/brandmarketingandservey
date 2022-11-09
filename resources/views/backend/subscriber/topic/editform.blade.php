<div class="row">
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Name</label>
                {!! Form::text('name',old('name'),['class'=>'form-control capital-input','placeholder'=>'Enter name']) !!}
                <span class="help-block text-danger">{{ $errors->first('name') }}</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Email List(To)</label>
                @if(isset($topic))
                    <input type="text" value="{{$topic->email_list_to}}" data-role="tagsinput" id="email_list_to" name="email_list_to" class="form-control" placeholder="Enter Email List(To)">
                @else
                    <input type="text" value="" data-role="tagsinput" id="email_list_to" name="email_list_to" class="form-control">
                @endif
                <span class="help-block text-danger">{{ $errors->first('email_list_to') }}</span>
                <label id="email_list_to-error" class="error" for="email_list_to"></label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-12">
                <label class="">Email List(Cc)</label>
                @if(isset($topic))
                    <input type="text" value="{{$topic->email_list_cc}}" data-role="tagsinput" id="email_list_cc" name="email_list_cc" class="form-control" placeholder="Enter Email List(Cc)">
                @else
                    <input type="text" value="" data-role="tagsinput" id="email_list_cc" name="email_list_cc" class="form-control">
                @endif
                <span class="help-block text-danger">{{ $errors->first('email_list_cc') }}</span>
                <label id="email_list_cc-error" class="error" for="email_list_cc"></label>
            </div>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary mr-2">Save</button>
<a class="btn btn-light" href="{{route('topic.index', [request()->route('brandId')])}}">Cancel</a>
