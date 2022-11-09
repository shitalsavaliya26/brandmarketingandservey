@extends('layouts.backend.subscriber.main')
@section('title', 'Top Up History')
@section('css')
<style>
    .topup-status {
        padding: 6px 12px;
        border-radius: 15px;
    }
</style>
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('topup.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Top Up History</h5></a></div>
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
                    <h4 class="card-title">Top Up History</h4>
                    <h4 class="card-title text-right">
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('topup.create')}}">Top Up</a>
                    </h4>   
                </div>
                @if(isset($topups) && count($topups) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th width="15%" class="text-left">Amount</th>
                                <th width="15%">Transaction ID</th>
                                <th width="15%">Status</th>
                                <th width="50%">Reason</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($topups as $row)
                                <tr class="text-center">
                                    <td class="text-left">{{ $symbol }}{{number_format((float)$row->amount, 3, '.', '')}}</td>
                                    <td>{{@$row->transaction_id}}</td>
                                    <td>
                                    @if($row->status== '1' )
                                        <span class="bg-success text-white topup-status">Success</span>
                                    @else
                                        <span class="bg-danger text-white topup-status">Failed</span>
                                    @endif
                                    </td>
                                    <td>{{$row->reason == '' ? '-' : @$row->reason}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6">{{ $topups->render('vendor.default_paginate') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>Add a Top Up</h6>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
