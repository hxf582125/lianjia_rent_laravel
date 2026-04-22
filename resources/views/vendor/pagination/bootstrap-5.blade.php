@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="small text-muted mb-2 mb-md-0">
            共 <span class="fw-semibold text-primary">{{ $paginator->total() }}</span> 条记录，
            当前显示第 <span class="fw-semibold text-primary">{{ $paginator->firstItem() }}</span> -
            <span class="fw-semibold text-primary">{{ $paginator->lastItem() }}</span> 条
        </div>

        <div class="d-flex align-items-center gap-2">
            <ul class="pagination pagination-sm mb-0">
                {{-- First Page Link --}}
                @if (!$paginator->onFirstPage())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}" title="首页">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" title="首页">
                            <i class="bi bi-chevron-double-left"></i>
                        </span>
                    </li>
                @endif

                {{-- Previous Page Link --}}
                @if (!$paginator->onFirstPage())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="上一页">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" title="上一页">
                            <i class="bi bi-chevron-left"></i>
                        </span>
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
                                <li class="page-item active" aria-current="page">
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
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="下一页">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" title="下一页">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif

                {{-- Last Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="尾页">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" title="尾页">
                            <i class="bi bi-chevron-double-right"></i>
                        </span>
                    </li>
                @endif
            </ul>

            {{-- Jump to Page --}}
            <div class="d-flex align-items-center gap-1 ms-2">
                <span class="small text-muted">跳转</span>
                <form class="d-flex align-items-center m-0" action="{{ url()->current() }}" method="GET">
                    @foreach (request()->query() as $key => $value)
                        @if ($key !== 'page')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="number" name="page" min="1" max="{{ $paginator->lastPage() }}" 
                           class="form-control form-control-sm" style="width: 60px;"
                           placeholder="页">
                    <button type="submit" class="btn btn-sm btn-primary ms-1">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                <span class="small text-muted">页</span>
            </div>
        </div>
    </nav>
@endif
