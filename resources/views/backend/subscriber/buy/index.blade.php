@extends('layouts.backend.subscriber.main')
@section('title', 'Shop')
@section('content')

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Shop</h4>
                <h4 class="card-title text-right">
                    <a class="btn btn-success btn-sm btn-xs-block" href="{{route('buy.create', [request()->route('brandId')])}}">Add Shop</a>
                </h4>
                @if(isset($buys) && count($buys) > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php  $i =  1;  @endphp
                        <tbody>
                            @foreach($buys as $row)
                                <tr>
                                    <td>{{@$row->link}}</td>
                                    <td>
                                        @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                                        {!! Form::open(['route' => ['buy.destroy',$row->id],'onsubmit'=>"return confirmDelete(this,'Are you sure to want delete ?')",'id'=>'formBuyDel'.$row->id]) !!}
                                            <a class="fa fa-address-book"  href="{{route('buy.edit',[$id, request()->route('brandId')])}}" title="Update Shop">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            @method('delete')
                                            <a class="fa fa-address-book"  type="submit" onclick="$('#formBuyDel{{$row->id}}').submit()" title="Delete Shop">
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
                    <h6>Add a Shop</h6>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
