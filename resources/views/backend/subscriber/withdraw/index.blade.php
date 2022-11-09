@extends('layouts.backend.subscriber.main')
@section('title', 'Withdraw History')
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
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('withdraw.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Withdraw History</h5></a></div>
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
                    <h4 class="card-title">Withdraw History</h4>
                    <h4 class="card-title text-right">
                        @if(auth()->user()->wallet_amount > 0)
                            <a class="btn btn-success btn-sm btn-xs-block" href="{{route('withdraw.create')}}">Withdraw</a>
                        @endif
                    </h4>   
                </div>
                @if(isset($withdraws) && count($withdraws) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th class="text-left">Bank Name</th>
                                <th>Branch Name</th>
                                <th>Account Holder Name</th>
                                <th>Account Number</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($withdraws as $row)
                                <tr class="text-center">
                                    <td class="text-left">{{@$row->bank_name}}</td>
                                    <td>{{@$row->branch_name}}</td>
                                    <td>{{@$row->acc_holder_name}}</td>
                                    <td>{{@$row->acc_number}}</td>
                                    <td class="text-left">{{ $symbol }} {{number_format((float)$row->amount, 3, '.', '')}}</td>

                                    <td>
                                    @if($row->status== '1')
                                        <span class="bg-success text-white topup-status">Success</span>
                                    @elseif($row->status== '2')
                                        <span class="bg-warning text-white topup-status">Pending</span>
                                    @else
                                        <span class="bg-danger text-white topup-status">Rejected</span>
                                    @endif
                                    </td>
                                    <td>{{@$row->reason}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="7">{{ $withdraws->render('vendor.default_paginate') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>No withdraw history found!</h6>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
