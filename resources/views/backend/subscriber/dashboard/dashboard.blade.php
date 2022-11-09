@extends('layouts.backend.subscriber.main')
@section('title', 'Dashboard')
@section('css')
    <link rel="stylesheet" href="{{asset('backend/css/plugins/daterangepicker/datetimepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/bootstrapValidator.min.css')}}">
    <style>
        .alerts {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
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
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                @if((auth()->user()->wallet_amount + auth()->user()->bonus_amount) <= 0)
                    <div class="alerts alert-warning" role="alert">
                        <strong>Your wallet balance is low, To Top Up Please  <a href="{{ route('topup.index') }}" style="color:#4B49AC">Click Here</a></strong>
                    </div>
                @endif
                @if(auth()->user()->pause_account == 1)
                    <div class="alerts alert-warning">
                        <strong>Your account is paused.</strong>
                    </div>
                @endif
                <h4 class="card-title">Dashboard</h4>
                <div class="col-12">
                    <div class="row">
                        {!! Form::open(['autocomplete'=>'false','id'=>'dahboard-form','method'=>'get']) !!}
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label class="">Brand</label>
                                            <select class="form-control" id="group_id" name="group_id">
                                                <option value="null">--Select Brand--</option>
                                                <option value="null">All</option>
                                                {{-- @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}" @if(app('request')->input('brand_id') == $brand->id) selected @endif>{{$brand->name}} ({{$brand->country->name}})</option>
                                                @endforeach --}}
                                                @foreach($brandgroups as $brand)
                                                <option value="{{$brand->id}}" @if(app('request')->input('group_id') == $brand->id) selected @endif>{{$brand->name}}</option>
                                                 @endforeach
                                            </select>
                                            <span class="help-block text-danger">{{ $errors->first('group_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label class="">Country</label>
                                            <select class="form-control" id="country_id" name="country_id">
                                                <option value="null">--Select country--</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}" @if(app('request')->input('country_id') == $country->id) selected @endif>{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                            <span class="help-block text-danger">{{ $errors->first('country_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label class="">City</label>
                                            <select class="form-control" id="city_id" name="city_id">
                                            </select>
                                            <span class="help-block text-danger">{{ $errors->first('city_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label class="">Start Date</label>
                                            <input id="startDate" name="startDate" type="text" class="form-control" value="{{ app('request')->input('startDate') }}" autocomplete="off" />
                                            <span class="help-block text-danger">{{ $errors->first('startDate') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label class="">End Date</label>
                                            <input id="endDate" name="endDate" type="text" class="form-control" value="{{ app('request')->input('endDate') }}" autocomplete="off" />
                                            <span class="help-block text-danger">{{ $errors->first('endDate') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary" id="search" style="margin-top: 35px;">Search</button>
                                    <a href="{{route('subscriber.dashboard')}}" class="btn btn-success" id="clear" style="margin-top: 35px;">Clear</a>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Monthly Summary</h4>
                <div class="col-12">
                    @if(isset($priceSetting) && count($priceSetting) > 0)
                        <div class="table-responsive">
                            <table id="transactionHistory" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-left">Total</th>
                                        @foreach($priceSetting as $row)
                                            @if ($row->title == "User Poll Winning Price" || $row->title == "User Withdrawal Setting")
                                                @continue
                                            @endif
                                            <th class="text-center">                  
                                                @if ($row->title == "Poll Question")
                                                    Poll (Per Question)
                                                @else
                                                    {{  $row->title }}
                                                @endif                                            
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr class="text-center">
                                        <th>Cost</th>
                                        <!-- <td class="text-left">${{$priceSetting->sum('price')}}</td> -->
                                        <td class="text-left"></td>
                                            @foreach($priceSetting as $row)
                                                @if ($row->title == "User Poll Winning Price" || $row->title == "User Withdrawal Setting")
                                                    @continue
                                                @endif
                                                <td class="text-center"> {{ $symbol }} {{ number_format((float)@$row->price*$currencyrate, 3, '.', '')}}</td>
                                            @endforeach
                                        </th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Qty</th>
                                        <td class="text-left"> {{$countHistory}}</td>
                                        {{-- <td>{{count($messageHistories)}}</td>
                                        <td>{{count($infoHistories)}}</td>
                                        <td>{{count($distributionHistories)}}</td>
                                        <td>{{count($pollHistories)}}</td>
                                        <td>{{count($buyHistories)}}</td>
                                        <td>{{count($winHistories)}}</td>
                                        <td>{{count($wesiteLinkHistories)}}</td> --}}
                                        <td>{{$count['messageHistoriesCount']}}</td>
                                        <td>{{$count['infoHistoriesCount']}}</td>
                                        <td>{{ $count['distributionHistoriesCount'] }}</td>
                                        <td>{{$count['pollHistoriesCount'] }}</td>
                                        <td>{{ $count['buyHistoriesCount']}}</td>
                                        <td>{{ $count['winHistoriesCount']}}</td>
                                        <td>{{ $count['wesiteLinkHistoriesCount']}}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Spend</th>
                                        <td class="text-left"> {{ $symbol }} {{number_format((float)$totalHistory, 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$messageHistories->sum('amount'), 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$infoHistories->sum('amount'), 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$distributionHistories->sum('amount'), 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$pollHistories->sum('amount'), 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$buyHistories->sum('amount'), 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$winHistories->sum('amount'), 3, '.', '')}}</td>
                                        <td> {{ $symbol }} {{number_format((float)$wesiteLinkHistories->sum('amount'), 3, '.', '')}}</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">History</h4>
                <figure class="highcharts-figure">
                    <div id="container"></div>
                </figure>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript" src="{{asset('backend/js/bootstrapValidator.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/vendors/moment/moment.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/js/plugins/daterangepicker/datetimepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/js/plugins/highchart/highcharts.js')}}"></script>

    <script type="text/javascript">

        var symbol = "{{ $symbol }}";
        
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
                // defaultDate: moment().startOf('month'),
                maxDate: ed
            });

            $('#endDate').datetimepicker({
                pickTime: false,
                format: "YYYY/MM/DD",
                // defaultDate: moment().endOf('month'),
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
        messagingGraph = {!! str_replace("'", "\'", json_encode($messagingGraph)) !!};
        infoGraph = {!! str_replace("'", "\'", json_encode($infoGraph)) !!};
        infoDistributeGraph = {!! str_replace("'", "\'", json_encode($infoDistributeGraph)) !!};
        pollGraph = {!! str_replace("'", "\'", json_encode($pollGraph)) !!};
        shopGraph = {!! str_replace("'", "\'", json_encode($shopGraph)) !!};
        winGraph = {!! str_replace("'", "\'", json_encode($winGraph)) !!};
        weblinkGraph = {!! str_replace("'", "\'", json_encode($weblinkGraph)) !!};
        dates = {!! str_replace("'", "\'", json_encode($dates)) !!};
        var dates = Object.values(dates);
        dates = Object.keys(dates).length > 0 ? dates : [moment().format("DD-MM-YYYY")];

        messagingGraph = messagingGraph.length > 0 ? messagingGraph : [[moment().format("DD-MM-YYYY"), 0]];
        infoGraph = infoGraph.length > 0 ? infoGraph : [[moment().format("DD-MM-YYYY"), 0]];
        infoDistributeGraph = infoDistributeGraph.length > 0 ? infoDistributeGraph : [[moment().format("DD-MM-YYYY"), 0]];
        pollGraph = pollGraph.length > 0 ? pollGraph : [[moment().format("DD-MM-YYYY"), 0]];
        shopGraph = shopGraph.length > 0 ? shopGraph : [[moment().format("DD-MM-YYYY"), 0]];
        winGraph = winGraph.length > 0 ? winGraph : [[moment().format("DD-MM-YYYY"), 0]];
        weblinkGraph = weblinkGraph.length > 0 ? weblinkGraph : [[moment().format("DD-MM-YYYY"), 0]];
        
        Highcharts.chart('container', {
            title: {
                text: ''
            },

            yAxis: {
                min: 0,
                title: {
                    text: symbol+' Amount'
                }
            },

            xAxis: {
                min: 0,
                title: {
                    text: 'Date'
                },
                categories: dates
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                    connectorAllowed: false
                    },
                },
            },

            series: [
                {
                    name: 'Messaging '+symbol,
                    data: messagingGraph
                }, {
                    name: 'Info '+symbol,
                    data: infoGraph
                }, {
                    name: 'Info Distribute '+symbol,
                    data: infoDistributeGraph
                }, {
                    name: 'Poll Question'+symbol,
                    data: pollGraph
                }, {
                    name: 'Shop '+symbol,
                    data: shopGraph
                }, {
                    name: 'Win '+symbol,
                    data: winGraph
                }, {
                    name: 'Web Link '+symbol,
                    data: weblinkGraph
                },
            ],

            responsive: {
                rules: [{
                    condition: {
                    maxWidth: 500
                    },
                    chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                    }
                }]
            }
        });
    </script>
@endsection