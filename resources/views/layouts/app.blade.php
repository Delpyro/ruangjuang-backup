<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>{{ $title ?? 'Ruang Juang | Bimbel Persiapan Tes Kedinasan dan CPNS' }}</title>

    {{-- LIBRARIES --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/homepage.css', 'resources/js/homepage.js'])
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    
    {{-- CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* FIX: MENCEGAH SCROLLBAR HORIZONTAL PADA MOBILE */
        html, body {
            overflow-x: clip; 
        }
        /* Helper untuk top-24 jika tailwind belum compile class ini (96px) */
        .top-24 { top: 6rem; } 
    </style>

    {{-- MIDTRANS SNAP SCRIPT --}}
    <script>
        window.snapInitialized = false;
        window.pendingSnapToken = null;
        window.initializeMidtransSnap = function(snapToken, clientKey) {
            console.log('?? Initializing Midtrans Snap...');
            if (!snapToken || !clientKey) { console.error('? Missing Snap token or client key'); return; }
            window.pendingSnapToken = snapToken;
            if (typeof snap !== 'undefined') { executeSnapPayment(snapToken); } else { loadSnapJS(clientKey); }
        };
        function loadSnapJS(clientKey) {
            const scriptId = 'midtrans-snap-script';
            const oldScript = document.getElementById(scriptId);
            if (oldScript) { oldScript.remove(); }
            const script = document.createElement('script');
            script.id = scriptId;
	    script.src = 'https://app.midtrans.com/snap/snap.js';            
 	    script.setAttribute('data-client-key', clientKey);
            script.onload = function() {
                window.snapInitialized = true;
                if (window.pendingSnapToken) { executeSnapPayment(window.pendingSnapToken); }
            };
            script.onerror = function() { showSnapError(); };
            document.head.appendChild(script);
        }
        function executeSnapPayment(snapToken) {
            try {
                snap.pay(snapToken, {
                    onSuccess: function(result) { window.location.href = "{{ route('payment.finish') }}?order_id=" + result.order_id; },
                    onPending: function(result) { window.location.href = "{{ route('payment.pending') }}?order_id=" + result.order_id; },
                    onError: function(result) { window.location.href = "{{ route('payment.error') }}?order_id=" + result.order_id; },
                    onClose: function() { console.log('Payment closed'); }
                });
            } catch (error) { showSnapError(); }
        }
        function showSnapError() {
            const container = document.getElementById('snap-container');
            if (container) container.innerHTML = `<div class="text-center py-8 text-red-600">Gagal memuat pembayaran. Refresh halaman.</div>`;
        }
    </script>
</head>
<body class="bg-white text-gray-800 font-poppins transition-colors duration-400 scroll-smooth">
    
    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 z-50 py-4 px-4 sm:px-6 bg-white/90 backdrop-blur-md border-b border-gray-200 h-24 flex items-center">
        <div class="container mx-auto flex justify-between items-center w-full">
            <div class="flex items-center flex-shrink-0">
                {{-- Logo --}}
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logorj.webp') }}" alt="Logo RuangJuang" class="h-16 w-auto">
                </a>
            </div>

            {{-- NAVIGASI DESKTOP --}}
            <div class="hidden md:flex space-x-8 items-center">
                <a href="{{ url('/') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-home"></i> Beranda</a>
                
                @auth
                <a href="{{ route('tryout.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-file-alt"></i> Tryout</a>
                <a href="{{ route('bundle.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-file-alt"></i> Bundle</a>
                <a href="{{ route('tryout.my-tryouts') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-graduation-cap"></i> Tryoutku</a>
                
                <a href="{{ route('testimonials.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-comments"></i> Testimoni</a>
                
                <a href="https://wa.me/6285769163218" target="_blank" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fab fa-whatsapp"></i> MinRuJu</a>
                @else
                
                @if (Request::is('/'))
                    <a href="#promo" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-percent"></i> Promo</a>
                    <a href="#profil" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-user-circle"></i> Profil</a>
                @else
                    <a href="{{ url('/#promo') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-percent"></i> Promo</a>
                    <a href="{{ url('/#profil') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-user-circle"></i> Profil</a>
                @endif
                
                <a href="{{ route('testimonials.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-comments"></i> Testimoni</a>
                
                <a href="https://wa.me/6285769163218" target="_blank" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fab fa-whatsapp"></i> MinRuJu</a>
                @endauth
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    {{-- Dropdown Profile User --}}
                    <div class="relative hidden md:block">
                        <button onclick="toggleProfileMenu()" class="flex items-center space-x-2 px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors cursor-pointer focus:outline-none">
                            <i class="fas fa-user"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div id="dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 hidden">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"><i class="fas fa-user-circle mr-2"></i>Profile</a>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
                            <a href="{{ route('transaction.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"><i class="fas fa-history mr-2"></i>History Transaksi</a>
                            <a href="{{ route('rapor.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"><i class="fas fa-chart-line mr-2"></i>Rapor Saya</a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-primary border border-primary rounded-full font-medium hover:bg-primary hover:text-white transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors">Daftar</a>
                    </div>
                @endauth

                {{-- TOMBOL HAMBURGER MOBILE (ANIMASI X) --}}
                {{-- FIX: Menambahkan ID 'hamburgerBtn' dan styling transisi pada span --}}
                <button id="hamburgerBtn" onclick="toggleMobileMenu()" class="md:hidden flex flex-col space-y-1.5 w-10 h-10 justify-center items-center cursor-pointer bg-transparent focus:outline-none p-1 rounded hover:bg-gray-50 group">
                    {{-- Baris 1 --}}
                    <span class="block w-6 h-0.5 bg-gray-800 rounded transition-all duration-300 ease-in-out transform origin-center"></span>
                    {{-- Baris 2 --}}
                    <span class="block w-6 h-0.5 bg-gray-800 rounded transition-all duration-300 ease-in-out"></span>
                    {{-- Baris 3 --}}
                    <span class="block w-6 h-0.5 bg-gray-800 rounded transition-all duration-300 ease-in-out transform origin-center"></span>
                </button>
            </div>
        </div>

        {{-- MOBILE MENU --}}
        <div id="mobileMenu" class="fixed top-24 left-0 w-64 h-[calc(100vh-6rem)] bg-white shadow-lg md:hidden p-6 hidden overflow-y-auto z-40 border-t border-gray-100">
            <div class="flex flex-col space-y-6 pb-24">
                @auth
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm text-gray-500">Halo,</p>
                        <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    </div>
                    <a href="{{ route('profile') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-user-circle w-5"></i><span>Profile</span></a>
                    <a href="{{ route('dashboard') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span></a>
                    <a href="{{ route('transaction.history') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-history w-5"></i><span>History Transaksi</span></a>
                    <a href="{{ route('rapor.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-chart-line w-5"></i><span>Rapor Saya</span></a>
                    <div class="border-t border-gray-200 my-2"></div>
                @endauth

                <a href="{{ url('/') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-home w-5"></i><span>Beranda</span></a>
                
                @auth
                    <a href="{{ route('tryout.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-file-alt w-5"></i><span>Tryout</span></a>
                    <a href="{{ route('bundle.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-file-alt w-5"></i><span>Bundle</span></a>
                    <a href="{{ route('tryout.my-tryouts') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-graduation-cap w-5"></i><span>Tryoutku</span></a>
                @else
                    @if (Request::is('/'))
                        <a href="#promo" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-percent w-5"></i><span>Promo</span></a>
                        <a href="#profil" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-user-circle w-5"></i><span>Profil</span></a>
                    @else
                        <a href="{{ url('/#promo') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-percent w-5"></i><span>Promo</span></a>
                        <a href="{{ url('/#profil') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-user-circle w-5"></i><span>Profil</span></a>
                    @endif
                @endauth
                
                <a href="{{ route('testimonials.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fas fa-comments w-5"></i><span>Testimoni</span></a>
                <a href="https://wa.me/6285769163218" target="_blank" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"><i class="fab fa-whatsapp w-5"></i><span>MinRuJu</span></a>
                
                @auth
                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 w-full text-red-600 hover:text-red-700 transition-colors">
                                <i class="fas fa-sign-out-alt w-5"></i><span>Logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-primary border border-primary rounded-full font-medium hover:bg-primary hover:text-white transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{ $slot }}

    <footer id="kontak" class="bg-gray-100 border-t border-gray-200 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-4">Ruang Juang</h3>
                    <p class="text-gray-600 mb-4">Bimbingan belajar dan latihan Try Out untuk persiapan tes kedinasan dan CPNS, dengan metode terbaik serta materi dan soal yang selalu terupdate.</p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/temanmuberjuang?igsh=YmNmZTBqOHVncjRp" class="text-gray-600 hover:text-primary transition-colors" target="_blank"><i class="fab fa-instagram text-xl"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Program</h4>
                    <ul class="space-y-2">
                        @if (Request::is('/'))
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Try Out SKD</a></li>
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel Kedinasan</a></li>
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel CPNS</a></li>
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Kelas Intensif</a></li>
                        @else
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Try Out SKD</a></li>
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel Kedinasan</a></li>
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel CPNS</a></li>
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Kelas Intensif</a></li>
                        @endif
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Bantuan</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-600 hover:text-primary transition-colors">
                            <a href="https://www.instagram.com/temanmuberjuang?igsh=YmNmZTBqOHVncjRp" class="flex items-center hover:text-primary transition-colors" target="_blank"><i class="fab fa-instagram w-4 mr-2"></i> temanmuberjuang</a>
                        </li>
                        <li>
                            <a href="mailto:inruangjuang@gmail.com" class="text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-envelope w-4 mr-2"></i>inruangjuang@gmail.com</a>
                        </li>
                        <li><a href="https://wa.me/6285769163218" class="text-gray-600 hover:text-primary transition-colors flex items-center" target="_blank"><i class="fab fa-whatsapp w-4 mr-2"></i>+62 857-6916-3218</a></li>
                        
                        @if (Request::is('/'))
                            <li><a href="#faq" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-question-circle w-4 mr-2"></i>FAQ</a></li>
                        @else
                            <li><a href="{{ url('/#faq') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-question-circle w-4 mr-2"></i>FAQ</a></li>
                        @endif
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="https://www.instagram.com/temanmuberjuang?igsh=YmNmZTBqOHVncjRp" class="text-gray-600 hover:text-primary transition-colors flex items-center" target="_blank"><i class="fab fa-instagram w-4 mr-2"></i> temanmuberjuang</a>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 mr-2"></i> Jakarta, Indonesia
                        </li>
                        <li>
                            <a href="https://wa.me/6285769163218" class="text-gray-600 hover:text-primary transition-colors flex items-center" target="_blank">
                                <i class="fab fa-whatsapp w-4 mr-2"></i> +62 857-6916-3218
                            </a>
                        </li>
                        <li>
                            <a href="mailto:inruangjuang@gmail.com" class="text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-envelope w-4 mr-2"></i>inruangjuang@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; 2025 Ruang Juang. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // ========================================================
        // 1. FUNGSI TOGGLE MENU & ANIMASI HAMBURGER (Window Scope)
        // ========================================================
        
        window.toggleMobileMenu = function() {
            const menu = document.getElementById('mobileMenu');
            const btn = document.getElementById('hamburgerBtn');

            if(menu) {
                menu.classList.toggle('hidden');
            }

            // ANIMASI IKON MENJADI X
            if(btn) {
                const spans = btn.querySelectorAll('span');
                
                // Jika menu tersembunyi (artinya baru saja ditutup)
                if (menu.classList.contains('hidden')) {
                    // Kembalikan ke garis 3
                    spans[0].classList.remove('rotate-45', 'translate-y-2');
                    spans[1].classList.remove('opacity-0');
                    spans[2].classList.remove('-rotate-45', '-translate-y-2');
                } else {
                    // Ubah menjadi X
                    spans[0].classList.add('rotate-45', 'translate-y-2');
                    spans[1].classList.add('opacity-0');
                    spans[2].classList.add('-rotate-45', '-translate-y-2');
                }
            }
        };

        window.toggleProfileMenu = function() {
            const menu = document.getElementById('dropdown-menu');
            if(menu) {
                menu.classList.toggle('hidden');
            }
        };

        // ========================================================
        // 2. LOGIKA TUTUP MENU SAAT KLIK DI LUAR (Event Delegation)
        // ========================================================
        document.addEventListener('click', function(e) {
            const mobileMenu = document.getElementById('mobileMenu');
            const dropdownMenu = document.getElementById('dropdown-menu');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            
            // Tutup Mobile Menu
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                // Cek jika yang diklik BUKAN menu DAN BUKAN tombol hamburger
                const isHamburger = e.target.closest('#hamburgerBtn');
                if (!mobileMenu.contains(e.target) && !isHamburger) {
                    mobileMenu.classList.add('hidden');
                    
                    // Reset animasi ikon hamburger kembali ke garis 3
                    if(hamburgerBtn) {
                        const spans = hamburgerBtn.querySelectorAll('span');
                        spans[0].classList.remove('rotate-45', 'translate-y-2');
                        spans[1].classList.remove('opacity-0');
                        spans[2].classList.remove('-rotate-45', '-translate-y-2');
                    }
                }
            }

            // Tutup Dropdown Profile
            if (dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                const isProfileBtn = e.target.closest('button[onclick="toggleProfileMenu()"]');
                if (!dropdownMenu.contains(e.target) && !isProfileBtn) {
                    dropdownMenu.classList.add('hidden');
                }
            }
        });

        // ========================================================
        // 3. INIT AOS & RESET LIVEWIRE
        // ========================================================
        function initPlugins() {
            setTimeout(() => {
                if(typeof AOS !== 'undefined') {
                    AOS.init();
                    AOS.refresh();
                }
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', initPlugins);
        
        document.addEventListener('livewire:navigated', function() {
            initPlugins();
            window.snapInitialized = false;
            window.pendingSnapToken = null;
        });
    </script>
    
</body>
</html>