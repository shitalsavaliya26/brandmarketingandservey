@extends('layouts.backend.subscriber.main')
@section('title', 'Update Brand')
@section('css')
    <style>
    .select2-container--default .select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        cursor: text;
        padding-bottom: 6px;
        padding-right: 5px;
        position: relative;
      }
    </style>
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
                        <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                        <div class="p-1 bd-highlight mt-2"><a class="text-decoration-none text-dark"><h6 class="mt-1">Edit Brand</h5></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Brand</h4>
                @php $id= App\Helpers\CustomHelper::getEncrypted($brand->id); @endphp
                {!! Form::model($brand,['route' => ['brand.update',$id],'autocomplete'=>'false','files'=>true,'id'=>'brand-form-edit']) !!}
                    @method('patch')
                    @include('backend.subscriber.brand.singleeditform')
                {!! Form::close() !!}       
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    var brandExits = "{{ route('brandExits') }}";
    var group_id = "{{ $brand->group_id }}";

    $(document).on('change', '#country_id', function() {
        if($(this).val().length > 0){
            $("#replicadiv").removeClass("d-none");
        }
        else{
            $("#replicadiv").addClass("d-none");
            // $("input[name=replica_brand_id]").prop('checked', false)

            $("input[name=replica_brand_id] option:selected").prop("selected", false)
        }
    });

    $(function() {
        var val = $('#country_id').val();
        if(val.length > 0){
            $("#replicadiv").removeClass("d-none");
        }
        else{
            $("#replicadiv").addClass("d-none");
            // $("input[name=replica_brand_id]").prop('checked', false)

            $("input[name=replica_brand_id] option:selected").prop("selected", false)
        }
    });

       /*
              Define the adapter so that it's reusable
        */
        $.fn.select2.amd.define('select2/selectAllAdapter', [
            'select2/utils',
            'select2/dropdown',
            'select2/dropdown/attachBody'
        ], function(Utils, Dropdown, AttachBody) {
            function SelectAll() {}
            SelectAll.prototype.render = function(decorated) {
                var self = this,
                    $rendered = decorated.call(this),
                    $selectAll = $(
                        '<button class="btn btn-xs btn-default" type="button" style="margin-left:6px;"><i class="fa fa-check-square-o"></i> Select All</button>'
                    ),
                    $unselectAll = $(
                        '<button class="btn btn-xs btn-default" type="button" style="margin-left:6px;"><i class="fa fa-square-o"></i> Unselect All</button>'
                    ),
                    $btnContainer = $('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
                if (!this.$element.prop("multiple")) {
                    return $rendered;
                }
                $rendered.find('.select2-dropdown').prepend($btnContainer);
                $selectAll.on('click', function(e) {
                    // var spinner = $('#loader');
                    // spinner.show();
                    var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                    $results.each(function() {
                        self.trigger('select', {
                            data: $(this).data('data')
                        });
                    });
                    self.trigger('close');
                    // spinner.hide();
                });
                $unselectAll.on('click', function(e) {
                    var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                    $results.each(function() {
                        self.trigger('unselect', {
                            data: $(this).data('data')
                        });
                    });
                    self.trigger('close');
                });
                return $rendered;
            };

            return Utils.Decorate(
                Utils.Decorate(
                    Dropdown,
                    AttachBody
                ),
                SelectAll
            );

        });

        $('#country_id').select2({
            placeholder: 'Select Country',
            dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
        });
</script>
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection
