@extends('layouts.backend.subscriber.main')
@section('title', 'Win')
@section('content')

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Win</h4>
                @if(isset($wins) && count($wins) < 1)
                    <h4 class="card-title text-right">
                        <a class="btn btn-success btn-sm btn-xs-block" href="{{route('win.create', [request()->route('brandId')])}}">Add Win</a>
                    </h4>
                @endif
                @if(isset($wins) && count($wins) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Attachment Type</th>
                                <th>Attachment </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($wins as $row)
                                <tr>
                                    <td>{{ ucfirst(@$row->attachment_type)}}</td>
                                    <td>
                                        @if(@$row->attachment_type == 'image')
                                            <a href="{{asset('uploads/win/'.$row->brand_id.'.pdf')}}" target="_blank">
                                                View Attachments
                                            </a>
                                        @elseif(@$row->attachment_type == 'link')
                                            <a href="{{$row->attachment}}" target="_blank">
                                                View Attachments
                                            </a>
                                        @elseif(@$row->attachment_type == 'pdf')
                                            <a href="{{asset('uploads/win/'.$row->attachment)}}" target="_blank">
                                                View Attachments
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                                        {!! Form::open(['route' => ['win.destroy',$row->id],'onsubmit'=>"return confirmDelete(this,'Are you sure to want delete ?')",'id'=>'formWinDel'.$row->id]) !!}
                                            <a class="fa fa-address-book"  href="{{route('win.edit',[$id, request()->route('brandId')])}}" title="Update Win">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            @method('delete')
                                            <a class="fa fa-address-book"  type="submit" onclick="$('#formWinDel{{$row->id}}').submit()" title="Delete Win">
                                                <i class="ti-trash"></i>
                                            </a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>Add a win</h6>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
