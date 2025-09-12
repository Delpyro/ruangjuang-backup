<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Juang | Bimbel Persiapan Tes Kedinasan dan CPNS</title>
    
    @vite(['resources/css/homepage.css', 'resources/js/homepage.js'])
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 font-poppins transition-colors duration-400 scroll-smooth">
    
    <div id="loading-screen">
        <span class="loading-title">RuangJuang</span>
        <div id="progress-bar-container">
            <div id="progress-bar"></div>
        </div>
    </div>
    
    <nav class="fixed top-0 left-0 right-0 z-50 py-4 px-6 bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="#beranda">
                    <img src="{{ asset('images/logorj.png') }}" alt="Logo RuangJuang" class="h-16">
                </a>
            </div>

            <div class="hidden md:flex space-x-8">
                <a href="#beranda" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-home"></i> Beranda</a>
                <a href="#promo" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-percent"></i> Promo</a>
                <a href="#profil" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-user-circle"></i> Profil</a>
                <a href="#testimoni" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-comments"></i> Testimoni</a>
                <a href="#kontak" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-envelope"></i> Kontak</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors hidden md:block">Daftar Try Out</a>
                <button id="hamburger" class="hamburger md:hidden flex flex-col space-y-1.5 w-6 h-6 justify-center items-center">
                    <span class="hamburger-line block w-6 h-0.5 bg-gray-800 rounded"></span>
                    <span class="hamburger-line block w-6 h-0.5 bg-gray-800 rounded"></span>
                    <span class="hamburger-line block w-6 h-0.5 bg-gray-800 rounded"></span>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="mobile-menu fixed top-16 left-0 w-64 h-screen bg-white shadow-lg md:hidden p-6">
            <div class="flex flex-col space-y-6">
                <a href="#beranda" class="nav-link font-medium hover:text-primary transition-colors"><i class="fas fa-home"></i> Beranda</a>
                <a href="#promo" class="nav-link font-medium hover:text-primary transition-colors"><i class="fas fa-percent"></i> Promo</a>
                <a href="#profil" class="nav-link font-medium hover:text-primary transition-colors"><i class="fas fa-user-circle"></i> Profil</a>
                <a href="#testimoni" class="nav-link font-medium hover:text-primary transition-colors"><i class="fas fa-comments"></i> Testimoni</a>
                <a href="#kontak" class="nav-link font-medium hover:text-primary transition-colors"><i class="fas fa-envelope"></i> Kontak</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors w-full text-center">Daftar Try Out</a>
            </div>
        </div>
    </nav>
    
    <header id="beranda" class="gradient-bg pt-32 pb-20 text-white overflow-hidden relative">
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 text-center lg:text-left mb-10 lg:mb-0" data-aos="fade-right">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">SIAP TEMBUS TES KEDINASAN & CPNS</h1>
                    <p class="text-xl md:text-2xl mb-8 font-light">Raih impianmu dengan persiapan materi dan Try Out terbaik di Ruang Juang</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary rounded-full font-semibold text-lg hover:bg-gray-100 transition-colors flex items-center justify-center space-x-2">
                            <span>Daftar Try Out</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <button class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/10 text-white rounded-full font-semibold text-lg hover:bg-white/20 transition-colors">
                            Lihat Program
                        </button>
                    </div>
                </div>

                <div class="lg:w-1/2" data-aos="fade-left" data-aos-delay="300">
                    <div class="relative">
                        <img src="{{ asset('images/praja.jpeg') }}" alt="Main Banner" class="rounded-2xl shadow-2xl w-full max-w-xl mx-auto transform rotate-2">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="tentang" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Tentang Ruang Juang</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Ruang Juang adalah bimbingan belajar khusus yang fokus pada persiapan tes kedinasan dan CPNS. Kami hadir dengan metode pembelajaran dan pelaihan Try Out terbaik serta materi dan soal yang selalu update untuk membantu kamu meraih kesuksesan.</p>
            </div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                <div class="p-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl shadow-xl border border-blue-100 space-y-4" data-aos="fade-right" data-aos-delay="200">
                    <h3 class="text-2xl font-bold text-primary-dark">Kenapa harus Ruang Juang?</h3>
                    <p class="text-lg text-gray-800 leading-relaxed text-justify">
                        Karena setiap tahun, ribuan pejuang gagal bukan karena mereka tidak pintar, tetapi karena mereka tidak siap. Apakah kamu mau menjadi salah satunya? Ingat, satu langkah terlambat bisa membuatmu hanya menjadi penonton saat nama orang lain diumumkan lolos. Bayangkan wajah orang tuamu yang tersenyum bangga saat kamu resmi dilantik menjadi taruna atau CPNS. Semua itu bukan sekadar angan. Bersama Ruang Juang, mimpi itu bisa kamu wujudkan.
                    </p>
                    <p class="text-lg text-gray-800 leading-relaxed text-justify">
                        Sainganmu bukan puluhan, tapi puluhan ribu. Pemenang bukanlah yang paling pintar, melainkan mereka yang paling siap dan berani memulai lebih dulu. Setiap detik yang kamu tunda, ada ribuan pesaing lain yang selangkah lebih maju darimu.
                    </p>
                    <p class="text-lg text-gray-800 leading-relaxed text-justify">
                        Kesuksesan bukan milik orang yang hanya bermimpi. Kesuksesan milik mereka yang berani mengambil langkah hari ini. Amankan kursimu sekarang, sebelum kesempatan itu direbut orang lain. Dengan soal terupdate, simulasi nyata, dan mentor berpengalaman, Ruang Juang siap mengantarkanmu lolos kedinasan & CPNS.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <div class="bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-icon mx-auto flex items-center justify-center w-[70px] h-[70px] rounded-full mb-6">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-center">Materi Terupdate</h3>
                        <p class="text-gray-600 text-center">Kami selalu memperbarui materi sesuai dengan perkembangan terbaru dari pola tes kedinasan dan CPNS.</p>
                    </div>
                
                    <div class="bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-icon mx-auto flex items-center justify-center w-[70px] h-[70px] rounded-full mb-6">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-center">Tentor Berpengalaman</h3>
                        <p class="text-gray-600 text-center">Pengajar kami memiliki segudang pengalaman dalam hal Seleksi Kompetensi Dasar</p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow md:col-span-2 text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-icon mx-auto flex items-center justify-center w-[70px] h-[70px] rounded-full mb-6">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Try Out</h3>
                        <p class="text-gray-600">Kami menyediakan banyak Try Out dengan sistem penilaian dan tampilan yang sesuai dengan tes SKD sebenarnya, lengkap dengan variasi tingkat kesulitan mulai dari standar hingga HOTS.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="profil" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Profil Founder</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="flex flex-col items-center text-center" data-aos="fade-right">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/isnan.jpg') }}" alt="Founder 1" class="rounded-2xl shadow-2xl w-64 h-64 object-cover mx-auto image-focus-top">
                    </div>
                   <h2 class="text-3xl font-bold mb-2">Isnan Lian Darojad</h2>
                    <p class="text-xl text-gray-600 mb-4">Founder, Owner & Pengajar</p>
                    <p class="text-gray-600 italic">"Kejar apa yang kamu impikan dan doakan apa yang kamu usahakan"</p>
                </div>

                <div class="flex flex-col items-center text-center" data-aos="fade-left">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/gagah.jpg') }}" alt="Founder 1" class="rounded-2xl shadow-2xl w-64 h-64 object-cover mx-auto image-focus-top">
                    </div>
                   <h2 class="text-3xl font-bold mb-2">Gagah Edwin Kurniawan</h2>
                    <p class="text-xl text-gray-600 mb-4">Owner Try Out Ruang Juang</p>
                    <p class="text-gray-600 italic">"Untuk melihat pelangi yang indah, kamu harus siap melewati badai, sebab pelangi lahir dari badai yang telah usai."</p>
                </div>
            </div>
        </div>
    </section>

    <section id="visi-misi" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Visi dan Misi</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg transition-shadow hover:shadow-xl" data-aos="fade-right" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-eye text-primary text-3xl mr-4"></i>
                        <h3 class="text-2xl font-bold text-primary">Visi</h3>
                    </div>
                    <p class="text-gray-600">Menjadi lembaga bimbingan belajar dan tryout terbaik di Indonesia yang mendampingi generasi muda untuk meraih kesuksesan dalam seleksi sekolah kedinasan dan CPNS, dengan menyediakan layanan berkualitas, inovatif, dan berorientasi pada hasil.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 shadow-lg transition-shadow hover:shadow-xl" data-aos="fade-left" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-bullseye text-primary text-3xl mr-4"></i>
                        <h3 class="text-2xl font-bold text-primary">Misi</h3>
                    </div>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        <li>Menyediakan materi pembelajaran berkualitas tinggi yang selalu terbarukan sesuai dengan standar ujian kedinasan dan CPNS</li>
                        <li>Menghadirkan tryout berkualitas yang sesuai dengan tes SKD asli dengan sistem penilaian yang akurat serta analisis hasil yang komprehensif untuk mengukur kesiapan peserta, serta memberikan berbagai fasilitas web tryout demi kenyamanan peserta</li>
                        <li>Menyediakan tutor profesional dan berpengalaman untuk memberikan bimbingan terbaik kepada siswa.</li>
                        <li>Mengembangkan platform digital yang mudah diakses untuk mempermudah proses belajar dan tryout secara daring</li>
                        <li>Membangun komunitas belajar yang suportif dan inspiratif guna menciptakan lingkungan yang mendukung keberhasilan siswa</li>
                        <li>Berkomitmen terhadap kepuasan peserta melalui layanan prima dan hasil yang terukur.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="testimoni" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Apa Kata Mereka?</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Testimoni dari alumni yang telah berhasil tembus tes kedinasan dan CPNS bersama Ruang Juang</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 rounded-2xl p-6 shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=987&auto=format&fit=crop" alt="Testimonial" class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-semibold">Diana Putri</h4>
                            <p class="text-sm text-primary-dark">Alumni PKN STAN</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"Berkat metode pembelajaran di Ruang Juang, saya berhasil lolos PKN STAN. Try out yang diselenggarakan sangat membantu untuk beradaptasi dengan soal-soal tes."</p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=987&auto=format&fit=crop" alt="Testimonial" class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-semibold">Rizky Pratama</h4>
                            <p class="text-sm text-primary-dark">CPNS Kemenkeu</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"Tutor di Ruang Juang sangat berpengalaman dan menguasai materi. Strategi yang diajarkan sangat membantu dalam mengerjakan soal dengan waktu terbatas."</p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 shadow-lg" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=988&auto=format&fit=crop" alt="Testimonial" class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-semibold">Sari Indah</h4>
                            <p class="text-sm text-primary-dark">Alumni IPDN</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"Materi yang diajarkan sangat update dan sesuai dengan kebutuhan tes. Saya sangat merekomendasikan Ruang Juang untuk yang serius ingin lolos tes kedinasan."</p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                        <i class="fas fa-star text-accent"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section id="skor" class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-6" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-primary-dark">Skor Ambang Batas SKD</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">Informasi skor ambang batas resmi yang ditetapkan untuk Seleksi Kompetensi Dasar (SKD) tes kedinasan dan CPNS.</p>
        </div>
        
        <div class="overflow-x-auto no-scrollbar">
            <table class="min-w-full text-left bg-white rounded-lg shadow-xl overflow-hidden">
                <thead class="bg-primary-dark text-white">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Jenis Tes</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Jumlah Soal</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Bobot Nilai Benar</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Skor Ambang Batas</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Skor Maksimal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4">Tes Wawasan Kebangsaan (TWK)</td>
                        <td class="px-6 py-4">30 soal</td>
                        <td class="px-6 py-4">5</td>
                        <td class="px-6 py-4 font-bold text-primary-dark">65</td>
                        <td class="px-6 py-4">150</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4">Tes Intelegensia Umum (TIU)</td>
                        <td class="px-6 py-4">35 soal</td>
                        <td class="px-6 py-4">5</td>
                        <td class="px-6 py-4 font-bold text-primary-dark">80</td>
                        <td class="px-6 py-4">175</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4">Tes Karakteristik Pribadi (TKP)</td>
                        <td class="px-6 py-4">45 soal</td>
                        <td class="px-6 py-4">1-5</td>
                        <td class="px-6 py-4 font-bold text-primary-dark">166</td>
                        <td class="px-6 py-4">225</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-8 text-center text-gray-600 max-w-2xl mx-auto">
                <p><strong>Catatan:</strong> Skor ambang batas dan jumlah soal dapat berubah sesuai dengan kebijakan terbaru dari BKN. Pastikan untuk selalu memeriksa informasi resmi.</p>
            </div>
        </div>
    </div>
