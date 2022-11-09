@if(isset($subscribers) && count($subscribers) > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="text-center">
                    <th class="text-left">Organization name</th>
                    <th>Currency</th>
                    <th>Total Account Balance</th>
                    <th>Total Spend</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            @php  $i =  1;  @endphp
            <tbody>
                @foreach($subscribers as $row)
                    <tr class="text-center">

                       
                        <td class="text-left">{{@$row->organization_name ?? "-"}}</td>
                        <td>{{@$row->currency->symbol}} {{@$row->currency->code ?? "-"}}</td>
                        <td>${{number_format((float)$row->wallet_amount_usd, 3, '.', '')}}</td>
                        <td>${{number_format((float)$row->spend, 3, '.', '')}}</td>
                        <td>{{@$row->country->name ?? "-"}}</td>

                        @if (empty($row->organization_name) && empty($row->contact_number) && empty($row->country_id ) && empty($row->calling_code))
                        <td>
                            <input name="status" class="toggle-class" disabled = "disabled" type="checkbox" data-onstyle="warning" data-toggle="toggle" data-on="Pending" data-off="Pending" data-offstyle="warning" >
                        </td>
                        @else
                        <td>
                            <input name="status" id="status{{$row->id}}" onChange="changeStatus({{$row->id}}, {{$row->status}});" data-id="{{$row->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $row->status== '1' ? 'checked' : '' }}>
                        </td>
                        @endif

                        <td>
                            @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                            <a class="ml-3 text-decoration-none"  href="{{route('subscriber.show',[$id])}}" title="View Subsciber"> 
                                <i class="ti-eye icon"></i>
                            </a>
                            <a class="ml-3 text-decoration-none"  href="{{route('subscriber.login-using-id',[$id])}}" title="Login to Subscriber Dashboard"> 
                                <i class="ti-unlock icon"></i>
                            </a>
                            <!-- <a class="ml-3 text-decoration-none" data-toggle="modal" data-target="#messageModal" data-id="{{$row->id}}"  title="Change Password"> 
                                <i class="ti-lock icon"></i>
                            </a> -->
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="8">{{ $subscribers->render('vendor.default_paginate') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <h6>No subscriber found</h6>
@endif
