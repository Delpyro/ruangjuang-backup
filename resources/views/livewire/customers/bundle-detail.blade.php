<div class="min-h-screen bg-gradient-to-br from-gray-50 to-purple-50 py-12 mt-24">
    <div class="container mx-auto px-4 max-w-6xl">
        
        @if ($bundle)
        {{-- HEADER: JUDUL & DESKRIPSI --}}
        <div class="text-center mb-16 relative">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">{{ $bundle->title }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Persiapkan dirimu dengan paket bundle try out komprehensif kami yang lebih hemat.
            </p>
        </div>

        {{-- MAIN GRID LAYOUT --}}
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM KIRI: KONTEN UTAMA --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-2xl hover:shadow-2xl transition-all duration-500 p-8 order-2 lg:order-1 border border-gray-100">
                
                {{-- DETAIL PAKET --}}
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-purple-500 to-purple-700 w-3 h-8 rounded-full mr-3"></span>
                    Detail Paket
                </h3>
                <div class="prose max-w-none text-gray-700 mb-8 bg-purple-50 p-6 rounded-2xl border border-purple-100">
                    {!! $bundle->description !!}
                </div>

                {{-- DAFTAR TRYOUT DALAM BUNDLE --}}
                <div class="pt-8 border-t border-gray-200 mt-10">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-list-alt text-purple-500 mr-3 text-xl"></i>
                        Isi Paket Try Out ({{ $bundle->tryouts->count() }})
                    </h3>
                    
                    <div class="space-y-4">
                        @forelse ($bundle->tryouts as $tryout)
                            <div class="flex items-center justify-between bg-gray-50 p-5 rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md hover:bg-white">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-4 flex items-center justify-center w-12 h-12">
                                        <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-900 block text-lg">{{ $tryout->title }}</span>
                                        <span class="text-sm text-gray-500">Harga Satuan Normal: <strong class="text-gray-700">Rp {{ number_format($tryout->price, 0, ',', '.') }}</strong></span>
                                    </div>
                                </div>
                                <div class="hidden sm:block text-right">
                                    <i class="fas fa-check-square text-green-500 text-xl opacity-80"></i>
                                </div>
                            </div>
                        @empty
                            <div class="text-center bg-yellow-50 p-6 rounded-xl border border-yellow-100">
                                <i class="fas fa-box-open text-yellow-500 text-3xl mb-3"></i>
                                <p class="text-gray-600 italic">Belum ada try out di dalam paket ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
            
            {{-- KOLOM KANAN: STICKY SIDEBAR (TOMBOL AKSI & HARGA) --}}
            <div class="lg:col-span-1 p-0 order-1 lg:order-2 sticky top-32 self-start">
                
                <div class="bg-gradient-to-b from-white to-purple-50 rounded-2xl shadow-2xl p-8 border border-purple-200 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-purple-100 rounded-full -mr-6 -mt-6 opacity-40"></div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 relative z-10">Dapatkan Bundle Ini</h3>

                    {{-- Badge Status --}}
                    @if($hasPurchased)
                        <div class="mb-6 relative z-10">
                            <span class="bg-green-100 text-green-700 text-sm font-bold px-4 py-2 rounded-full inline-flex items-center shadow-sm border border-green-200">
                                <i class="fas fa-check-circle mr-2"></i> Sudah Dibeli
                            </span>
                        </div>
                    @endif

                    {{-- Harga --}}
                    <div class="text-left mb-6 border-b border-purple-200 pb-4 relative z-10">
                        <span class="text-lg text-gray-600 font-semibold block mb-2">Harga Bundle Saat Ini:</span>
                        @if($bundle->discount > 0)
                            <div class="flex flex-col mb-1 mt-1">
                                <span class="text-gray-500 line-through text-sm font-medium">Harga Awal: Rp {{ number_format($bundle->price, 0, ',', '.') }}</span>
                                <span class="text-4xl font-extrabold text-purple-800 mt-1">Rp {{ number_format($bundle->final_price, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-green-600 font-semibold mt-2 text-sm bg-green-50 inline-block px-3 py-1 rounded-md border border-green-100">
                                Hemat Rp {{ number_format($bundle->discount, 0, ',', '.') }}
                            </p>
                        @else
                            <span class="text-4xl font-extrabold text-purple-800 block mt-2">Rp {{ number_format($bundle->price, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="relative z-10">
                        @if(!$hasPurchased)
                            {{-- Tombol Beli (Hijau) --}}
                            <a href="{{ route('bundle.payment', ['bundle_slug' => $bundle->slug]) }}"
                                wire:navigate
                                class="flex items-center justify-center bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 w-full text-lg transform hover:scale-[1.02] group">
                                <i class="fa-solid fa-money-bill-wave mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i> 
                                Beli Paket Ini
                            </a>
                        @else
                            {{-- Tombol Akses (Ungu) --}}
                            <a href="{{ route('tryout.my-tryouts') }}"
                                wire:navigate
                                class="flex items-center justify-center bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 w-full text-lg transform hover:scale-[1.02] group">
                                <i class="fas fa-play-circle mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i> 
                                Akses Try Out
                            </a>
                        @endif
                    </div>
                </div>
                
                {{-- Tombol Kembali --}}
                <div class="text-center mt-6">
                    <a href="{{ route('bundle.index') }}" wire:navigate class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-gray-700 bg-white border-[3px] border-gray-500 rounded-xl shadow-sm hover:bg-purple-50 hover:text-purple-700 hover:border-purple-600 transition-all duration-300 group">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-300"></i> 
                        Kembali ke Daftar Bundle
                    </a>
                </div>

            </div>
        </div>
        @endif
    </div>
</div>