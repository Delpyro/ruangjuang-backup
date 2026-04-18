<div class="min-h-screen bg-gray-50 py-12 mt-24">
    <div class="container mx-auto px-4 max-w-4xl">
        
        @if($isLoading)
            {{-- Bagian Loading --}}
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
                <p class="text-gray-600">Mempersiapkan pembayaran...</p>
            </div>
        @elseif($error)
            {{-- Bagian Error --}}
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-red-800 mb-2">Terjadi Kesalahan</h3>
                <p class="text-red-600 mb-4">{{ $error }}</p>
                
                <div class="flex gap-4 justify-center mt-6">
                    <button wire:click="retryPayment" 
                            wire:loading.attr="disabled"
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-75">
                        <span wire:loading.remove>Coba Lagi</span>
                        <span wire:loading>Memuat...</span>
                    </button>
                    {{-- Asumsi kembali ke detail item, jika tryout_slug tidak null, gunakan tryout, jika tidak gunakan bundle --}}
                    <a href="{{ $tryout_slug ? route('tryout.detail', $tryout_slug) : route('bundle.detail', $bundle_slug) }}" 
                       wire:navigate
                       class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Kembali
                    </a>
                </div>
            </div>
        @else
            {{-- Konten Utama Pembayaran --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Order Summary (Kolom Kiri) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24 border-t-4 border-primary">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-start border-b pb-4">
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $item->title }}</h4>
                                    
                                    @if($isBundle)
                                        <p class="text-xs text-purple-600 mt-1 font-medium">
                                            <i class="fas fa-box-open mr-1"></i> Paket {{ $item->tryouts->count() }} Try Out
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-600 mt-1">
                                            <i class="fas fa-question-circle mr-1"></i> {{ $item->active_questions_count }} Soal
                                        </p>
                                    @endif
                                </div>
                                <span class="text-lg font-bold text-primary flex-shrink-0 ml-4">
                                    Rp {{ number_format($item->final_price, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Rincian Diskon (Jika Ada) --}}
                            @if($item->price != $item->final_price)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Harga Normal</span>
                                    <span class="line-through">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm font-medium text-green-600">
                                    <span>Diskon {{ $item->discount_percentage }}%</span>
                                    <span>- Rp {{ number_format($item->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <hr class="border-dashed">

                            <div class="flex justify-between text-xl font-extrabold">
                                <span>Total Tagihan</span>
                                <span class="text-primary">Rp {{ number_format($item->final_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-shield-alt text-blue-500 mt-1 mr-2 text-lg"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-bold">Pembayaran Aman</p>
                                    <p class="mt-1">Transaksi Anda diproses oleh **Midtrans** (Verifikasi VISA/Mastercard Secure).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section (Kolom Kanan) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <div class="flex items-center justify-between mb-6 border-b pb-4">
                            <h2 class="text-2xl font-extrabold text-gray-800">Metode Pembayaran</h2>
                            <div class="text-sm text-gray-500">
                                Order ID: <span class="font-mono font-semibold text-gray-700">{{ $orderId }}</span>
                            </div>
                        </div>

                        <!-- Snap Container -->
                        <div id="snap-container" class="min-h-[400px] bg-gray-50 p-4 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <div class="animate-pulse h-16 w-16 bg-primary-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                                     <i class="fas fa-money-check-dollar text-primary text-2xl"></i>
                                </div>
                                <p class="text-gray-600 font-medium">Midtrans siap menampilkan pembayaran.</p>
                                <p class="text-sm text-gray-500 mt-1">Harap tunggu atau klik tombol "Bayar" jika halaman tidak muncul.</p>
                            </div>
                        </div>
                        
                        {{-- Tombol Trigger (Hidden jika auto-open aktif, tapi bagus untuk fallback/retry) --}}
                        <button 
                            onclick="window.initializeMidtransSnap('{{ $snapToken }}', '{{ $clientKey }}')"
                            class="mt-6 w-full bg-primary text-white py-3 rounded-xl text-lg font-bold hover:bg-primary-dark transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg"
                            @if(!$snapToken) disabled @endif
                        >
                            <i class="fas fa-wallet mr-2"></i> Klik untuk Membuka Pembayaran
                        </button>
                    </div>
                    
                    {{-- @if(app()->environment('local'))
                        <div class="mt-8 bg-yellow-50 p-4 rounded-lg text-xs text-yellow-800 border border-yellow-200">
                            <h4 class="font-bold mb-2">DEBUG INFO (LOCAL)</h4>
                            <pre class="whitespace-pre-wrap">{{ json_encode($debugInfo, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif --}}
                </div>
            </div>

            <!-- JavaScript Auto-Trigger Snap -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('🎯 Payment page loaded');
                    
                    @if($snapToken && $clientKey)
                        console.log('💰 Snap token available, attempting to initialize...');
                        // Tunggu sebentar untuk memastikan Livewire selesai render & JS di layout sudah dimuat
                        setTimeout(() => {
                            // Memanggil fungsi global yang ada di layout utama (Midtrans Snap Script)
                            if (typeof window.initializeMidtransSnap === 'function') {
                                window.initializeMidtransSnap("{{ $snapToken }}", "{{ $clientKey }}");
                            } else {
                                console.error('❌ Midtrans Snap initialization function not found.');
                                // Tampilkan pesan ke user jika gagal memuat
                                document.getElementById('snap-container').innerHTML = `
                                    <div class="text-center p-8">
                                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                                        <p class="text-red-600 mb-4">Gagal memuat halaman pembayaran. Silakan klik tombol di bawah.</p>
                                    </div>
                                `;
                            }
                        }, 500);
                    @endif
                });
                
                // Memicu ulang Snap jika komponen di-update (misal setelah retry payment)
                document.addEventListener('livewire:updated', function() {
                    @if($snapToken && $clientKey && !$isLoading)
                        console.log('💰 Re-initializing Snap after Livewire update');
                        setTimeout(() => {
                            if (typeof window.initializeMidtransSnap === 'function') {
                                window.initializeMidtransSnap("{{ $snapToken }}", "{{ $clientKey }}");
                            }
                        }, 300);
                    @endif
                });
            </script>
        @endif
    </div>
</div>
