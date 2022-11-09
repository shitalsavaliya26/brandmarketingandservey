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
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('superadmin.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('currency.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Currency</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('currency.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Currency Rate</h5></a></div>
                      
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Currency Rate</h4>
                        <h4 class="card-title text-right">
                            @if ($apicalltoday > 0)
                            <a class="btn btn-info mx-2 btn-sm btn-xs-block" href="javascript:void(0)" id="currencyapialert"> <i
                                class="ti-reload menu-icon"></i></a>
                            @else
                            <a class="btn btn-info mx-2 btn-sm btn-xs-block" href="javascript:void(0)" id="currencyapicall"> <i
                                class="ti-reload menu-icon"></i></a>
                            @endif
                          
                        </h4>
                    </div>
                    <form method="POST" action='{{ route('currencyRateEdit') }}' class="forms-sample"
                        id="currencyrateform">
                        @csrf
                        <div class="card-body" id="settingdata">
                            <div class="row">
                                @foreach ($currency as $key => $value)
                                    @if ($key == "USD")
                                        @continue
                                    @endif
                                    <div class="col-sm-12 col-md-3 mt-3">
                                        <label class="">{{ $key }}</label>
                                        <input type="text" name="{{ $key }}" value="{{ $value }}"
                                            class="form-control cus-grid-title-textarea" placeholder="Rate"
                                            autocomplete="off" pattern="[0-9]+([.][0-9]+)*" required>
                                        <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            {{-- <button type="submit" class="btn btn-primary mr-2 mt-3">Update</button> --}}
                            <a class="btn btn-primary mr-2 mt-3" href="javascript:void(0)" id="updatecurrency">Update</a>
                            <a class="btn btn-light mt-3" href="{{ route('currency.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js
  "></script>


    <script>
        /* updatecurrency */
        $(document).on("click", "#updatecurrency", function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Do you want to update currency rate?',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Update',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $( "#currencyrateform" ).submit();
                } else if (result.isDenied) {
                    // Swal.fire('Changes are not saved', '', 'info')
                }
            })
        });

        $(document).on("click", "#currencyapialert", function(event) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "Today's limit is over!",
                })
        });
    </script>
@endsection
