<div>
    <div class="min-h-screen bg-gray-50 py-12 mt-24">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Tryout Saya</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan semua tryout yang pernah Anda ikuti atau beli di sini.
                </p>
            </div>

            {{-- Filter Section --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                    {{-- Search Input --}}
                    <div class="flex-1 w-full lg:w-auto">
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.500ms="search"
                                placeholder="Cari tryout saya..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Filter Buttons & Dropdowns --}}
                    <div class="flex flex-col sm:flex-row flex-wrap gap-4 w-full lg:w-auto items-center">
                        
                        {{-- ✨ BARU: Dropdown Filter Kategori (Umum/Khusus) ✨ --}}
                        <div class="w-full lg:w-auto">
                            <select
                                wire:model.live="category"
                                wire:loading.attr="disabled"
                                class="w-full lg:w-auto px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                            >
                                <option value="all">Semua Kategori</option>
                                <option value="umum">Kategori Umum</option>
                                <option value="khusus">Kategori Khusus</option>
                            </select>
                        </div>

                        {{-- Tombol Filter Tipe --}}
                        <div class="flex flex-wrap gap-2 w-full lg:w-auto border-t sm:border-t-0 sm:border-l border-gray-200 pt-4 sm:pt-0 sm:pl-4">
                            <button
                                wire:click="setFilter('all')"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-full text-sm font-medium {{ $filter === 'all' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                            >
                                Semua
                            </button>

                            <button
                                wire:click="setFilter('hots')"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-full text-sm font-medium {{ $filter === 'hots' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                            >
                                HOTS <i class="fas fa-fire ml-1"></i>
                            </button>

                            <button
                                wire:click="setFilter('regular')"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-full text-sm font-medium {{ $filter === 'regular' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                            >
                                Regular
                            </button>
                        </div>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="w-full lg:w-auto">
                        <select
                            wire:model.live="sort"
                            wire:loading.attr="disabled"
                            class="w-full lg:w-auto px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                            <option value="latest">Terbaru Dibeli</option>
                            <option value="purchased_date">Lama Dibeli</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters --}}
                @if($search || $filter !== 'all' || $category !== 'all' || $sort !== 'latest')
                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-gray-600">Filter aktif:</span>

                    @if($search)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center">
                        Pencarian: "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-2 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    {{-- ✨ BARU: Badge Filter Aktif Kategori ✨ --}}
                    @if($category !== 'all')
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full flex items-center">
                        Kategori: {{ ucfirst($category) }}
                        <button wire:click="$set('category', 'all')" class="ml-2 text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if($filter !== 'all')
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full flex items-center">
                        Tipe: {{ $filter === 'hots' ? 'HOTS' : 'Regular' }}
                        <button wire:click="$set('filter', 'all')" class="ml-2 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if($sort !== 'latest')
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full flex items-center">
                        Urutan:
                        @if($sort === 'purchased_date') Lama Dibeli
                        @else Terbaru Dibeli
                        @endif
                        <button wire:click="$set('sort', 'latest')" class="ml-2 text-purple-600 hover:text-purple-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    <button
                        wire:click="resetFilters"
                        class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center"
                    >
                        <i class="fas fa-times mr-1"></i> Hapus Semua Filter
                    </button>
                </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="contents">
                @if($tryouts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                        @foreach($tryouts as $tryout)
                            @php
                                $progress = $tryout->user_progress;
                                // Perbaikan: Gunakan isset($progress) untuk cek apakah progress ada
                                $allCompleted = isset($progress) && $tryout->last_completed_id && ($progress->is_completed ?? true) && ($progress->attempt >= $tryout->max_attempt);
                                
                                // Variabel penolong untuk status utama
                                $currentAttempt = $progress->attempt ?? 1;
                                $isCompleted = isset($progress) && $progress->is_completed;
                                $isStarted = isset($progress) && $progress->started_at && !$isCompleted;
                            @endphp
                            
                            {{-- CARD UTAMA: Tambahkan flex flex-col h-full untuk konsistensi tinggi --}}
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden relative p-5 flex flex-col h-full">

                                {{-- WADAH KONTEN FLEKSIBEL: flex-grow akan memastikan bagian ini mengisi semua ruang vertikal yang tersisa --}}
                                <div class="flex-grow flex flex-col justify-start">
                                    
                                    {{-- KONTENER TITLE & HOTS BADGE --}}
                                    <div class="flex flex-col items-start gap-2 mb-3">
                                        <div class="flex flex-col gap-1 min-w-0 w-full">
                                            <h3 class="font-bold text-lg text-gray-800 line-clamp-2">
                                                {{ $tryout->title }}
                                            </h3>
                                            
                                            {{-- Baris Status Percobaan (Diberi h-5 untuk tinggi yang konsisten) --}}
                                            <div class="h-5"> 
                                                @if($allCompleted)
                                                    <span class="text-sm font-semibold text-purple-600">Semua Percobaan Selesai</span>
                                                @elseif(isset($progress) && $progress)
                                                    <span class="text-sm font-semibold {{ $isCompleted ? 'text-purple-600' : 'text-primary' }}">
                                                        Percobaan ke-{{ $currentAttempt }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            {{-- ✨ BARU: Badge Kategori Umum/Khusus ✨ --}}
                                            <span class="px-2 py-0.5 rounded text-xs font-bold flex-shrink-0 {{ $tryout->category === 'khusus' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ ucfirst($tryout->category) }}
                                            </span>

                                            @if($tryout->is_hots)
                                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold flex-shrink-0 flex items-center">
                                                    HOTS <i class="fas fa-fire ml-1"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tanggal Pembelian (dari pivot yang relevan) --}}
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3 text-sm mt-3">
                                        <div class="flex items-center text-green-700">
                                            <i class="fas fa-calendar-check mr-2"></i>
                                            <span class="font-semibold">Tanggal Pembelian:</span>
                                        </div>
                                        <div class="text-green-600 mt-1">
                                            {{ $progress->purchased_at->translatedFormat('d F Y') ?? 'Belum ada tanggal' }}
                                        </div>
                                    </div>

                                    {{-- Progress atau Status --}}
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                                        <div class="flex justify-between items-center text-sm text-blue-700 mb-2">
                                            <span class="font-semibold">Status:</span>
                                            <span class="bg-blue-100 px-2 py-1 rounded-full text-xs font-medium">
                                                @if($allCompleted)
                                                    <i class="fas fa-trophy mr-1"></i> SEMUA SELESAI
                                                @elseif($isStarted)
                                                    <i class="fas fa-play-circle mr-1"></i> Dalam Progress
                                                @elseif($isCompleted)
                                                    <i class="fas fa-check-circle mr-1"></i> Selesai
                                                @else
                                                    <i class="fas fa-clock mr-1"></i> Belum Dimulai
                                                @endif
                                            </span>
                                        </div>
                                        
                                        {{-- Baris Waktu Mulai (Diberi h-4 untuk tinggi yang konsisten) --}}
                                        <div class="text-xs text-blue-600 h-4">
                                            @if($isStarted)
                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                Dimulai: {{ $progress->started_at->diffForHumans() }}
                                            @else
                                                &nbsp; {{-- Placeholder untuk menjaga tinggi --}}
                                            @endif
                                        </div>
                                    </div>

                                    {{-- JUMLAH SOAL --}}
                                    <p class="text-left text-gray-600 text-sm mb-4">
                                        <i class="fas fa-file-alt mr-1"></i> Jumlah Soal: {{ $tryout->active_questions_count }}
                                    </p>

                                </div> {{-- End of flex-grow content --}}

                                {{-- TOMBOL AKSI: flex-shrink-0 memastikan bagian ini tidak menyusut --}}
                                <div class="flex flex-col gap-3 mt-4 flex-shrink-0">
                                    {{-- TOMBOL UTAMA (MULAI/LANJUTKAN/LIHAT HASIL) --}}
                                    @if($allCompleted)
                                        <a href="{{ route('tryout.my-results', ['tryout' => $tryout->slug]) }}"
                                        class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg">
                                            <i class="fas fa-chart-bar mr-2"></i> Lihat Hasil Akhir
                                        </a>
                                    @elseif($isStarted)
                                        <a href="{{ route('tryout.start', ['tryout' => $tryout->slug, 'attempt' => $currentAttempt]) }}"
                                        class="flex items-center justify-center bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg">
                                            <i class="fas fa-play-circle mr-2"></i> Lanjutkan Percobaan {{ $currentAttempt }}
                                        </a>
                                    @elseif($isCompleted)
                                        <a href="{{ route('tryout.my-results', ['tryout' => $tryout->slug]) }}"
                                        class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg">
                                            <i class="fas fa-chart-bar mr-2"></i> Lihat Hasil Percobaan {{ $currentAttempt }}
                                        </a>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="confirmStart({{ $tryout->id }})" 
                                            class="flex w-full items-center justify-center bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg disabled:opacity-75 disabled:cursor-wait"
                                        >
                                            Mulai Percobaan {{ $currentAttempt }}
                                        </button>
                                    @endif

                                    {{-- TOMBOL DETAIL --}}
                                    <a href="{{ route('tryout.detail', ['tryout' => $tryout->slug]) }}"
                                    class="flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm">
                                        <i class="fas fa-info-circle mr-2"></i> Detail Tryout
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="flex justify-center">
                        <div class="bg-white rounded-2xl shadow-lg p-4">
                            {{ $tryouts->links() }}
                        </div>
                    </div>

                    <div class="text-center mt-6 text-gray-600">
                        <p>Menampilkan {{ $tryouts->count() }} dari total {{ $tryouts->total() }} tryout.</p>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-12">
                        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                            <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Tryout yang Dibeli</h3>
                            <p class="text-gray-600 mb-4">Sepertinya Anda belum memiliki tryout. Jelajahi pilihan tryout kami dan tingkatkan persiapan Anda sekarang juga!</p>
                            <a href="{{ route('tryout.index') }}" class="bg-primary text-white px-6 py-2 rounded-full font-medium hover:bg-primary-dark">
                                <i class="fas fa-search mr-2"></i> Jelajahi Tryout
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Hak Cipta - Alpine Transitions Dihapus --}}
        <div
            x-data="{ showModal: false }"
            x-on:show-copyright-modal.window="showModal = true"
            x-on:keydown.escape.window="showModal = false"
            x-show="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            style="display: none;">
            <div
                x-show="showModal"
                x-on:click.outside="showModal = false"
                class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
            >
                <div class="bg-gray-50 px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-lg sm:text-xl"></i>
                        PERINGATAN HAK CIPTA
                    </h2>
                </div>

                <div class="p-4 sm:p-6 text-gray-700 space-y-4 max-h-[80vh] overflow-y-auto"> 
                    <p class="text-sm">
                        Seluruh konten di dalam website ini, termasuk soal, jawaban, dan materi pembelajaran, adalah karya intelektual yang dilindungi oleh Undang-Undang Nomor 28 Tahun 2014 tentang Hak Cipta.
                    </p>

                    <div>
                        <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                            <i class="fas fa-times-circle text-red-600 mr-2"></i>
                            Pelanggaran yang dianggap sebagai pelanggaran hak cipta:
                        </h3>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                            <li>Menyalin atau menggandakan soal tanpa izin.</li>
                            <li>Mengganti nama, angka, atau pilihan jawaban lalu mempublikasikannya kembali.</li>
                            <li>Menghapus watermark atau metadata dalam file.</li>
                            <li>Menjual soal hasil salinan tanpa izin.</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                            <i class="fas fa-gavel text-yellow-600 mr-2"></i>
                            Sanksi yang dapat dikenakan:
                        </h3>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                            <li>Pidana penjara paling lama 4 tahun atau denda hingga Rp1 miliar.</li>
                            <li>Pelanggaran hak ekonomi dapat dikenakan denda hingga Rp500 juta.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button
                        type="button"
                        x-on:click="showModal = false"
                        class="px-3 py-2 sm:px-5 sm:py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="startTryout"
                        wire:loading.attr="disabled"
                        wire:target="startTryout"
                        x-on:click="showModal = false" 
                        class="px-3 py-2 sm:px-5 sm:py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-wait inline-flex items-center"
                    >
                        <span wire:loading.remove wire:target="startTryout">
                            Saya Mengerti & Lanjutkan
                        </span>
                        <span wire:loading wire:target="startTryout" class="flex items-center">
                            <div class="h-4 w-4 border-b-2 border-white mr-2"></div>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .grid { view-transition-name: my-tryout-grid; }
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0.5rem;
    }
    .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        color: #6b7280;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .page-link:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }
</style>
@endpush