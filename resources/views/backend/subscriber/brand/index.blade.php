@extends('layouts.backend.subscriber.main')
@section('title', 'Brands')
@section('css')
    <style>
        .vl {
            border-left: 2px solid #007bff;
            height: 20px;
        }

        .subtoggle .toggle.btn {
            min-width: 84px;
            min-height: 34px;
        }

        .read-more-show {
            cursor: pointer;
            color: #ed8323;
        }

        .read-more-hide {
            cursor: pointer;
            color: #ed8323;
        }

        .hide_content {
            display: none;
        }

        .mdi-plus-circle {
            color: #57B657;
        }

        .mdi-minus {
            color: #e64942;
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
                    <div class="p-1 bd-highlight mt-2"><a href="{{ route('brand.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Brands</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body" id="brandslist">
                    <div class="alert alert-success" role="alert" style="display:none;">
                    </div>
                    <div class="alert alert-danger" role="alert" style="display:none;">
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Brands</h4>
                        <h4 class="card-title text-right">
                            <a class="btn btn-success btn-sm btn-xs-block" href="{{ route('brand.create') }}">Add Brand</a>
                        </h4>
                    </div>
                    @if (isset($brands) && count($brands) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th width="5%"></th>
                                        <th width="20%" class="text-left">Name</th>
                                        {{-- <th width="20%">Logo</th> --}}
                                        <th width="20%">Country</th>
                                        <th width="20%">Total Spend</th>
                                        <th width="20%">Status</th>
                                        <th width="20%">Action</th>
                                    </tr>
                                </thead>
                                @php  $i =  1;  @endphp
                                <tbody class="text-center">
                                    @foreach ($brands as $key => $rowmain)
                                        {{-- check grater than 1 country --}}
                                        @if (count($rowmain->brands) > 1)
                                            <tr>
                                                <td class="text-left panel-title">
                                                    <a href="javascript:void(0)" data-toggle="collapse"
                                                        data-target="#brandrow{{ $key }}" class="accordion-toggle">
                                                        <i class="mdi mdi-plus-circle mdi-24px"></i>
                                                    </a>
                                                </td>

                                                <td class="text-left">
                                                    {{ $rowmain->name }}
                                                </td>

                                                {{-- <td>
                                                    <a class="blueimp-link" href="{{ $rowmain->logo }}" data-gallery=""
                                                        target="_blank">
                                                        <img onerror="this.src='{{ asset('backend/images/no-found.png') }}'"
                                                            src="{{ $rowmain->logo }}" width="auto" height="50px">
                                                    </a>
                                                </td> --}}


                                                <td>
                                                    <p>
                                                    @php
                                                        $totalcount = count($rowmain->brands);
                                                    @endphp

                                                    @foreach ($rowmain->brands as $keyid => $rowcountry)
                                                        @break($keyid > 2)
                                                        {{ $rowcountry->country->name }}
                                                        @if ($loop->last)
                                                            .
                                                        @elseif($totalcount > 3)
                                                            @if ($keyid == 2)
                                                                <p data-groupcountry="{{ $rowmain->id }}"
                                                                    class="text-success viewmorecountry" style="cursor: pointer;">viewmore </p>
                                                            @else
                                                                ,
                                                            @endif
                                                        @else
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </p>
                                                </td>

                                                <td>
                                                    {{ $symbol }}{{ number_format($rowmain->spend, 3, '.', '') }}
                                                </td>

                                                <td>
                                                    <input name="status" id="groupstatus{{ $rowmain->id }}"
                                                        onChange="changegroupStatus({{ $rowmain->id }}, {{ $rowmain->status }});"
                                                        data-id="{{ $rowmain->id }}" class="toggle-class" type="checkbox"
                                                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                                                        data-on="Active" data-off="InActive"
                                                        {{ $rowmain->status == '1' ? 'checked' : '' }}>
                                                </td>

                                                <td class="text-right">
                                                    @php $mainid= App\Helpers\CustomHelper::getEncrypted($rowmain->id) @endphp
                                                    <a class="ml-3 text-decoration-none"
                                                        href="{{ route('main-brand.edit', [$mainid]) }}"
                                                        title="Update Brand">
                                                        <i class="ti-pencil icon"></i>
                                                    </a>

                                                    <a class="ml-3 text-decoration-none deletemainbrand"
                                                        href="javascript:void(0)"
                                                        data-url="{{ route('main-brand.delete', [$mainid]) }}"
                                                        data-brand={{ @$rowmain->name }} title="Delete Brand">
                                                        <i class="ti-trash icon"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" class="p-0">
                                                    <div class="accordion-body collapse table-responsive"
                                                        id="brandrow{{ $key }}"
                                                        style="background-color:rgba(87, 182, 87, 0.2);">
                                                        <table class="table mx-3" id="table-{{ $key }}">
                                                            <thead>
                                                            <tr class="text-center">
                                                                <th width="5%"></th>
                                                                <th width="20%" class="text-left">Name</th>
                                                                {{-- <th width="20%">Logo</th> --}}
                                                                <th width="20%">Country</th>
                                                                <th width="20%">Total Spend</th>
                                                                <th width="20%">Status</th>
                                                                <th width="20%">Action</th>
                                                                <th width="5%"></th>
                                                                <th width="5%"></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="text-center">
                                                                @foreach ($rowmain->brands as $key => $row)
                                                                    <tr>
                                                                        <td colspan="1"></td>
                                                                        <td class="text-left">{{ @$row->name }}</td>
                                                                        {{-- <td>
                                                                            <a class="blueimp-link"
                                                                                href="{{ $row->logo }}" data-gallery=""
                                                                                target="_blank">
                                                                                <img onerror="this.src='{{ asset('backend/images/no-found.png') }}'"
                                                                                    src="{{ $row->logo }}"
                                                                                    width="auto" height="50px">
                                                                            </a>
                                                                        </td> --}}
                                                                        <td>
                                                                            {{ $row->country->name }}
                                                                        </td>
                                                                        <td>{{ $symbol }}
                                                                            {{ number_format((float) $row->spend_amount, 3, '.', '') }}
                                                                        </td>
                                                                        <td class="subtoggle">
                                                                            <input name="status"
                                                                                id="status{{ $row->id }}"
                                                                                onChange="changeStatus({{ $row->id }}, {{ $row->status }});"
                                                                                data-id="{{ $row->id }}"
                                                                                class="toggle-class subgroupstatus{{ $rowmain->id }}"
                                                                                type="checkbox" data-onstyle="success"
                                                                                data-offstyle="danger" data-toggle="toggle"
                                                                                data-on="Active" data-off="InActive"
                                                                                {{ $row->status == '1' ? 'checked' : '' }}>
                                                                        </td>
                                                                        <td>
                                                                            @php $id= App\Helpers\CustomHelper::getEncrypted($row->id) @endphp
                                                                            {!! Form::open([
                                                                                'route' => ['brand.destroy', $row->id],
                                                                                'class' => 'd-flex align-items-center justify-content-center',
                                                                                'onsubmit' => "return confirmDelete(this,'Are you sure to want delete ?')",
                                                                                'id' => 'formBrandDel' . $row->id,
                                                                            ]) !!}
                                                                            <a class="text-decoration-none @if (count($row->topics) == 0) icon-red @endif"
                                                                                href="{{ route('topic.index', [$id]) }}"
                                                                                title="Topic">
                                                                                <i class='ti-comments icon'></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none @if (count($row->mainpolls) == 0) icon-red @endif"
                                                                                href="{{ route('polltitle.index', [$id]) }}"
                                                                                title="Poll">
                                                                                <i class="ti-bar-chart-alt icon"></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none @if (count($row->notificationTopics) == 0) icon-red @endif"
                                                                                href="{{ route('notification-topic.index', [$id]) }}"
                                                                                title="Notification Topic">
                                                                                <i class="ti-bell icon"></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none @if ($row->buy == null || $row->buy == '') icon-red @endif"
                                                                                href="{{ route('buy.index', [$id]) }}"
                                                                                title="Shopping">
                                                                                <i class="ti-shopping-cart icon"></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none @if ($row->win == null || $row->win == '') icon-red @endif"
                                                                                href="{{ route('win.index', [$id]) }}"
                                                                                title="Win">
                                                                                <i class="ti-crown icon"></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none @if ($row->advert == null || $row->advert == '' || count($row->advert) <= 0) icon-red @endif"
                                                                                href="{{ route('advertisement.edit', [$id]) }}"
                                                                                title="Advertisement">
                                                                                <i class="ti-desktop icon"></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none"
                                                                                href="{{ route('setting.index', [$id]) }}"
                                                                                title="Function Settings">
                                                                                <i class="ti-settings icon"></i>
                                                                            </a>
                                                                            <a class="ml-3 text-decoration-none"
                                                                                href="javascript:void(0);">
                                                                                <i class="vl"></i>
                                                                            </a>
                                                                            {{-- <a class="ml-3 text-decoration-none"
                                                                            href="{{ route('brand.edit', [$id]) }}"
                                                                            title="Update Brand">
                                                                            <i class="ti-pencil icon"></i>
                                                                        </a> --}}

                                                                          <a class="ml-3 text-decoration-none"
                                                                            href="{{ route('sinlecollapsebrandedit', [$id]) }}"
                                                                            title="Update Brand">
                                                                            <i class="ti-pencil icon"></i>
                                                                        </a>

                                                                            @method('delete')
                                                                            <a class="ml-3 text-decoration-none deletesubbrand"
                                                                                href="javascript:void(0)"
                                                                                data-url="{{ route('brand.destroy', [$id]) }}"
                                                                                data-country={{ @$row->country->name }}
                                                                                data-brand={{ @$row->name }}
                                                                                title="Delete Brand">
                                                                                <i class="ti-trash icon"></i>
                                                                            </a>
                                                                            {!! Form::close() !!}
                                                                        </td>
                                                                        {{-- <td colspan="1"></td> --}}
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </td>
                                            </tr>
                                        @else
                                            {{-- only one country --}}
                                            @foreach ($rowmain->brands as $key => $row)
                                                <tr>
                                                    <td></td>
                                                    <td class="text-left">{{ @$row->name }}</td>
                                                    {{-- <td>
                                                        <a class="blueimp-link" href="{{ $row->logo }}"
                                                            data-gallery="" target="_blank">
                                                            <img onerror="this.src='{{ asset('backend/images/no-found.png') }}'"
                                                                src="{{ $row->logo }}" width="auto" height="50px">
                                                        </a>
                                                    </td> --}}
                                                    <td>
                                                        {{ $row->country->name }}
                                                    </td>
                                                    <td>{{ $symbol }}
                                                        {{ number_format((float) $row->spend_amount, 3, '.', '') }}
                                                    </td>
                                                    <td class="subtoggle">
                                                        <input name="status" id="status{{ $row->id }}"
                                                            onChange="changeStatus({{ $row->id }}, {{ $row->status }});"
                                                            data-id="{{ $row->id }}"
                                                            class="toggle-class subgroupstatus{{ $rowmain->id }}"
                                                            type="checkbox" data-onstyle="success" data-offstyle="danger"
                                                            data-toggle="toggle" data-on="Active" data-off="InActive"
                                                            {{ $row->status == '1' ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        @php $id= App\Helpers\CustomHelper::getEncrypted($row->id) @endphp
                                                        {!! Form::open([
                                                            'route' => ['brand.destroy', $row->id],
                                                            'class' => 'd-flex align-items-center justify-content-center',
                                                            'onsubmit' => "return confirmDelete(this,'Are you sure to want delete ?')",
                                                            'id' => 'formBrandDel' . $row->id,
                                                        ]) !!}
                                                        <a class="text-decoration-none @if (count($row->topics) == 0) icon-red @endif"
                                                            href="{{ route('topic.index', [$id]) }}" title="Topic">
                                                            <i class='ti-comments icon'></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none @if (count($row->mainpolls) == 0) icon-red @endif"
                                                            href="{{ route('polltitle.index', [$id]) }}" title="Poll">
                                                            <i class="ti-bar-chart-alt icon"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none @if (count($row->notificationTopics) == 0) icon-red @endif"
                                                            href="{{ route('notification-topic.index', [$id]) }}"
                                                            title="Notification Topic">
                                                            <i class="ti-bell icon"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none @if ($row->buy == null || $row->buy == '') icon-red @endif"
                                                            href="{{ route('buy.index', [$id]) }}" title="Shopping">
                                                            <i class="ti-shopping-cart icon"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none @if ($row->win == null || $row->win == '') icon-red @endif"
                                                            href="{{ route('win.index', [$id]) }}" title="Win">
                                                            <i class="ti-crown icon"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none @if ($row->advert == null || $row->advert == '' || count($row->advert) <= 0) icon-red @endif"
                                                            href="{{ route('advertisement.edit', [$id]) }}"
                                                            title="Advertisement">
                                                            <i class="ti-desktop icon"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none"
                                                            href="{{ route('setting.index', [$id]) }}"
                                                            title="Function Settings">
                                                            <i class="ti-settings icon"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none" href="javascript:void(0);">
                                                            <i class="vl"></i>
                                                        </a>
                                                        <a class="ml-3 text-decoration-none"
                                                            href="{{ route('brand.edit', [$id]) }}" title="Update Brand">
                                                            <i class="ti-pencil icon"></i>
                                                        </a>
                                                        @method('delete')
                                                        <a class="ml-3 text-decoration-none deletesubbrand"
                                                            href="javascript:void(0)"
                                                            data-url="{{ route('brand.destroy', [$id]) }}"
                                                            data-country={{ @$row->country->name }}
                                                            data-brand={{ @$row->name }} title="Delete Brand">
                                                            <i class="ti-trash icon"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="7">{{ $brands->render('vendor.default_paginate') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <h6>Add a brand</h6>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="countrylistModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modal-content">
                
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    <script type="text/javascript">
        function changeStatus(brand_id, status, reuse = null) {
            swal({
                    title: `Are you sure you want to change the status?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((change) => {
                    if (change) {
                        var url = "{{ route('brand.change-status') }}";
                        var spinner = $('#loader');
                        spinner.show();

                        $.ajax({
                            type: "GET",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "status": status,
                                "brand_id": brand_id,
                            },
                            success: function(data) {

                                spinner.hide()
                                $('html, body').animate({
                                    scrollTop: $("#brandslist").offset().top - 50
                                }, 500);


                                $(".alert-success").show();
                                $('.alert-success').html(data.success).fadeIn('slow');
                                $('.alert-success').delay(3000).fadeOut('slow');

                                setTimeout(function() {
                                    window.location.reload(true);
                                }, 2500);
                            }
                        });
                    } else {
                        if (status == 1) {
                            $('#status' + brand_id).parent('.toggle').removeClass('btn-danger off');
                            $('#status' + brand_id).parent('.toggle').addClass('btn-sucess');
                        } else {
                            $('#status' + brand_id).parent('.toggle').addClass('btn-danger off');
                            $('#status' + brand_id).parent('.toggle').removeClass('btn-sucess');
                        }

                    }
                });
        }





        function changegroupStatus(group_id, status, reuse = null) {
            swal({
                    title: `Are you sure you want to change the all countries status?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((change) => {
                    if (change) {
                        var url = "{{ route('brandgroup.change-status') }}";

                        var spinner = $('#loader');
                        spinner.show();

                        $.ajax({
                            type: "GET",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "status": status,
                                "group_id": group_id,
                            },
                            success: function(data) {

                                spinner.hide()
                                $('html, body').animate({
                                    scrollTop: $("#brandslist").offset().top - 50
                                }, 500);

                                $(".alert-success").show();
                                $('.alert-success').html(data.success).fadeIn('slow');
                                $('.alert-success').delay(3000).fadeOut('slow');

                                setTimeout(function() {
                                    window.location.reload(true);
                                }, 2500);
                            }
                        });
                    } else {
                        if (status == 1) {
                            $('#groupstatus' + group_id).parent('.toggle').removeClass('btn-danger off');
                            $('#groupstatus' + group_id).parent('.toggle').addClass('btn-sucess');
                        } else {
                            $('#groupstatus' + group_id).parent('.toggle').addClass('btn-danger off');
                            $('#groupstatus' + group_id).parent('.toggle').removeClass('btn-sucess');
                        }
                    }
                });
        }

        /* delete brand */
        $(document).on("click", ".deletesubbrand", function() {
            var url = $(this).attr("data-url");
            var country = $(this).attr("data-country");
            var brand = $(this).attr("data-brand");

            swal({
                    title: `Are you sure you want to delete ` + brand + ` brand in ` + country + `?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((change) => {
                    if (change) {

                        var spinner = $('#loader');
                        spinner.show();
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "country": country
                            },
                            success: function(data) {
                                location.reload();
                            }
                        });
                    }
                });
        });


        /* delete main brand */
        $(document).on("click", ".deletemainbrand", function() {
            var url = $(this).attr("data-url");
            var brand = $(this).attr("data-brand");

            swal({
                    title: `Are you sure you want to delete ` + brand + ` brand in all Countries ?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((change) => {
                    if (change) {

                        var spinner = $('#loader');
                        spinner.show();
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "brand": brand
                            },
                            success: function(data) {
                                location.reload();
                            }
                        });
                    }
                });
        });



        // Changes contributed by @diego-rzg
        $(document).on("click", ".viewmorecountry", function() {
            var groupid = $(this).attr("data-groupcountry");
            fetchcountrylist(groupid);
        });

        function fetchcountrylist(groupid) {

            var spinner = $('#loader');
            spinner.show();
            $.ajax({
                type: "GET",
                url: "{{ route('get_country_list') }}",
                cache: false,
                data: {
                    _token: $("input[name=_token]").val(),
                    groupid: groupid,
                },
                success: function(data) {

                    spinner.hide();
                    $("#countrylistModal").modal("show");
                    $("#countrylistModal .modal-body").empty();
                    $("#countrylistModal #modal-content").html(data.html);
                }
            });
        }
    </script>

@endsection
