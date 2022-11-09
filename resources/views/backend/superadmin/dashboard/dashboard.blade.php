@extends('layouts.backend.superadmin.main')
@section('title', 'Dashboard')
@section('css')
    <link rel="stylesheet" href="{{asset('backend/css/plugins/daterangepicker/datetimepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/bootstrapValidator.min.css')}}">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css"> -->
    <style>
        .dashboard-detail{
            font-weight: bold;
            font-size: .875rem;
            margin-right: 52px;
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
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Dashboard</h3>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 grid-margin transparent">
        <div class="row">
            <div class="col-md-1 mb-4 stretch-card transparent">
            </div>
            <div class="col-md-2 mb-4 stretch-card transparent">
                <div class="card card-tale">
                    <div class="card-body">
                        <p class="font-15 mb-4 minh-45">Subscribers</p>
                        <p class="font-18 mb-2">{{$totalSubscribers}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                    <div class="card-body">
                        <p class="font-15 mb-4 minh-45">Active Users</p>
                        <p class="font-18 mb-2">{{$totalUsers}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-4 stretch-card transparent">
                <div class="card card-light-blue">
                    <div class="card-body">
                        <p class="font-15 mb-4 minh-45">Account Balance</p>
                        <p class="font-18 mb-2">${{number_format((float)$totalAmount, 3, '.', '')}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-4 stretch-card transparent">
                <div class="card card-light-danger">
                    <div class="card-body">
                        <p class="font-15 mb-4 minh-45">Paused</p>
                        <p class="font-18 mb-2">{{$closeSubscriptions}}</p>
                    </div>
                </div>
            </div>
            {{-- Subscriptions --}}

            <div class="col-md-2 mb-4 stretch-card transparent">
                <div class="card card-tale">
                    <div class="card-body">
                        <p class="font-15 mb-4 minh-45">User Credit</p>
                       
                        <p class="font-18 mb-2">$ {{number_format((float)$totalAmountuser, 3, '.', '')}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-1 mb-4 stretch-card transparent">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                {!! Form::open(['autocomplete'=>'false','id'=>'dahboard-form','method'=>'get']) !!}
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <div class="col-6">
                                    <label class="">Country</label>
                                    <select class="form-control" id="country_id" name="country_id">
                                        <option value="null">--Select Country--</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}" @if(app('request')->input('country_id') == $country->id) selected @endif>{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block text-danger">{{ $errors->first('country_id') }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="">City</label>
                                    <select class="form-control" id="city_id" name="city_id">
                                    </select>
                                    <span class="help-block text-danger">{{ $errors->first('city_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group row">
                                <div class="col-6">
                                    <label class="">Start Date</label>
                                    <input id="startDate" name="startDate" type="text" class="form-control" value="{{ app('request')->input('startDate') }}" autocomplete="off" />
                                    <span class="help-block text-danger">{{ $errors->first('startDate') }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="">End Date</label>
                                    <input id="endDate" name="endDate" type="text" class="form-control" value="{{ app('request')->input('endDate') }}" autocomplete="off" />
                                    <span class="help-block text-danger">{{ $errors->first('endDate') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" id="search" style="margin-top: 35px;">Search</button>
                            <a href="{{route('superadmin.dashboard')}}" class="btn btn-success" id="clear" style="margin-top: 35px;">Clear</a>
                        </div>
                    </div>
                {!! Form::close() !!}
                <label class="dashboard-detail" >Total Subscriber : {{$subscriberCount}}</label>
                <label class="dashboard-detail" >Total User : {{$userCount}}</label>
                @if(isset($priceSetting) && count($priceSetting) > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th></th>
                                    <th class="text-left">Total</th>
                                    @foreach($priceSetting as $row)
                                        @if ($row->title == "User Poll Winning Price" || $row->title == "User Withdrawal Setting")
                                            @continue
                                        @endif
                                        <th> @if ($row->title == "Poll Question")
                                            Poll (Per Question)
                                        @else
                                            {{  $row->title }}
                                        @endif  </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <th>Cost</th>
                                    <!-- <th class="text-left"> ${{$priceSetting->sum('price')}}</th> -->
                                    <th class="text-left"></th>
                                    @foreach($priceSetting as $row)
                                        @if ($row->title == "User Poll Winning Price" || $row->title == "User Withdrawal Setting")
                                            @continue
                                        @endif
                                        <td>${{number_format((float)$row->price, 3, '.', '')}}</td>
                                    @endforeach
                                </tr>
                                <tr class="text-center">
                                    <th>Qty</th>
                                    <th class="text-left"> {{$countHistory}}</th>
                                    <td>{{@$count['messageHistoriesCount']}}</td>
                                    <td>{{@$count['infoHistoriesCount']}}</td>
                                    <td>{{@$count['distributionHistoriesCount']}}</td>
                                    <td>{{@$count['pollHistoriesCount']}}</td>
                                    <td>{{@$count['buyHistoriesCount']}}</td>
                                    <td>{{@$count['winHistoriesCount']}}</td>
                                    <td>{{@$count['wesiteLinkHistoriesCount']}}</td>
                                </tr>
                                <tr class="text-center">
                                    <th>Spend</th>
                                    <th class="text-left">${{number_format((float)$totalHistory, 3, '.', '')}}</th>
                                    <td>${{number_format((float)$sum['messageHistoriesSum'], 3, '.', '')}}</td>
                                    <td>${{number_format((float)$sum['infoHistoriesSum'], 3, '.', '')}}</td>
                                    <td>${{number_format((float)$sum['distributionHistoriesSum'], 3, '.', '')}}</td>
                                    <td>${{number_format((float)$sum['pollHistoriesSum'], 3, '.', '')}}</td>
                                    <td>${{number_format((float)$sum['buyHistoriesSum'], 3, '.', '')}}</td>
                                    <td>${{number_format((float)$sum['winHistoriesSum'], 3, '.', '')}}</td>
                                    <td>${{number_format((float)$sum['wesiteLinkHistoriesSum'], 3, '.', '')}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript" src="{{asset('backend/js/bootstrapValidator.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/vendors/moment/moment.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/js/plugins/daterangepicker/datetimepicker.min.js')}}"></script>

    <script type="text/javascript">
        var bindDateRangeValidation = function (f, s, e) {
            if(!(f instanceof jQuery)){
                    console.log("Not passing a jQuery object");
            }

            var jqForm = f,
                startDateId = s,
                endDateId = e;

            var checkDateRange = function (startDate, endDate) {
                var isValid = (startDate != "" && endDate != "") ? startDate <= endDate : true;
                return isValid;
            }

            var bindValidator = function () {
                var bstpValidate = jqForm.data('bootstrapValidator');
                var validateFields = {
                    startDate: {
                        validators: {
                            notEmpty: { message: 'This field is required.' },
                            callback: {
                                message: 'Start Date must less than or equal to End Date.',
                                callback: function (startDate, validator, $field) {
                                    return checkDateRange(startDate, $('#' + endDateId).val())
                                }
                            }
                        }
                    },
                    endDate: {
                        validators: {
                            notEmpty: { message: 'This field is required.' },
                            callback: {
                                message: 'End Date must greater than or equal to Start Date.',
                                callback: function (endDate, validator, $field) {
                                    return checkDateRange($('#' + startDateId).val(), endDate);
                                }
                            }
                        }
                    },
                    customize: {
                        validators: {
                            customize: { message: 'customize.' }
                        }
                    }
                }
                if (!bstpValidate) {
                    jqForm.bootstrapValidator({
                        excluded: [':disabled'],
                    })
                }

                jqForm.bootstrapValidator('addField', startDateId, validateFields.startDate);
                jqForm.bootstrapValidator('addField', endDateId, validateFields.endDate);

            };

            var hookValidatorEvt = function () {
                var dateBlur = function (e, bundleDateId, action) {
                    jqForm.bootstrapValidator('revalidateField', e.target.id);
                }

                $('#' + startDateId).on("dp.change dp.update blur", function (e) {
                    $('#' + endDateId).data("DateTimePicker").setMinDate(e.date);
                    dateBlur(e, endDateId);
                });

                $('#' + endDateId).on("dp.change dp.update blur", function (e) {
                    $('#' + startDateId).data("DateTimePicker").setMaxDate(e.date);
                    dateBlur(e, startDateId);
                });
            }

            bindValidator();
            hookValidatorEvt();
        };

        $(function () {
            var sd = new Date(), ed = new Date();

            $('#startDate').datetimepicker({
                pickTime: false,
                format: "YYYY/MM/DD",
                defaultDate: moment().startOf('month'),
                maxDate: ed
            });

            $('#endDate').datetimepicker({
                pickTime: false,
                format: "YYYY/MM/DD",
                defaultDate: moment().endOf('month'),
                minDate: sd
            });

            //passing 1.jquery form object, 2.start date dom Id, 3.end date dom Id
            bindDateRangeValidation($("#form"), 'startDate', 'endDate');
        });
        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);
            if(urlParams.get('country_id') > 0){
                getCity(urlParams.get('country_id'), urlParams.get('city_id'));
            }

            $('#country_id').on('change', function() {
                var country_id = this.value;
                getCity(country_id, null);
            });
        });

        
        function getCity(country_id, city_id) {
            $("#city_id").html('');
            var url = "{{ route('get-countryBased-city') }}";

            $.ajax({
                url:url,
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: '{{csrf_token()}}'
                },
                dataType : 'json',
                success: function(result){
                    $('#city_id').html('<option value="">--Select City--</option>');
                    $.each(result.cities,function(key,value){
                        if(value.id == city_id){
                            $("#city_id").append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                        }else{
                            $("#city_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                        }
                    });
                }
            });
        }
    </script>
@endsection
