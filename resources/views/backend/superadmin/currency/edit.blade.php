@extends('layouts.backend.superadmin.main')
@section('title', 'Currency')

@section('css')
    <style>
        .form-group {
            margin-bottom: -20px;
        }

        .col-sm-2 {
            max-width: 10.66667%;
        }

        .description {
            margin-left: 80px;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <h4 class="card-title">Currency [ {{ @$currency->symbol }} {{ @$currency->name }} ]</h4>
                    </div>
                    @php $id= App\Helpers\CustomHelper::getEncrypted($currency->id); @endphp
                    <form method="POST" action='{{ route('currency.update', [$id]) }}' class="forms-sample" id="currencyform">
                        @method('PUT')
                        @csrf
                        <div class="card-body" id="settingdata">
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <input type="text" name="amount" id="amount" class="form-control"
                                        placeholder="Rate" min="0" value="{{ @$currency->rate }}" autocomplete="off">
                                    <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-primary mr-2 mt-3">Update</button>
                            <a class="btn btn-light mt-3" href="{{ route('currency.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script>
       
    </script>
@endsection
