@if ($paginator->hasPages())
<nav aria-label="Page navigation">
    <ul class="pagination mb-0">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled">
            <span class="page-link">«</span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="#"
                wire:click.prevent="setPage({{ $paginator->currentPage() - 1 }}, '{{ $paginator->getPageName() }}')">«</a>
        </li>
        @endif

        {{-- Pages --}}
        @php
        $start = max(1, $paginator->currentPage() - 2);
        $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
        @endphp

        @if($start > 1)
        <li class="page-item">
            <a class="page-link" href="#" wire:click.prevent="setPage(1, '{{ $paginator->getPageName() }}')">1</a>
        </li>
        @if($start > 2)
        <li class="page-item disabled"><span class="page-link">...</span></li>
        @endif
        @endif

        @for($page = $start; $page <= $end; $page++) <li
            class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
            <a class="page-link" href="#"
                wire:click.prevent="setPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ $page }}</a>
            </li>
            @endfor

            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="#"
                            wire:click.prevent="setPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')">{{
                            $paginator->lastPage() }}</a>
                    </li>
                    @endif

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="#"
                            wire:click.prevent="setPage({{ $paginator->currentPage() + 1 }}, '{{ $paginator->getPageName() }}')">»</a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link">»</span>
                    </li>
                    @endif
    </ul>
</nav>
@endif