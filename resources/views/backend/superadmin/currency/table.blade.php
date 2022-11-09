@if(isset($currency) && count($currency) > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="text-left">
                    <th>Name</th>
                    <th>Symbol </th>
                    <th>Code</th>
                    <th>Rate</th>
                    {{-- <th>Action</th> --}}
                </tr>
            </thead>
            @php  $i =  1;  @endphp
            <tbody>
                @foreach($currency as $row)
                    @if (@$row->code == "USD")
                        @continue
                    @endif
                    <tr class="text-left">
                        <td>{{@$row->name}}</td>
                        <td>{{@$row->symbol}}</td>
                        <td>{{@$row->code}}</td>
                        <td>{{number_format((float)$row->rate, 3, '.', '')}}</td>
                        {{-- <td>
                            @php $id= App\Helpers\CustomHelper::getEncrypted($row->id); @endphp
                            <a class="ml-3 text-decoration-none"  href="{{route('currency.edit',[$id])}}" title="View Subsciber"> 
                                <i class="ti-settings icon"></i>
                            </a>
                        </td> --}}
                    </tr>
                @endforeach
                {{-- <tr>
                    <td colspan="8">{{ $currency->render('vendor.default_paginate') }}</td>
                </tr> --}}
            </tbody>
        </table>
    </div>
@else
    <h6>No currency found</h6>
@endif
