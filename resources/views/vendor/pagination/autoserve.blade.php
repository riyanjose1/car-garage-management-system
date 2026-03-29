@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Mobile --}}
        <div class="flex flex-1 justify-between sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/40">
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                    Prev
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                    Next
                </a>
            @else
                <span class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/40">
                    Next
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between gap-4">
            <div class="text-sm text-white/60">
                Showing
                <span class="text-white">{{ $paginator->firstItem() }}</span>
                to
                <span class="text-white">{{ $paginator->lastItem() }}</span>
                of
                <span class="text-white">{{ $paginator->total() }}</span>
                results
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/40">
                        ←
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white hover:bg-white/10 transition"
                       aria-label="Previous page">
                        ←
                    </a>
                @endif

                {{-- Page Numbers --}}
                <div class="flex items-center gap-1">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-2 text-sm text-white/40">{{ $element }}</span>
                        @endif

                        {{-- Array of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-sm text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/70 hover:bg-white/10 hover:text-white transition"
                                       aria-label="Go to page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white px-3 py-2 text-sm font-medium text-black hover:bg-white/90 transition"
                       aria-label="Next page">
                        →
                    </a>
                @else
                    <span class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/40">
                        →
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
