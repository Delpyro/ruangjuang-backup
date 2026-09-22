<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <title>Status Pembayaran - Ruang Juang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center mb-6">
                    {{-- MENGGUNAKAN 'settlement' sebagai status berhasil --}}
                    @if($status === 'settlement')
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h1>
                        <p class="text-gray-600">Terima kasih telah membeli tryout di Ruang Juang</p>
                    @elseif($status === 'pending')
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-yellow-500 text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Menunggu Pembayaran</h1>
                        <p class="text-gray-600">Silakan selesaikan pembayaran Anda</p>
                    @elseif($status === 'capture')
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-blue-500 text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Tertangkap</h1>
                        <p class="text-gray-600">Pembayaran Anda sedang diproses</p>
                    @else
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times-circle text-red-500 text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Gagal/Batal</h1>
                        <p class="text-gray-600">Terjadi kesalahan dalam proses pembayaran</p>
                    @endif
                </div>

                @if(isset($transaction))
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-mono text-gray-800">{{ $transaction->order_id }}</span>
                    </div>

                    @if($transaction->tryout)
                    <div class="flex justify-between items-center mt-2 border-t pt-2">
                        <span class="text-gray-600">Produk:</span>
                        <span class="font-semibold text-gray-800 text-right">{{ $transaction->tryout->title }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center mt-2 border-t pt-2">
                        <span class="text-gray-600">Total Bayar:</span>
                        <span class="font-semibold text-gray-800">{{ 'Rp ' . number_format($transaction->amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center mt-2 border-t pt-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-semibold 
                            @if($status === 'settlement') text-green-600
                            @elseif($status === 'pending') text-yellow-600
                            @elseif($status === 'capture') text-blue-600
                            @else text-red-600 @endif">
                            
                            {{-- Tampilkan status aktual dari database --}}
                            {{ $transaction->getStatusLabelAttribute() }}
                        </span>
                    </div>

                    {{-- Tampilkan waktu settlement jika ada --}}
                    @if($transaction->settlement_time)
                    <div class="flex justify-between items-center mt-2 border-t pt-2">
                        <span class="text-gray-600">Waktu Settlement:</span>
                        <span class="text-sm text-gray-800">{{ $transaction->settlement_time->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <div class="space-y-4 mb-8">
                    @if($status === 'settlement')
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Akses Tryout</p>
                                <p class="text-sm text-gray-600">Tryout sudah dapat diakses di dashboard Anda</p>
                            </div>
                        </div>
                    @elseif($status === 'pending' || $status === 'capture')
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-clock text-yellow-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Status Diperbarui Otomatis</p>
                                <p class="text-sm text-gray-600">Halaman ini akan otomatis mengecek status terbaru</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-sync-alt text-blue-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Auto Refresh</p>
                                <p class="text-sm text-gray-600">Status akan terupdate otomatis setiap refresh</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-redo text-red-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Coba Lagi</p>
                                <p class="text-sm text-gray-600">Silakan coba melakukan pembayaran kembali</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-3">
                    @if($status === 'settlement')
                        <a href="{{ route('dashboard') }}" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Ke Dashboard
                        </a>
                    @elseif($status === 'pending' || $status === 'capture')
                        {{-- Tombol refresh untuk manual check --}}
                        <button onclick="location.reload()" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Refresh Status Sekarang
                        </button>
                        
                        {{-- Auto refresh setiap 30 detik untuk pending --}}
                        <script>
                            setTimeout(function() {
                                location.reload();
                            }, 30000); // Refresh otomatis setiap 30 detik
                        </script>
                    @else
                        <a href="{{ route('tryout.index') }}" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-redo mr-2"></i>
                            Cari Tryout Lain
                        </a>
                    @endif
                    
                    <a href="{{ route('dashboard') }}" 
                        class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition-colors duration-300 flex items-center justify-center">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

                <div class="text-center pt-4 border-t border-gray-200 mt-6">
                    <p class="text-sm text-gray-600">Butuh bantuan?</p>
                    <a href="https://wa.me/628123456789" 
                        target="_blank"
                        class="inline-flex items-center text-green-600 hover:text-green-700 font-medium mt-1">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Hubungi WhatsApp
                    </a>
                </div>
            </div>

            {{-- Auto redirect ke dashboard setelah 5 detik untuk settlement --}}
            @if($status === 'settlement')
            <script>
                setTimeout(function() {
                    window.location.href = "{{ route('dashboard') }}";
                }, 50000);
            </script>
            @endif
        </div>
    </div>
</body>
</html>