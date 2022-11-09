@extends('layouts.backend.subscriber.main')
@section('title', 'Notification Topic - Summary Cost')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('notification-topic.index',request()->route('brandId'))}}"class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Notification Topics</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Notification Topic - Summary Cost</h5></a></div>
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
                    <div>
                        <h4 class="card-title" style="margin-bottom:0.9rem">{{ $brand->name }} ({{ $brand->country->name }}) Notification Topic - Summary Cost</h4>
                        <h6><b>Total Message Distributed : {{count($transactionHistory) }}</b></h6>
                    </div>
                    <h4 class="card-title text-right">
                        <a class="btn btn-light" href="{{route('notification-topic.index', [request()->route('brandId')])}}">Back</a>
                    </h4>
                </div>
                @if(isset($transactionHistory) && count($transactionHistory) > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-left">Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            @php  $i =  1;  @endphp
                            <tbody>
                                @foreach($transactionHistory  as $row)
                                <tr class="text-center">
                                    <td class="text-left">{{date('d-m-Y h:m:s', strtotime($row->created_at))}}</td>
                                    <td>{{ $symbol }} {{number_format((float)$row->amount, 3, '.', '')}}</td>
                                </tr>
                                @endforeach
                                <tr class="text-center">
                                    <td class="text-left"><b>Count</b></td>
                                    <td><b> {{ $symbol }} {{number_format((float)$transactionHistory->sum('amount'), 3, '.', '')}}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="5">{{ $transactionHistory->render('vendor.default_paginate') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <h6>No history found</h6>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
