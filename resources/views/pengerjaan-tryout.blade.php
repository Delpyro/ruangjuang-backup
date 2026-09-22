<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <title>Try Out | Bahasa Indonesia</title>
    <!-- Asumsi @vite('resources/css/app.css') sudah memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animasi untuk timer berkedip merah */
        @keyframes blink {
            0%, 100% { color: red; background-color: white; }
            50% { color: white; background-color: red; }
        }
        .blink {
            animation: blink 1s infinite;
        }

        /* Scrollbar halus */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #9CA3AF;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background-color: #6B7280;
        }
    </style>
</head>
<body class="h-full bg-gray-50">

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 h-3">
        <div class="bg-blue-600 h-3" style="width: 1%;"></div>
    </div>

    <!-- Main Container -->
    <div id="main-container" class="flex h-[calc(100vh-0.75rem)] relative overflow-hidden">
        
        <!-- Mobile Overlay (Akan Muncul saat Sidebar Terbuka) -->
        <div id="mobile-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Nomor Soal -->
        <div id="sidebar" 
             class="fixed inset-y-0 left-0 z-20 w-64 bg-white border-r overflow-y-scroll p-4 shadow-xl
                    transform -translate-x-full transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-1/6 md:shadow-none">
            
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button id="close-sidebar-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="grid grid-cols-4 gap-2 md:grid-cols-5">
                <!-- Looping Nomor Soal -->
                @for ($i = 1; $i <= 110; $i++)
                    <button 
                        class="w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm 
                        {{ $i == 1 ? 'bg-green-500 border-2 border-green-700' : 'bg-red-600' }}
                        hover:opacity-80 transition hover:scale-105">
                        {{ $i }}
                    </button>
                @endfor
            </div>
        </div>

        <!-- Konten Soal -->
        <div class="flex-1 flex flex-col relative w-full md:w-5/6">
            <!-- Header -->
            <div class="bg-blue-700 text-white flex justify-between items-center px-4 py-3 md:px-6">
                
                <!-- Tombol Toggle (Hanya Muncul di Mobile) -->
                <button id="toggle-sidebar-btn" class="md:hidden bg-blue-600 hover:bg-blue-800 px-3 py-1 rounded flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <span id="btn-text">Daftar Soal</span>
                </button>
                
                <!-- Judul -->
                <h2 class="font-bold text-lg hidden md:block">BAHASA INDONESIA</h2>
                <h2 class="font-bold text-lg block md:hidden">INDONESIA</h2>

                <!-- Timer -->
                <div id="timer" class="text-xl font-semibold bg-blue-600 px-4 py-1 rounded shadow-lg transition duration-500 ease-in-out">
                    98:40
                </div>
            </div>

            <!-- Area Soal -->
            <div class="flex-1 px-4 py-6 overflow-y-auto bg-gray-50 md:px-6">
                <div class="p-6 border rounded-lg shadow-lg bg-white">
                    <p class="mb-4 font-semibold text-gray-800 text-base md:text-lg">
                        1. “Meski banyak tantangan, perlu adanya dukungan berbagai pihak agar kebijakan ini dapat berjalan optimal.”<br>
                        Kalimat di atas dapat menjadi kalimat efektif jika?
                    </p>

                    <div class="space-y-4 text-gray-700">
                        <label class="flex items-start space-x-3 cursor-pointer p-3 border rounded-lg hover:bg-blue-50 transition">
                            <input type="radio" name="jawaban" class="text-blue-600 mt-1 focus:ring-blue-500">
                            <span>A. Menghapus kata "banyak" sebelum tantangan.</span>
                        </label>

                        <label class="flex items-start space-x-3 cursor-pointer p-3 border rounded-lg hover:bg-blue-50 transition">
                            <input type="radio" name="jawaban" class="text-blue-600 mt-1 focus:ring-blue-500">
                            <span>B. Menambahkan subjek setelah kata "Meski".</span>
                        </label>

                        <label class="flex items-start space-x-3 cursor-pointer p-3 border rounded-lg bg-blue-100 hover:bg-blue-200 transition">
                            <input type="radio" name="jawaban" class="text-blue-600 mt-1 focus:ring-blue-500" checked>
                            <span>C. Mengganti "berjalan" dengan "dijalankan".</span>
                        </label>

                        <label class="flex items-start space-x-3 cursor-pointer p-3 border rounded-lg hover:bg-blue-50 transition">
                            <input type="radio" name="jawaban" class="text-blue-600 mt-1 focus:ring-blue-500">
                            <span>D. Menghilangkan frasa "agar kebijakan ini".</span>
                        </label>

                        <label class="flex items-start space-x-3 cursor-pointer p-3 border rounded-lg hover:bg-blue-50 transition">
                            <input type="radio" name="jawaban" class="text-blue-600 mt-1 focus:ring-blue-500">
                            <span>E. Sudah tepat semua.</span>
                        </label>
                    </div>

                    <!-- Tombol Navigasi -->
                    <div class="mt-8 flex flex-wrap justify-between items-center gap-3">
                        
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md flex-1 min-w-[120px]">
                            <span class="hidden md:inline">Soal </span>Sebelumnya
                        </button>
                        
                        <label class="flex items-center gap-2 cursor-pointer order-last md:order-none w-full md:w-auto justify-center">
                            <input type="checkbox" class="text-yellow-500 w-5 h-5 border-yellow-500 rounded focus:ring-yellow-500">
                            <span class="text-gray-700 font-medium">Ragu-ragu</span>
                        </label>
                        
                        <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md flex-1 min-w-[120px] order-3">
                            Simpan & Lanjutkan
                        </button>
                        
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg shadow-md flex-1 min-w-[120px] order-4 hidden md:block">
                            Lewati
                        </button>
                        
                        <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md flex-1 min-w-[120px] order-5">
                            Selesai Ujian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const timer = document.getElementById('timer');
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');
        const overlay = document.getElementById('mobile-overlay');

        // State & Timer Logic
        let totalSeconds = 98 * 60 + 40;

        /**
         * Mengubah waktu total menjadi format MM:SS dan menerapkan efek kedip.
         */
        function updateTimer() {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timer.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (totalSeconds <= 600) { // 10 menit terakhir
                timer.classList.add('blink');
            } else {
                timer.classList.remove('blink');
            }

            if (totalSeconds > 0) {
                totalSeconds--;
                setTimeout(updateTimer, 1000);
            } else {
                timer.textContent = '00:00';
                timer.classList.add('bg-red-700', 'text-white');
            }
        }

        // Sidebar Toggle Logic (Mobile Only)
        
        /**
         * Membuka Sidebar dan menampilkan Overlay.
         */
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden', 'opacity-0');
            overlay.classList.add('opacity-100');
            document.body.style.overflow = 'hidden'; // Mencegah scroll di body
        }

        /**
         * Menutup Sidebar dan menyembunyikan Overlay.
         */
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            document.body.style.overflow = ''; // Mengizinkan scroll kembali

            // Tunggu transisi selesai sebelum menyembunyikan overlay sepenuhnya
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300); 
        }

        // Event Listeners
        
        toggleBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar); // Tutup saat klik di overlay

        // Inisialisasi
        updateTimer();
        
    </script>

</body>
</html>
