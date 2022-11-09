<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{ $brandgroup->name }} Brand All Countries List</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @foreach ($brands as $keyid => $brand)
        {{ $brand->country->name }}
        @if ($loop->last)
            .
        @else
            ,
        @endif
    @endforeach
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
