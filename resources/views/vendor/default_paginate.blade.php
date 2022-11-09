@if ($paginator->lastPage() > 1)
@php $link_limit=4 @endphp
<ul class="pagination flex-wrap " style="justify-content: end;">
    <li class="page-item {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
        <a class="page-link" href="{{ $paginator->url(1) }}"><i class="ti-angle-left"></i></a>
    </li>

     {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <li class="disabled page-item"><span class="page-link">{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @php $i = 0 @endphp
            @foreach ($element as $page => $url)
                @if($i == $link_limit)
                    @php //continue; @endphp
                @endif
                @if ($page == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
                @php $i++; @endphp
            @endforeach
        @endif
    @endforeach
    <li class="page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
        <a  class="page-link" href="{{ $paginator->url($paginator->currentPage()+1) }}" ><i class="ti-angle-right"></i></a>
    </li>
</ul>
@endif
