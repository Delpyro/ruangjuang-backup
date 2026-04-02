<div class="min-h-screen bg-gray-50 py-12 mt-24">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Daftar Try Out</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto font-bold">
                Disarankan untuk mengerjakan try out secara berurutan, dimulai dari try out 1 hingga yang terakhir, agar grafik perkembangan skor terlihat lebih jelas dan terarah.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" data-aos="fade-up" data-aos-delay="100">
            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                {{-- Bagian Search --}}
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari try out..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

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

                    {{-- Tombol Filter Tipe (Semua, HOTS, Regular) --}}
                    <div class="flex flex-wrap gap-2 w-full lg:w-auto border-t sm:border-t-0 sm:border-l border-gray-200 pt-4 sm:pt-0 sm:pl-4">
                        <button
                            wire:click="setFilter('all')"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $filter === 'all' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            Semua
                        </button>

                        <button
                            wire:click="setFilter('hots')"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $filter === 'hots' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            HOTS <i class="fas fa-fire ml-1"></i>
                        </button>

                        <button
                            wire:click="setFilter('regular')"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ $filter === 'regular' ? 'bg-teal-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            Regular
                        </button>
                    </div>
                </div>

                <div class="w-full lg:w-auto">
                    <select
                        wire:model.live="sort"
                        wire:loading.attr="disabled"
                        class="w-full lg:w-auto px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                    >
                        <option value="latest">Terbaru</option>
                        <option value="price_asc">Harga Terendah</option>
                        <option value="price_desc">Harga Tertinggi</option>
                    </select>
                </div>
            </div>

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

                {{-- ✨ BARU: Badge Info Filter Kategori ✨ --}}
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
                    @if($sort === 'price_asc') Harga Terendah
                    @elseif($sort === 'price_desc') Harga Tertinggi
                    @else Terbaru
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

        <div class="contents">
            @if($tryouts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($tryouts as $tryout)
                        {{-- DEFINISI VARIABEL UNTUK HARGA --}}
                        @php
                            $is_discount_active = $tryout->discount > 0 
                                && ($tryout->discount_start_date === null || \Carbon\Carbon::parse($tryout->discount_start_date)->lte(\Carbon\Carbon::now())) 
                                && ($tryout->discount_end_date === null || \Carbon\Carbon::parse($tryout->discount_end_date)->gte(\Carbon\Carbon::now()));
                            $is_discount_upcoming = $tryout->discount > 0 && $tryout->discount_start_date && \Carbon\Carbon::parse($tryout->discount_start_date)->gt(\Carbon\Carbon::now());
                            $is_discount_expired = $tryout->discount > 0 && $tryout->discount_end_date && \Carbon\Carbon::parse($tryout->discount_end_date)->lt(\Carbon\Carbon::now());
                            // Asumsi ada properti is_bundle atau is_bundle_item di model Tryout
                            $is_bundle = property_exists($tryout, 'is_bundle') ? $tryout->is_bundle : false;
                        @endphp
                        
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 relative p-5 flex flex-col justify-between"
                            data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}"
                            wire:key="tryout-{{ $tryout->id }}-{{ $tryout->updated_at->timestamp }}">

                            {{-- START: CONTAINER JUDUL & BADGE --}}
                            <div class="mb-4">
                                {{-- Judul --}}
                                <div class="flex items-start gap-2 min-w-0 mb-1">
                                    <h3 class="font-bold text-lg text-gray-800 leading-tight line-clamp-2">
                                        {{ $tryout->title }}
                                    </h3>
                                </div>
                                
                                {{-- Baris Badge --}}
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    {{-- ✨ BARU: Badge Kategori Umum/Khusus ✨ --}}
                                    <span class="px-2 py-0.5 rounded text-xs font-bold flex-shrink-0 {{ $tryout->category === 'khusus' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($tryout->category) }}
                                    </span>

                                    @if($tryout->is_hots)
                                        <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs font-bold flex-shrink-0 flex items-center">
                                            HOTS <i class="fas fa-fire ml-1"></i>
                                        </span>
                                    @endif

                                    {{-- BADGE BUNDLE DITAMBAHKAN DI SINI --}}
                                    @if($is_bundle)
                                        <span class="bg-purple-600 text-white px-2 py-0.5 rounded text-xs font-semibold flex-shrink-0">
                                            BUNDLE <i class="fas fa-box-open ml-1"></i>
                                        </span>
                                    @endif
                                    
                                    @if($is_discount_active)
                                        <span class="bg-green-500 text-white px-2 py-0.5 rounded text-xs font-semibold flex-shrink-0">
                                            Hemat Rp {{ number_format($tryout->discount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- END: CONTAINER JUDUL & BADGE --}}
                            
                            <a
                                href="{{ route('tryout.detail', ['tryout' => $tryout->slug]) }}"
                                wire:navigate
                                class="text-blue-600 text-sm mb-4 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-4 py-2 rounded-lg flex justify-between items-center cursor-pointer transition duration-200"
                            >
                                <span>Fasilitas yang didapatkan</span>
                                <i class="fas fa-chevron-right text-blue-600 text-xs"></i>
                            </a>

                            {{-- START: CONTAINER HARGA FINAL (min-h-set) --}}
                            <div class="text-left mb-2 flex flex-col min-h-[60px] justify-start">

                                @if($is_discount_active)
                                    {{-- SKENARIO 1: DISKON AKTIF (3 baris info) --}}
                                    <div class="flex justify-start items-baseline space-x-2">
                                        <span class="text-2xl font-bold text-primary">Rp {{ number_format($tryout->final_price, 0, ',', '.') }}</span>
                                        {{-- PERUBAHAN 1: font-bold dihapus --}}
                                        <span class="text-black line-through text-sm">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="text-green-600 text-sm font-medium mt-1">Diskon {{ $tryout->discount_percentage }}%</p>
                                    
                                @elseif($is_discount_upcoming)
                                    {{-- SKENARIO 2: DISKON AKAN DATANG (3 baris info) --}}
                                    <span class="text-2xl font-bold text-primary">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                    {{-- Placeholder Baris 2: Transparan --}}
                                    <p class="text-sm font-medium mt-1 text-transparent invisible">Rp 00.000</p>
                                    <p class="text-sm text-blue-500 font-medium mt-1 flex items-center">
                                        <i class="fas fa-tags mr-1"></i> Mulai: {{ \Carbon\Carbon::parse($tryout->discount_start_date)->translatedFormat('d M Y H:i') }}
                                    </p>

                                @else
                                    {{-- SKENARIO 3: HARGA REGULER/KEDALUWARSA (3 baris info) --}}
                                    <span class="text-2xl font-bold text-primary">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                    
                                    {{-- Baris 2: Harga Coret (jika expired) / Kosong Transparan --}}
                                    <div class="flex justify-start items-baseline space-x-2">
                                        @if($is_discount_expired && $tryout->discount > 0)
                                            {{-- PERUBAHAN 2: font-bold dihapus --}}
                                            <span class="text-black line-through text-sm">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-400 text-sm invisible">Rp 00.000</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Baris 3: Placeholder Persentase Diskon --}}
                                    <p class="text-sm font-medium mt-1 text-transparent invisible">Diskon 00%</p>
                                    
                                @endif
                            </div>
                            {{-- END: CONTAINER HARGA FINAL --}}


                            <p class="text-left text-gray-600 text-sm mb-4">{{ $tryout->active_questions_count }} Soal</p>

                            <div class="flex justify-start gap-3 mt-auto">
                                <a
                                    href="{{ route('tryout.detail', ['tryout' => $tryout->slug]) }}"
                                    wire:navigate
                                    class="flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-300 text-sm w-1/2"
                                    wire:loading.attr="disabled">
                                    <i class="fas fa-eye mr-2"></i> Detail
                                </a>

                                <a href="{{ route('tryout.payment', ['tryout_slug' => $tryout->slug]) }}"
                                    wire:navigate
                                    class="flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg shadow-lg transition-colors duration-300 w-1/2">
                                    <i class="fa-solid fa-money-bill-wave mr-2"></i> Beli
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-center" data-aos="fade-up">
                    <div class="bg-white rounded-2xl shadow-lg p-4">
                        {{ $tryouts->links() }}
                    </div>
                </div>

                <div class="text-center mt-6 text-gray-600">
                    <p>Menampilkan {{ $tryouts->count() }} dari {{ $tryouts->total() }} try out</p>
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                        <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Try Out Tidak Ditemukan</h3>
                        <p class="text-gray-600 mb-4">Tidak ada try out yang sesuai dengan pencarian Anda.</p>
                        <button wire:click="resetFilters" class="bg-primary text-white px-6 py-2 rounded-full font-medium hover:bg-primary-dark transition-colors duration-300">
                            <i class="fas fa-refresh mr-2"></i> Reset Pencarian
                        </button>
                    </div>
                </div>
            @endif
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
    .grid { view-transition-name: tryout-grid; }
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
@push('scripts')
<script type="text/javascript" src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script type="text/javascript">
    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized, listening for Midtrans events...');
        
        @this.on('showMidtransSnap', ({ snapToken }) => {
            console.log('Received Snap Token:', snapToken);
            
            if (snapToken && typeof window.snap !== 'undefined') {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment Success:', result);
                        @this.dispatch('paymentSuccess');
                    },
                    onPending: function(result) {
                        console.log('Payment Pending:', result);
                        // Redirect ke halaman pending
                        window.location.href = "{{ route('payment.pending') }}?order_id=" + result.order_id;
                    },
                    onError: function(result) {
                        console.error('Payment Error:', result);
                        alert('Pembayaran gagal: ' + (result.status_message || 'Silakan coba lagi'));
                        window.location.href = "{{ route('payment.error') }}";
                    },
                    onClose: function() {
                        console.log('Payment pop-up closed by user');
                        // Opsional: beri feedback ke user
                    }
                });
            } else {
                console.error('Snap token or Midtrans SDK not available');
                alert('Error: Sistem pembayaran tidak tersedia. Silakan refresh halaman.');
            }
        });
    });
</script>
@endpush