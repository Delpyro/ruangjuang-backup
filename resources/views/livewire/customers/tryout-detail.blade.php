<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 mt-24">
    <div class="container mx-auto px-4">
        
        {{-- HEADER: JUDUL & DESKRIPSI --}}
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">{{ $data->title }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Raih targetmu dengan persiapan materi dan try out terbaik.
            </p>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM KIRI: KONTEN UTAMA --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-2xl hover:shadow-2xl transition-all duration-500 p-8 order-2 lg:order-1 border border-gray-100">
                
                {{-- STATISTIK RINGKAS --}}
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-primary to-blue-600 w-3 h-8 rounded-full mr-3"></span>
                    Ringkasan Utama
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    
                    {{-- Total Soal --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-200 rounded-full -mr-6 -mt-6 opacity-20"></div>
                        <div class="flex items-start">
                            <div class="bg-white p-3 rounded-xl shadow-sm mr-4">
                                <i class="fas fa-question-circle text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 font-medium block">Total Soal</span>
                                <span class="text-3xl font-extrabold text-gray-900 mt-1 block">
                                    {{ $data->active_questions_count ?? '0' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Durasi --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-2xl border border-purple-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-purple-200 rounded-full -mr-6 -mt-6 opacity-20"></div>
                        <div class="flex items-start">
                            <div class="bg-white p-3 rounded-xl shadow-sm mr-4">
                                <i class="fas fa-clock text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 font-medium block">Durasi</span>
                                <span class="text-3xl font-extrabold text-gray-900 mt-1 block">
                                    {{ $data->duration ?? '-' }} Mnt
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DESKRIPSI KONTEN --}}
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center pt-8 border-t border-gray-200">
                    <span class="bg-gradient-to-r from-primary to-blue-600 w-3 h-8 rounded-full mr-3"></span>
                    Fasilitas yang Didapatkan
                </h3>
                <div class="prose max-w-none text-gray-700 mb-8 bg-blue-50 p-6 rounded-2xl border border-blue-100">
                    {!! $data->content !!}
                </div>
                
                {{-- QUOTES --}}
                @if(!empty($data->quote))
                    <div class="relative bg-gradient-to-r from-yellow-50 to-amber-50 p-8 rounded-2xl shadow-lg border border-yellow-200 mb-8 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200 rounded-full -mr-10 -mt-10 opacity-20"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-amber-200 rounded-full -ml-8 -mb-8 opacity-20"></div>
                        
                        <i class="fas fa-quote-left absolute top-6 left-6 text-yellow-300 text-4xl opacity-50 z-0"></i>
                        
                        <div class="relative z-10 ml-12">
                            <p class="font-bold text-lg text-amber-800 mb-3">Quotes Motivasi:</p>
                            <div class="mt-1 max-w-none text-base italic text-amber-900 leading-relaxed"> 
                                {!! $data->quote !!}
                            </div>
                        </div>
                        <i class="fas fa-quote-right absolute bottom-6 right-6 text-yellow-300 text-4xl opacity-50 z-0"></i>
                    </div>
                @endif
                
                {{-- SECTION PERINGKAT / LEADERBOARD --}}
                <div class="pt-8 border-t border-gray-200 mt-10">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-3 text-xl"></i>
                        10 Peringkat Teratas
                    </h3>
                    
                    @if($rankings->isNotEmpty())
                        <ol class="space-y-4">
                            @foreach ($rankings as $index => $ranking)
                                <li class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                                    <div class="flex items-center">
                                        @if($index == 0)
                                            <span class="w-10 text-center"><i class="fas fa-medal text-3xl text-yellow-400" title="Peringkat 1"></i></span>
                                        @elseif($index == 1)
                                            <span class="w-10 text-center"><i class="fas fa-medal text-3xl text-gray-400" title="Peringkat 2"></i></span>
                                        @elseif($index == 2)
                                            <span class="w-10 text-center"><i class="fas fa-medal text-3xl text-orange-400" title="Peringkat 3"></i></span>
                                        @else
                                            <span class="text-lg font-bold text-gray-700 w-10 text-center">{{ $index + 1 }}</span>
                                        @endif
                                        
                                        <div class="ml-4">
                                            <span class="font-semibold text-gray-900 block">{{ $ranking->user->name ?? 'Pengguna' }}</span>
                                            <span class="text-sm text-gray-500">Skor: <strong class="text-blue-600">{{ number_format($ranking->score, 0, ',', '.') }}</strong></span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        {{-- PERINGKAT USER SAAT INI (SUDAH DIPERBAIKI) --}}
                        @if($userRanking)
                            <div class="mt-8 pt-6 border-t border-dashed border-gray-300">
                                <p class="text-sm font-semibold text-gray-700 mb-3 ml-1">Peringkat Anda Saat Ini:</p>
                                
                                {{-- Card Peringkat User --}}
                                <div class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-700 p-5 rounded-xl shadow-lg text-white transform hover:scale-[1.01] transition-transform duration-300 relative overflow-hidden">
                                    
                                    {{-- Background Decor --}}
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-8 -mt-8"></div>
                                    
                                    <div class="flex items-center relative z-10">
                                        {{-- Badge Angka Peringkat --}}
                                        <div class="w-14 h-14 flex flex-col items-center justify-center bg-white text-blue-700 rounded-full font-bold shadow-md border-4 border-blue-300">
                                            {{-- <span class="text-[10px] leading-none text-gray-400 uppercase font-bold tracking-tighter">POSISI</span> --}}
                                            <span class="text-2xl leading-none font-extrabold">{{ $userRankPosition ?? '-' }}</span>
                                        </div>
                                        
                                        <div class="ml-5">
                                            <span class="font-bold text-lg block tracking-wide">{{ $userRanking->user->name ?? 'Anda' }}</span>
                                            <div class="flex items-center mt-1">
                                                {{-- <i class="fas fa-star text-yellow-400 text-sm mr-2"></i> --}}
                                                <span class="text-blue-100 text-sm">Skor Perolehan: <strong class="text-white text-base ml-1">{{ number_format($userRanking->score, 0, ',', '.') }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Ikon Grafik di Kanan --}}
                                    {{-- <div class="hidden md:block text-right opacity-80 relative z-10">
                                        <i class="fas fa-chart-line text-4xl text-blue-300"></i>
                                    </div> --}}
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- Jika Belum Ada Ranking Sama Sekali --}}
                        <div class="text-center bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <i class="fas fa-info-circle text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-600">Belum ada peringkat untuk tryout ini.</p>
                        </div>
                    @endif
                </div>

                {{-- SECTION REVIEW / ULASAN --}}
                <div 
                    class="pt-8 border-t border-gray-200 mt-10"
                    x-data
                    @review-page-changed.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                >
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-3 text-xl"></i>
                        Ulasan Peserta
                    </h3>

                    @if($reviews->isNotEmpty())
                        {{-- Summary Rating --}}
                        <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 mb-8 flex flex-col md:flex-row items-center gap-4">
                            <div class="text-center">
                                <span class="text-5xl font-extrabold text-blue-700">{{ number_format($averageRating, 1) }}</span>
                                <span class="text-xl text-blue-600">/ 5</span>
                            </div>
                            <div class="text-center md:text-left">
                                <div class="flex justify-center md:justify-start text-yellow-400 text-2xl mb-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="{{ $averageRating >= $i ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                                <span class="text-gray-700">Berdasarkan <strong>{{ $totalReviews }}</strong> ulasan</span>
                            </div>
                        </div>

                        {{-- List Review --}}
                        <div class="space-y-6">
                            @foreach ($reviews as $review)
                                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-semibold text-gray-900">{{ $review->user->name ?? 'Peserta' }}</span>
                                        <div class="flex text-yellow-400">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $review->rating >= $i ? 'fas' : 'far' }} fa-star text-sm"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700 text-sm italic">"{{ $review->review_text }}"</p>
                                    <span class="text-xs text-gray-400 mt-2 block">{{ $review->created_at->locale('id')->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $reviews->links() }}
                        </div>

                    @else
                        <div class="text-center bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <i class="fas fa-comment-dots text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-600">Belum ada ulasan untuk tryout ini.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- KOLOM KANAN: STICKY SIDEBAR (TOMBOL AKSI) --}}
            <div class="lg:col-span-1 p-0 order-1 lg:order-2 sticky top-32 self-start">                 
                <div class="bg-gradient-to-b from-white to-blue-50 rounded-2xl shadow-2xl p-8 border border-gray-200 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 rounded-full -mr-6 -mt-6 opacity-40"></div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 relative z-10">Siap untuk Berjuang?</h3>

                    {{-- Harga --}}
                    <div class="text-left mb-6 border-b pb-4 relative z-10">
                        <span class="text-lg text-gray-600 block">Harga Spesial:</span>
                        @if($data->discount > 0)
                            <div class="flex items-baseline space-x-2">
                                <span class="text-4xl font-extrabold text-primary">Rp {{ number_format($data->final_price, 0, ',', '.') }}</span>
                                <span class="text-gray-400 line-through text-md">Rp {{ number_format($data->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-green-600 font-medium mt-1 text-sm">Anda Hemat **{{ $data->discount_percentage }}%**!</p>
                        @else
                            <span class="text-4xl font-extrabold text-primary">Rp {{ number_format($data->price, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    {{-- Logic Tombol Mulai/Beli --}}
                    @if($userTryout->isNotEmpty())
                        @php
                            $completed_attempts = $userTryout->where('is_completed', 1)->count();
                            $next_attempt = $completed_attempts + 1;
                            $has_completed_attempts = $completed_attempts > 0;
                        @endphp

                        <div class="mb-4 relative z-10">
                            <div class="text-center bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-xl mb-4 border border-gray-200 shadow-sm">
                                <p class="text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                    Percobaan Selesai: <span class="font-bold text-green-600">{{ $completed_attempts }}</span> kali
                                </p>
                                @if($next_attempt > 1)
                                    <p class="text-xs text-gray-500 mt-1">Anda akan memulai percobaan ulang.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol Lihat Hasil (Jika pernah selesai) --}}
                        @if($has_completed_attempts)
                            <a href="{{ route('tryout.my-results', ['tryout' => $data->slug]) }}"
                               class="flex items-center justify-center bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 w-full text-lg transform hover:scale-[1.02] mb-4 relative z-10 group">
                                <i class="fas fa-chart-bar mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i> 
                                Lihat Hasil Tryout
                            </a>
                        @endif

                        {{-- Tombol Mulai/Lanjutkan --}}
                        @php
                            $nextAttemptData = $userTryout->firstWhere('attempt', $next_attempt);
                        @endphp

                        @if($nextAttemptData)
                            <div wire:click="confirmStart({{ $next_attempt }})"
                                   class="flex items-center justify-center bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 w-full text-lg transform hover:scale-[1.02] cursor-pointer relative z-10 group">
                                <i class="fa-solid fa-play-circle mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i> 
                                
                                @if($nextAttemptData->started_at && !$nextAttemptData->is_completed)
                                    Lanjutkan Percobaan Ke-{{ $next_attempt }}
                                @else
                                    Mulai Percobaan Ke-{{ $next_attempt }}
                                @endif
                            </div>
                        @else
                            {{-- Jika sudah max attempt --}}
                            <div class="text-center bg-gray-100 p-4 rounded-xl border border-gray-200">
                                <p class="font-medium text-gray-700">Anda telah menyelesaikan semua percobaan.</p>
                            </div>
                        @endif

                    @else
                        {{-- Tombol Beli --}}
                        <a href="{{ route('tryout.payment', ['tryout_slug' => $data->slug]) }}"
                           wire:navigate
                           class="flex items-center justify-center bg-gradient-to-r from-primary to-blue-600 hover:from-primary-dark hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 w-full text-lg transform hover:scale-[1.02] relative z-10 group">
                            <i class="fa-solid fa-money-bill-wave mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i> 
                            Beli Sekarang
                        </a>
                    @endif
                </div>
                
                <div class="text-center mt-6">
                    <a href="{{ route('tryout.index') }}" wire:navigate class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-gray-700 bg-white border-[3px] border-gray-500 rounded-xl shadow-sm hover:bg-blue-50 hover:text-primary hover:border-primary transition-all duration-300 group">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-300"></i> 
                        Kembali ke Daftar Try Out
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL COPYRIGHT & KONFIRMASI --}}
    <div
        x-data="{ showModal: false }"
        x-on:show-copyright-modal.window="showModal = true"
        x-on:keydown.escape.window="showModal = false"
        x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4 backdrop-blur-sm"
        style="display: none;">
        <div
            x-show="showModal"
            x-on:click.outside="showModal = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-200"
        >
            <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-5 border-b border-red-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-xl"></i>
                    PERINGATAN HAK CIPTA
                </h2>
            </div>

            <div class="p-6 text-gray-700 space-y-4 max-h-[80vh] overflow-y-auto"> 
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
                    wire:click="startAttempt"
                    wire:loading.attr="disabled"
                    wire:target="startAttempt"
                    x-on:click="showModal = false" 
                    class="px-3 py-2 sm:px-5 sm:py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-wait inline-flex items-center"
                >
                    <span wire:loading.remove wire:target="startAttempt">
                        Saya Mengerti & Lanjutkan
                    </span>
                    <span wire:loading wire:target="startAttempt" class="flex items-center">
                        <div class="h-4 w-4 border-b-2 border-white mr-2 animate-spin rounded-full"></div>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>