</section>

    <section id="promo" class="py-20 bg-gradient-to-r from-green-50 to-gold-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Promo Spesial</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Promo spesial ini tidak berlaku untuk semua orang. Hanya sebagian pengguna yang beruntung bisa menikmatinya. Amankan paketmu sekarang sebelum habis!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl flex flex-col">
                    <div class="p-6 border-b border-gray-200">
                        <span class="promo-tag-premium text-xs px-2 py-1 rounded-full">POPULER</span>
                        <h3 class="text-2xl font-bold my-4">Paket Premium</h3>
                        <p class="text-gray-600">Akses penuh semua materi dan try out selama 6 bulan</p>
                        <div class="my-6">
                            <span class="text-4xl font-bold">Rp 499rb</span>
                            <span class="line-through text-gray-400 ml-2">Rp 799rb</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <ul class="space-y-3 mb-6 flex-grow">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-700 mr-2"></i>
                                <span>12x Try Out Berkala</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-700 mr-2"></i>
                                <span>Akses Materi Lengkap</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-700 mr-2"></i>
                                <span>Konsultasi dengan Tutor</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-700 mr-2"></i>
                                <span>Group Discussion</span>
                            </li>
                        </ul>
                        <button class="w-full mt-auto promo-btn-premium text-white py-3 rounded-xl font-medium">Daftar Sekarang</button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl flex flex-col">
                    <div class="p-6 border-b border-gray-200">
                        <span class="promo-tag-ultimate text-xs px-2 py-1 rounded-full">TERLARIS</span>
                        <h3 class="text-2xl font-bold my-4">Paket Ultimate</h3>
                        <p class="text-gray-600">Akses penuh semua materi dan try out selama 1 tahun</p>
                        <div class="my-6">
                            <span class="text-4xl font-bold">Rp 899rb</span>
                            <span class="line-through text-gray-400 ml-2">Rp 1.299rb</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <ul class="space-y-3 mb-6 flex-grow">
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-700 mr-2"></i>
                                <span>24x Try Out Berkala</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-700 mr-2"></i>
                                <span>Akses Materi Lengkap</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-700 mr-2"></i>
                                <span>Konsultasi Private dengan Tutor</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-700 mr-2"></i>
                                <span>Simulasi Wawancara</span>
                            </li>
                        </ul>
                        <button class="w-full mt-auto promo-btn-ultimate text-white py-3 rounded-xl font-medium">Daftar Sekarang</button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl flex flex-col">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-2xl font-bold my-4">Paket Try Out</h3>
                        <p class="text-gray-600">4x try out untuk persiapan intensif</p>
                        <div class="my-6">
                            <span class="text-4xl font-bold">Rp 199rb</span>
                            <span class="line-through text-gray-400 ml-2">Rp 299rb</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <ul class="space-y-3 mb-6 flex-grow">
                            <li class="flex items-center">
                                <i class="fas fa-check text-gray-500 mr-2"></i>
                                <span>4x Try Out Berkala</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-gray-500 mr-2"></i>
                                <span>Pembahasan Soal Lengkap</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-gray-500 mr-2"></i>
                                <span>Analisis Hasil Try Out</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-gray-500 mr-2"></i>
                                <span>Tips & Strategi Mengerjakan</span>
                            </li>
                        </ul>
                        <button class="w-full mt-auto bg-gray-200 text-gray-800 py-3 rounded-xl font-medium hover:bg-gray-300 transition-colors">Daftar Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="kontak" class="bg-gray-100 border-t border-gray-200 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-4">Ruang Juang</h3>
                    <p class="text-gray-600 mb-4">Bimbingan belajar dan latihan Try Out untuk persiapan tes kedinasan dan CPNS, dengan metode terbaik serta materi dan soal yang selalu terupdate.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-600 hover:text-primary transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary transition-colors">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Program</h4>
                    <ul class="space-y-2">
                        <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors">Try Out SKD</a></li>
                        <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors">Bimbel Kedinasan</a></li>
                        <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors">Bimbel CPNS</a></li>
                        <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors">Kelas Intensif</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Bantuan</h4>
                    <ul class="space-y-2">
                        <li><a href="#tentang" class="nav-link text-gray-600 hover:text-primary transition-colors">FAQ</a></li>
                        <li><a href="#" class="nav-link text-gray-600 hover:text-primary transition-colors">Customer Service 1</a></li>
                        <li><a href="#kontak" class="nav-link text-gray-600 hover:text-primary transition-colors">Customer Service 2</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center text-gray-600">
                            Jakarta, Indonesia
                        </li>
                        <li class="flex items-center text-gray-600">
                            +62 123 456 789
                        </li>
                        <li class="flex items-center text-gray-600">
                            info@ruangjuang.com
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; 2025 Ruang Juang. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>