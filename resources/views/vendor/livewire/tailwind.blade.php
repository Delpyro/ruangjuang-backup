@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            {{-- Tampilan Mobile (Sederhana) --}}
            <div class="flex justify-between flex-1 sm:hidden">
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border-2 border-gray-500 cursor-default rounded-lg">
                        Sebelumnya
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        Sebelumnya
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        Selanjutnya
                    </button>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-gray-100 border-2 border-gray-500 cursor-default rounded-lg">
                        Selanjutnya
                    </span>
                @endif
            </div>

            {{-- Tampilan Desktop --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-end">
                
                <div>
                    <p class="text-sm text-gray-700 leading-5 dark:text-gray-400 mr-4">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md gap-1.5">
                        
                        {{-- Tombol Previous --}}
                        @if ($paginator->onFirstPage())
                            <span aria-disabled="true" aria-label="Previous">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border-2 border-gray-500 cursor-not-allowed rounded-lg" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left w-4 h-4 flex items-center justify-center"></i>
                                </span>
                            </span>
                        @else
                            <button wire:click="previousPage" dusk="previousPage" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" aria-label="Previous">
                                <i class="fa-solid fa-chevron-left w-4 h-4 flex items-center justify-center"></i>
                            </button>
                        @endif

                        {{-- Elemen Angka Pagination --}}
                        {{-- Elemen Angka Pagination Custom --}}
                        @php
                            $currentPage = $paginator->currentPage();
                            $lastPage = $paginator->lastPage();
                            $onEachSide = 2; // Menentukan jumlah 3 ke kiri dan 3 ke kanan
                            $customElements = [];

                            // Jika total halaman sedikit, tampilkan semuanya tanpa titik-titik
                            if ($lastPage < ($onEachSide * 2) + 6) {
                                $customElements[] = array_combine(range(1, $lastPage), range(1, $lastPage));
                            } else {
                                // Tentukan batas mulai dan batas akhir window
                                $windowStart = max(2, $currentPage - $onEachSide);
                                $windowEnd = min($lastPage - 1, $currentPage + $onEachSide);

                                // Selalu masukkan halaman pertama
                                $customElements[] = [1 => 1];

                                // Beri titik-titik jika ada gap antara halaman 1 dan window start
                                if ($windowStart > 2) {
                                    $customElements[] = '...';
                                }

                                // Masukkan halaman-halaman di dalam range window
                                $customElements[] = array_combine(range($windowStart, $windowEnd), range($windowStart, $windowEnd));

                                // Beri titik-titik jika ada gap antara window end dan halaman terakhir
                                if ($windowEnd < $lastPage - 1) {
                                    $customElements[] = '...';
                                }

                                // Selalu masukkan halaman terakhir
                                $customElements[] = [$lastPage => $lastPage];
                            }
                        @endphp

                        @foreach ($customElements as $element)
                            {{-- Pemisah Tiga Titik "..." --}}
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border-2 border-gray-500 cursor-default rounded-lg">{{ $element }}</span>
                                </span>
                            @endif

                            {{-- Array Link Halaman --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-blue-600 border border-blue-600 cursor-default rounded-lg shadow-md">{{ $page }}</span>
                                        </span>
                                    @else
                                        <button wire:click="gotoPage({{ $page }})" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" aria-label="Go to page {{ $page }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Tombol Next --}}
                        @if ($paginator->hasMorePages())
                            <button wire:click="nextPage" dusk="nextPage" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" aria-label="Next">
                                <i class="fa-solid fa-chevron-right w-4 h-4 flex items-center justify-center"></i>
                            </button>
                        @else
                            <span aria-disabled="true" aria-label="Next">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border-2 border-gray-500 cursor-not-allowed rounded-lg" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-right w-4 h-4 flex items-center justify-center"></i>
                                </span>
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
