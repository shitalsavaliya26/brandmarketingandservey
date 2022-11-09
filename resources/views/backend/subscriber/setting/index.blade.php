@extends('layouts.backend.subscriber.main')
@section('title', 'Setting')

@section('css')
<style>
.form-group {
    margin-bottom:-20px;
}
.col-sm-2{
    max-width: 10.66667%;
}
.description {
    margin-left: 80px;
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
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brand</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Setting</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="col-12">
                    <h4 class="card-title">{{ $brand->name }} ({{ $brand->country->name }}) Setting</h4>
                </div>
                <form method="POST" action='{{ route("setting.update") }}' class="forms-sample">
                    @csrf
                        <div class="card-body" id="settingdata">

                            @php $id= App\Helpers\CustomHelper::getEncrypted($settings->id); @endphp
                            <input type="hidden" name="id" value="{{$id}}">
                            <div class="form-group row border-bottom">
                                <div class="col-12">
                                    <label class="d-block font-weight-bold mb-0">Messages:</label>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label" for="tellactive">
                                            <input type="radio" class="form-check-input" name="tell" id="tellactive" value="1"  {{ ($settings->tell['isShowing']=="1")? "checked" : "" }} >
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block ml-3">
                                        <label class="form-check-label" for="tellinactive">
                                            <input type="radio" class="form-check-input" name="tell" id="tellinactive" value="0" {{ ($settings->tell['isShowing']=="0")? "checked" : "" }} >
                                            Inactive
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block mt-0 description">
                                        <label class="form-check-label ml-0">
                                            Receive e-mail topic messages from the Public
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row border-bottom">
                                <div class="col-12">
                                    <label class="d-block font-weight-bold mb-0">Info:</label>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label" for="knowactive">
                                            <input type="radio" class="form-check-input" name="know" id="knowactive" value="1" {{ ($settings->know['isShowing']=="1")? "checked" : "" }} >
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block ml-3">
                                        <label class="form-check-label" for="knowinactive">
                                            <input type="radio" class="form-check-input" name="know" id="knowinactive" value="0" {{ ($settings->know['isShowing']=="0")? "checked" : "" }} >
                                            Inactive
                                    </label>
                                    </div>
                                    <div class="form-check d-inline-block mt-0 description">
                                        <label class="form-check-label ml-0">
                                            Send and distribute topic information requests to the public
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row border-bottom">
                                <div class="col-12">
                                    <label class="d-block font-weight-bold mb-0">Polls:</label>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label" for="thinkactive">
                                            <input type="radio" class="form-check-input" name="think" id="thinkactive" value="1" {{ ($settings->think['isShowing']=="1")? "checked" : "" }} >
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block ml-3">
                                        <label class="form-check-label" for="thinkinactive">
                                            <input type="radio" class="form-check-input" name="think" id="thinkinactive" value="0" {{ ($settings->think['isShowing']=="0")? "checked" : "" }} >
                                            Inactive
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block mt-0 description">
                                        <label class="form-check-label ml-0">
                                            Get the public to take your survey
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row border-bottom">
                                <div class="col-12">
                                    <label class="d-block font-weight-bold mb-0">Shop:</label>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label" for="buyactive">
                                            <input type="radio" class="form-check-input" name="buy" id="buyactive" value="1" {{ ($settings->buy['isShowing']=="1")? "checked" : "" }} >
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block ml-3">
                                        <label class="form-check-label" for="buyinactive">
                                            <input type="radio" class="form-check-input" name="buy" id="buyinactive" value="0" {{ ($settings->buy['isShowing']=="0")? "checked" : "" }} >
                                            Inactive
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block mt-0 description">
                                        <label class="form-check-label ml-0">
                                            Give the public direct access to your shopping site
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row border-bottom">
                                <div class="col-12">
                                    <label class="d-block font-weight-bold mb-0">Win:</label>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label" for="winactive">
                                            <input type="radio" class="form-check-input" name="win" id="winactive" value="1" {{ ($settings->win['isShowing']=="1")? "checked" : "" }} >
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block ml-3">
                                        <label class="form-check-label" for="wininactive">
                                            <input type="radio" class="form-check-input" name="win" id="wininactive" value="0" {{ ($settings->win['isShowing']=="0")? "checked" : "" }} >
                                            Inactive
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block mt-0 description">
                                        <label class="form-check-label ml-0">
                                            Run a competition
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row border-bottom">
                                <div class="col-12">
                                    <label class="d-block font-weight-bold mb-0">Website:</label>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label" for="websiteactive">
                                            <input type="radio" class="form-check-input" name="website" id="websiteactive" value="1" {{ ($settings->website['isShowing']=="1")? "checked" : "" }} >
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block ml-3">
                                        <label class="form-check-label" for="websiteinactive">
                                            <input type="radio" class="form-check-input" name="website" id="websiteinactive" value="0" {{ ($settings->website['isShowing']=="0")? "checked" : "" }} >
                                            Inactive
                                        </label>
                                    </div>
                                    <div class="form-check d-inline-block mt-0 description">
                                        <label class="form-check-label ml-0">
                                            Direct the public to your website directly
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2 mt-3" name="save" value="save">Save</button>
                            @if ($countrycount > 1)
                                <button type="submit" class="btn btn-primary mr-2 mt-3" name="saveall" value="saveall">Save For All Countries</button>
                            @endif
                            <a class="btn btn-light mt-3" href="{{route('brand.index')}}">Cancel</a>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script>
    $( document ).ready(function() {
        $("#error-alert").fadeTo(2000, 500).slideUp(500, function() {
            $("#error-alert").slideUp(500);
        });

    });
</script>
@endsection
