@extends('layouts.backend.subscriber.main')
@section('title', 'Poll Result')
@section('css')
<link href="{{ asset('css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection
@section('content')
@php $bid= App\Helpers\CustomHelper::getEncrypted($pollResult->brand_id) @endphp
@php $mid= App\Helpers\CustomHelper::getEncrypted($pollResult->poll_title_id) @endphp
<div class="row">
    <div class="col-md-12 grid-margin mb-0">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="d-flex flex-row bd-highlight mb-3">
                    <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a href="{{ route('polltitle.index',[$bid])}}"class="text-decoration-none text-dark"><h6 class="mt-1">{{ $brand->name }} ({{ $brand->country->name }}) Polls</h5></a></div>
                            <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                            <div class="p-1 bd-highlight mt-2"><a href="{{ route('poll.index',[$mid])}}" class="text-decoration-none text-dark"><h6 class="mt-1">{{ $mainpoll['title'] ?? "" }} Questions</h5></a></div>
                                <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                                <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">[ {{ $mainpoll['title'] }} ] Poll Result</h5></a></div>
                        
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success" role="alert" style="display:none;">
                </div>
                <div class="alert alert-danger" role="alert" style="display:none;">
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    @php 
                    $main_id = App\Helpers\CustomHelper::getEncrypted($mainpoll['id']);
                    @endphp
                    <h4 class="card-title">[ {{ $mainpoll['title'] }} ] Poll Result</h4>
                    <h4 class="card-title text-right">
                    </h4>
                    <a class="mx-3 btn btn-warning btn-sm btn-xs-block" href="{{route('poll.index', [$main_id])}}"><i class="ti-arrow-left menu-icon"></i> Back</a>
                </div>
                @if(isset($pollResult))
                <label><b>Question - {{@$pollResult->question}}</b></label>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th width="80%" class="text-left">Option</th>
                                <th width="10%">Percentage</th>
                                <th width="10%">Vote</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody class="text-center">
                            @foreach($pollResult->options as $row)
                                <tr>
                                    <td class="text-left">{{@$row->option}}</td>
                                    <td>{{@$row->percent}}</td>
                                    <td>{{@$row->vote}}</td>
                                </tr>
                            @endforeach
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
