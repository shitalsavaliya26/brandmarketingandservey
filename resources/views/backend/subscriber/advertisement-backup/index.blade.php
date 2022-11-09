@extends('layouts.backend.subscriber.main')
@section('title', 'Advertisements')
@section('content')

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Advertisements</h4>
                <h4 class="card-title text-right">
                    <a class="btn btn-success btn-sm btn-xs-block" href="{{route('advertisement.create')}}">Add Advertisement</a>
                </h4>
                @if(isset($advertisements) && count($advertisements) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Brand Name</th>
                                <th>Images</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($advertisements as $row)
                                <tr>
                                    <td>{{@$row->brand->name}}</td>
                                    <td>
                                        @foreach($row->advertisementImages as $image)
                                            <a class="blueimp-link" href="{{$image->image}}" data-gallery="" target="_blank">
                                                <img onerror="this.src='{{asset('backend/images/no-found.png')}}'" src="{{$image->image}}" width="auto" height="50px">
                                            </a>
                                        @endforeach
                                    </td>
                                    <td>
                                        @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                                        {!! Form::open(['route' => ['advertisement.destroy',$row->id],'onsubmit'=>"return confirmDelete(this,'Are you sure to want delete ?')",'id'=>'formAdvertisementDel'.$row->id]) !!}
                                        <a class=""  href="{{route('advertisement.edit',[$id])}}">Edit</a> |
                                        @method('delete')
                                        <a class=""  type="submit" onclick="$('#formAdvertisementDel{{$row->id}}').submit()" >Delete</>
                                            {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <h6>No records Found!</h6>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
