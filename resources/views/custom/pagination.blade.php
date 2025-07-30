@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center text-muted">
            <small>
                Показано {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} 
                из {{ $paginator->total() }} записей
            </small>
        </div>
        
        <ul class="pagination pagination-modern mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
        
        @if($paginator->hasPages())
            <div class="d-flex align-items-center">
                <small class="text-muted me-2">Страница</small>
                <select class="form-select form-select-sm pagination-page-select" style="width: auto;" onchange="window.location.href = this.value">
                    @for($i = 1; $i <= $paginator->lastPage(); $i++)
                        <option value="{{ $paginator->url($i) }}" {{ $i == $paginator->currentPage() ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                <small class="text-muted ms-2">из {{ $paginator->lastPage() }}</small>
            </div>
        @endif
    </nav>
@endif
