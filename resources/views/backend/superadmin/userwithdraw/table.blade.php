@if(isset($withdraws) && count($withdraws) > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="text-center">
                    {{-- <th class="text-left">Bank Name</th>
                    <th>Branch Name</th>
                    <th>Account Holder Name</th>
                    <th>Account Number</th> --}}
                    <th>Name</th>
                    <th>Paypal email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            @php  $i =  1;  @endphp
            <tbody>
                
                @foreach($withdraws as $row)
                    <tr class="text-center">
                        {{-- <td class="text-left">{{@$row->bank_name ?? "-"}}</td>
                        <td>{{@$row->branch_name  ?? "-"}}</td>
                        <td>{{@$row->acc_holder_name  ?? "-"}}</td>
                        <td>{{@$row->acc_number  ?? "-"}}</td> --}}
                        <td>{{@$row->user->firstname." ".@$row->user->lastname ?? "-"}}</td>
                        <td>{{@$row->paypal_email ?? "-"}}</td>
                        <td>{{ isset($row->currencyname->symbol)?$row->currencyname->symbol:"$" }} {{number_format((float)$row->amount, 3, '.', '')}}</td>
                        <td>
                        @if($row->status== 'approved')
                            <span class="bg-success text-white topup-status">Success</span>
                        @elseif($row->status== 'pending')
                            <span class="bg-warning text-white topup-status">Pending</span>
                        @else
                            <span class="bg-danger text-white topup-status">Rejected</span>
                        @endif
                        </td>
                        <td>
                            @if($row->status== 'pending')
                                <span class="ti-check bg-success text-white topup-status user-change-status" id="approve" data-id="{{$row->id}}">Approve</span>
                                <span class="ti-close bg-danger text-white topup-status user-change-status" id="refuse" data-id="{{$row->id}}">Refuse</span>
                            @endif
                        </td>
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