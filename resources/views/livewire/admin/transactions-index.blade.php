<div class="container mx-auto px-4 py-6">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Ringkasan Transaksi Tryout & Bundle</h2>
    
    {{-- AREA RINGKASAN GLOBAL GRATIS VS BERBAYAR --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-money-bill-wave text-blue-500 text-2xl mr-3"></i>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Total Transaksi Berbayar</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($totalGlobalPaid) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-gift text-green-500 text-2xl mr-3"></i>
                <div>
                    <p class="text-sm font-semibold text-green-800">Total Transaksi Gratis</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($totalGlobalFree) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tambahkan Input Search --}}
    <div class="mb-6">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama Tryout atau Bundle..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        />
    </div>
    
    {{-- AREA KHUSUS BADGE TRYOUT TERLARIS (Papan Pengumuman) --}}
    @if (!empty($tryoutSummaries) && !$search && $topSalesCount > 0)
        @php
            $topItem = $tryoutSummaries[0];
            $topIsTryout = $topItem['type'] == 'tryout';
            $topBorderColor = $topIsTryout ? 'border-indigo-600' : 'border-purple-600';
        @endphp

        <div class="mb-6 p-4 bg-white border-2 {{ $topBorderColor }} rounded-xl shadow-lg flex justify-between items-center transition-opacity duration-500">
            <div class="flex items-center">
                <i class="fa-solid fa-trophy text-yellow-500 text-3xl mr-3"></i>
                <div class="text-sm">
                    <span class="font-bold text-gray-700 block">ITEM PALING LAKU (Terlaris Saat Ini)</span>
                    
                    {{-- MENAMPILKAN DAFTAR SEMUA JUDUL ITEM YANG SERI --}}
                    <div class="text-xl font-extrabold text-gray-900 leading-snug">
                        @foreach ($topSellerTitles as $title)
                            <span class="block text-base leading-tight">{{ $title }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <span class="text-md text-gray-500 block">Total Terjual</span>
                <span class="text-3xl font-extrabold text-red-600">
                    {{ number_format($topSalesCount) }}
                </span>
                <a wire:click="goToDetail('{{ $topItem['type'] }}', {{ $topItem['id'] }})"
                   class="text-xs text-blue-600 hover:text-blue-800 cursor-pointer font-semibold mt-1 block">
                    Lihat Detail Transaksi
                </a>
            </div>
        </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Tambahkan kondisi loading --}}
        <div wire:loading.delay class="md:col-span-3 text-center py-12">
            <i class="fa-solid fa-spinner fa-spin text-indigo-500 text-3xl"></i>
            <p class="mt-2 text-gray-600">Memuat ringkasan...</p>
        </div>
        
        @forelse ($tryoutSummaries as $summary)
            @php
                $isTryout = $summary['type'] == 'tryout';
                $iconClass = $isTryout ? 'fa-solid fa-file-signature text-indigo-500' : 'fa-solid fa-box-open text-purple-500';
                $borderColor = $isTryout ? 'border-indigo-500' : 'border-purple-500';
                $buttonClass = $isTryout ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-purple-600 hover:bg-purple-700';

                // Tentukan apakah item ini adalah bagian dari Terlaris (Seri di penjualan tertinggi)
                $isTopSeller = ($summary['total_sales'] == $topSalesCount) && ($topSalesCount > 0);
            @endphp

            <div class="relative">
                
                {{-- BADGE TERLARIS --}}
                @if ($isTopSeller)
                    <div class="absolute -top-3 right-0 z-10">
                        <span class="px-3 py-1 text-xs font-bold text-white bg-red-600 rounded-lg shadow-xl uppercase transform rotate-2">
                            🔥 TERLARIS
                        </span>
                    </div>
                @endif
                
                {{-- KARTU UTAMA --}}
                <div class="flex flex-col h-full bg-white rounded-xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 border-t-8 {{ $borderColor }}"
                     wire:key="{{ $summary['type'] }}-{{ $summary['id'] }}">
                    
                    <div class="flex justify-between items-start mb-3">
                        <p class="text-xs font-semibold uppercase text-gray-500 tracking-wider">
                            <i class="{{ $iconClass }} mr-1"></i> {{ ucfirst($summary['type']) }}
                        </p>
                    </div>

                    <h3 class="text-lg font-extrabold text-gray-900 mb-2 line-clamp-2 flex-grow">
                        {{ $summary['title'] }}
                    </h3>
                    
                    <hr class="my-3 border-gray-100">

                    {{-- Area Utama Angka Terjual dengan Split --}}
                    <div class="text-center py-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-medium text-gray-500 mb-1">Total Item Terjual</p>
                        <p class="text-4xl font-extrabold text-gray-800">
                            {{ number_format($summary['total_sales']) }}
                        </p>
                        
                        {{-- Pemisah Gratis vs Berbayar --}}
                        <div class="flex justify-center items-center gap-4 mt-3 pt-3 border-t border-gray-200 text-sm">
                            <div class="text-center">
                                <span class="block text-xs font-semibold text-green-600">Gratis</span>
                                <span class="font-bold text-gray-700">{{ number_format($summary['total_free']) }}</span>
                            </div>
                            <div class="h-6 w-px bg-gray-300"></div>
                            <div class="text-center">
                                <span class="block text-xs font-semibold text-blue-600">Berbayar</span>
                                <span class="font-bold text-gray-700">{{ number_format($summary['total_paid']) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button wire:click="goToDetail('{{ $summary['type'] }}', {{ $summary['id'] }})"
                                class="w-full text-center py-2 text-sm font-medium text-white {{ $buttonClass }} rounded-lg transition-colors duration-200 shadow-md">
                            Lihat Detail Transaksi
                            <i class="fa-solid fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
                
            </div>
        @empty
            <div wire:loading.remove class="md:col-span-3 text-center py-12 bg-gray-50 rounded-xl">
                <i class="fa-solid fa-box-open text-gray-400 text-5xl mb-3"></i>
                <p class="text-lg text-gray-600">
                    @if ($this->search)
                        Tidak ada Tryout/Bundle yang cocok dengan "{{ $this->search }}".
                    @else
                        Belum ada transaksi Tryout atau Bundle yang berhasil.
                    @endif
                </p>
            </div>
        @endforelse
    </div>
</div>