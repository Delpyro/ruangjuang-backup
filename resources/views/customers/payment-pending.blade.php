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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
                <!-- Status Icon -->
                <div class="text-center mb-6">
                    @if($status === 'success')
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
                        <p class="text-gray-600">Pembayaran Anda sedang diproses</p>
                    @else
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times-circle text-red-500 text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Gagal</h1>
                        <p class="text-gray-600">Terjadi kesalahan dalam proses pembayaran</p>
                    @endif
                </div>

                <!-- Order Info -->
                @if(isset($orderId) && $orderId)
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-mono text-gray-800">{{ $orderId }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-semibold 
                            @if($status === 'success') text-green-600
                            @elseif($status === 'pending') text-yellow-600
                            @else text-red-600 @endif">
                            @if($status === 'success') Berhasil
                            @elseif($status === 'pending') Pending
                            @else Gagal
                            @endif
                        </span>
                    </div>
                </div>
                @endif

                <!-- Message -->
                @if(isset($message) && $message)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-blue-700 text-sm">{{ $message }}</p>
                </div>
                @endif

                <!-- Instructions based on status -->
                <div class="space-y-4 mb-8">
                    @if($status === 'success')
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Akses Tryout</p>
                                <p class="text-sm text-gray-600">Tryout sudah dapat diakses di dashboard Anda</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-green-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Email Konfirmasi</p>
                                <p class="text-sm text-gray-600">Konfirmasi akan dikirim ke email Anda</p>
                            </div>
                        </div>
                    @elseif($status === 'pending')
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-clock text-yellow-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Proses Verifikasi</p>
                                <p class="text-sm text-gray-600">Pembayaran sedang diverifikasi oleh sistem</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-sync-alt text-yellow-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Auto Update</p>
                                <p class="text-sm text-gray-600">Status akan update otomatis dalam 5-10 menit</p>
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
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-headset text-red-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Bantuan</p>
                                <p class="text-sm text-gray-600">Hubungi customer service jika masalah berlanjut</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @if($status === 'success')
                        <a href="{{ route('dashboard') }}" 
                           class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Ke Dashboard
                        </a>
                        <a href="{{ route('tryout.index') }}" 
                           class="w-full bg-primary text-white py-3 px-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-file-alt mr-2"></i>
                            Lihat Tryout Lainnya
                        </a>
                    @elseif($status === 'pending')
                        <button onclick="location.reload()" 
                                class="w-full bg-yellow-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-yellow-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Refresh Status
                        </button>
                        <a href="{{ route('dashboard') }}" 
                           class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i>
                            Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('tryout.index') }}" 
                           class="w-full bg-primary text-white py-3 px-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-redo mr-2"></i>
                            Coba Lagi
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i>
                            Ke Dashboard
                        </a>
                    @endif
                    
                    <!-- Contact Support -->
                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">Butuh bantuan?</p>
                        <a href="https://wa.me/628123456789" 
                           target="_blank"
                           class="inline-flex items-center text-green-600 hover:text-green-700 font-medium mt-1">
                            <i class="fab fa-whatsapp mr-2"></i>
                            Hubungi WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Auto redirect for success -->
            @if($status === 'success')
            <script>
                // Auto redirect ke dashboard setelah 5 detik
                setTimeout(function() {
                    window.location.href = "{{ route('dashboard') }}";
                }, 5000);
            </script>
            @endif
        </div>
    </div>
</body>
</html>