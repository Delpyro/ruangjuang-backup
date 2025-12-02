<div>
    {{-- Ini adalah ELEMEN ROOT TUNGGAL untuk Livewire --}}

    @if ($isGuest)
        {{-- ========================================================================= --}}
        {{-- 1. SPLASH SCREEN (FINAL FIX RESPONSIF CLAMP()) --}}
        {{-- ========================================================================= --}}
        <div id="ruangjuang-splash-screen-wrapper" style="
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: fixed; 
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 100vh;
            width: 100vw;
            background-color: #f8f9fa; 
            z-index: 10000; 
            transition: opacity 0.5s ease-out; 
            padding: 0 10px; 
        ">
            {{-- Elemen Animasi Utama (Teks dan Progress Bar) --}}
            <h1 style="
                /* FIX: Menggunakan CLAMP() untuk memastikan fluiditas di mobile (9vw) namun dibatasi MAX 3rem di desktop. */
                font-size: clamp(2rem, 9vw, 3rem); 
                min-width: 250px; 
                max-width: 90%; 
                font-weight: bold;
                background: linear-gradient(to right, #4CAF50, #FFC107);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 25px; 
                animation: text-pulse 2s infinite alternate;
                text-align: center;
            ">RuangJuang</h1>

            <div style="
                width: 80%;
                max-width: 300px; 
                height: 10px;
                background-color: #e9ecef;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            ">
                <div id="progress-bar-animation" style="
                    width: 0%;
                    height: 100%;
                    background: linear-gradient(to right, #4CAF50, #FFC107); 
                    border-radius: 5px;
                    animation: loading-animation 2s forwards ease-out; 
                "></div>
            </div>
            
            {{-- Paragraf juga menggunakan CLAMP() untuk responsif --}}
            <p class="mt-4 text-gray-600" style="font-size: clamp(0.9rem, 3.5vw, 1.125rem); text-align: center;">Memuat halaman terbaik untuk persiapanmu...</p>
        </div>

        {{-- SCRIPT DITEMPATKAN DI DALAM ELEMEN ROOT TERTINGGI --}}
        <script>
            // Pasang class loading-active ke body untuk mencegah scroll awal
            document.body.classList.add('loading-active');

            // Jalankan timeout untuk menyembunyikan splash screen
            document.addEventListener('DOMContentLoaded', function () {
                const splash = document.getElementById('ruangjuang-splash-screen-wrapper');
                
                const ANIMATION_DURATION = 2000;
                const FADE_OUT_DURATION = 500;
                const TOTAL_DELAY = ANIMATION_DURATION; 

                setTimeout(function() {
                    if (splash) {
                        splash.style.opacity = '0';
                        
                        setTimeout(() => {
                            splash.style.display = 'none';
                            document.body.classList.remove('loading-active');
                        }, FADE_OUT_DURATION); 
                    }
                }, TOTAL_DELAY);
            });
        </script>
    @endif

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
                        <a href="#promo" class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/10 text-white rounded-full font-semibold text-lg hover:bg-white/20 transition-colors text-center">
                            Lihat Program
                        </a>
                    </div>
                </div>

                <div class="lg:w-1/2" data-aos="fade-left" data-aos-delay="300">
                    <div class="relative">
                        <img src="{{ asset('images/praja.webp') }}" alt="Main Banner" class="rounded-2xl shadow-2xl w-full max-w-xl mx-auto transform hover:rotate-0 transition-transform duration-300">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="tentang" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Tentang Ruang Juang</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Ruang Juang adalah bimbingan belajar khusus yang fokus pada persiapan tes kedinasan dan CPNS. Kami hadir dengan metode pembelajaran dan pelatihan Try Out terbaik serta materi dan soal yang selalu update untuk membantu kamu meraih kesuksesan.</p>
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
                        Kesuksesan bukan milik orang yang hanya bermimpi. Kesuksesan milik mereka yang berani mengambil langkah hari ini. Amankan kursumu sekarang, sebelum kesempatan itu direbut orang lain. Dengan soal terupdate, simulasi nyata, dan mentor berpengalaman, Ruang Juang siap mengantarkanmu lolos kedinasan & CPNS.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <div class="bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-icon mx-auto flex items-center justify-center w-[70px] h-[70px] rounded-full mb-6">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-center">Materi Terupdate</h3>
                        <p class="text-gray-600 text-center">Kami selalu memperbarui materi sesuai dengan perkembangan terbaru dari pola tes kedinasan dan CPNS.</p>
                    </div>
                
                    <div class="bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-icon mx-auto flex items-center justify-center w-[70px] h-[70px] rounded-full mb-6">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-center">Tentor Berpengalaman</h3>
                        <p class="text-gray-600 text-center">Pengajar kami memiliki segudang pengalaman dalam hal Seleksi Kompetensi Dasar</p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 md:col-span-2 text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-icon mx-auto flex items-center justify-center w-[70px] h-[70px] rounded-full mb-6">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Try Out Berkala</h3>
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
                        <img src="{{ asset('images/isnan.webp') }}" alt="Isnan Lian Darojad" class="rounded-2xl shadow-2xl w-64 h-64 object-cover mx-auto image-focus-top hover:scale-105 transition-transform duration-300">
                    </div>
                    <h2 class="text-3xl font-bold mb-2">Isnan Lian Darojad</h2>
                    <p class="text-xl text-gray-600 mb-4">Founder, Owner & Pengajar</p>
                    <p class="text-gray-600 italic">"Kejar apa yang kamu impikan dan doakan apa yang kamu usahakan"</p>
                </div>

                <div class="flex flex-col items-center text-center" data-aos="fade-left">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/gagah.webp') }}" alt="Gagah Edwin Kurniawan" class="rounded-2xl shadow-2xl w-64 h-64 object-cover mx-auto image-focus-top hover:scale-105 transition-transform duration-300">
                    </div>
                    <h2 class="text-3xl font-bold mb-2">Gagah Edwin Kurniawan</h2>
                    <p class="text-xl text-gray-600 mb-4">Owner Try Out Ruang Juang</p>
                    <p class="text-gray-600 italic">"Untuk melihat pelangi yang indah, kamu harus siap melewati badai, sebab pelangi lahir dari badai yang telah usai."</p>
                </div>
            </div>
        </div>
    </section>

    <section id="visi-misi" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Visi dan Misi</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-aos="fade-right" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-eye text-primary text-3xl mr-4"></i>
                        <h3 class="text-2xl font-bold text-primary">Visi</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">Menjadi lembaga bimbingan belajar dan tryout terbaik di Indonesia yang mendampingi generasi muda untuk meraih kesuksesan dalam seleksi sekolah kedinasan dan CPNS, dengan menyediakan layanan berkualitas, inovatif, dan berorientasi pada hasil.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-aos="fade-left" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-bullseye text-primary text-3xl mr-4"></i>
                        <h3 class="text-2xl font-bold text-primary">Misi</h3>
                    </div>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-3 flex-shrink-0"></i>
                            <span>Menyediakan materi pembelajaran berkualitas tinggi yang selalu terbarukan sesuai dengan standar ujian kedinasan dan CPNS</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-3 flex-shrink-0"></i>
                            <span>Menghadirkan tryout berkualitas yang sesuai dengan tes SKD asli dengan sistem penilaian yang akurat serta analisis hasil yang komprehensif</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-3 flex-shrink-0"></i>
                            <span>Menyediakan tutor profesional dan berpengalaman untuk memberikan bimbingan terbaik kepada siswa</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-3 flex-shrink-0"></i>
                            <span>Mengembangkan platform digital yang mudah diakses untuk mempermudah proses belajar dan tryout secara daring</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-3 flex-shrink-0"></i>
                            <span>Membangun komunitas belajar yang suportif dan inspiratif guna menciptakan lingkungan yang mendukung keberhasilan siswa</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-3 flex-shrink-0"></i>
                            <span>Berkomitmen terhadap kepuasan peserta melalui layanan prima dan hasil yang terukur</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="testimoni" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Apa Kata Mereka?</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Testimoni dari pejuang yang telah menggunakan Try Out dan layanan Ruang Juang</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">@g_m_s30</h4>
                        </div>
                    </div>
                    <p class="text-gray-600 italic text-justify">
                        "Aku pengen banget ikut TO di Ruang Juang karena menurutku TO-nya tuh paling mirip sama soal SKD asli ?? Awalnya aku sempat mikir tampilannya kok sederhana banget, eh ternyata pas SKD beneran suasananya dan tipe soalnya bener-bener sama! Aku juga suka banget karena pembahasannya jelas, hasilnya detail, dan bisa banget jadi bahan evaluasi biar makin mantap buat ujian ????"
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">@stellaaspace</h4>
                        </div>
                    </div>
                    <p class="text-gray-600 italic text-justify">
                        "Okay, kalau ditanya alasan buat ikut TO di Ruang Juang tuh karena TO di sini BENERAN WORTH IT BANGETTT! FYi guys, tahun 2025 adalah tahun pertama aku untuk coba SKD... testimoni orang-orang yang bilang 'kalau mau coba di ruangjuang deh, tampilannya mirip SKD asli, TWK-nya hampir plek ketiplek SKD' akhirnya aku beli dan memang bener worth it!!"
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">@_sastynn</h4>
                        </div>
                    </div>
                    <p class="text-gray-600 italic text-justify">
                        "Alasan ikut try out karena... Ruang Juang se worth it itu asli real no gimmick², kebetulan saya ikut kelas TWK di RJ ini bagus banget ganyangka bisa sebagus itu penjelasannya yang rapi dan mudah difahami, ngerangkul peserta buat ikut aktif berdiskusi dan 1 lagi soal² TWK di RJ emang bagus banget buat belajar lebih terarah dan tepat tidak melenceng ahh pokoknya nyesel ga tau dari taun pertama ??"
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="skor" class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-6" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-primary-dark">Skor Ambang Batas SKD</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Informasi skor ambang batas resmi yang ditetapkan untuk Seleksi Kompetensi Dasar (SKD) tes kedinasan dan CPNS.</p>
            </div>
            
            <div class="overflow-x-auto no-scrollbar" data-aos="fade-up" data-aos-delay="100">
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium">Tes Wawasan Kebangsaan (TWK)</td>
                            <td class="px-6 py-4">30 soal</td>
                            <td class="px-6 py-4">5</td>
                            <td class="px-6 py-4 font-bold text-primary-dark">65</td>
                            <td class="px-6 py-4">150</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium">Tes Intelegensia Umum (TIU)</td>
                            <td class="px-6 py-4">35 soal</td>
                            <td class="px-6 py-4">5</td>
                            <td class="px-6 py-4 font-bold text-primary-dark">80</td>
                            <td class="px-6 py-4">175</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium">Tes Karakteristik Pribadi (TKP)</td>
                            <td class="px-6 py-4">45 soal</td>
                            <td class="px-6 py-4">1-5</td>
                            <td class="px-6 py-4 font-bold text-primary-dark">166</td>
                            <td class="px-6 py-4">225</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-8 text-center text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    <p><strong>Catatan:</strong> Skor ambang batas dan jumlah soal dapat berubah sesuai dengan kebijakan terbaru dari BKN. Pastikan untuk selalu memeriksa informasi resmi.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="promo" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                    Promo Spesial
                </h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Lihat paket tryout terbaik dengan diskon terbesar yang kami tawarkan. Amankan paketmu sekarang!
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse ($tryouts as $tryout)
                    <div class="bg-white rounded-xl shadow-xl flex flex-col transform transition-all duration-300 hover:shadow-green-300 hover:shadow-2xl hover:-translate-y-0.5 relative group border border-gray-100 overflow-hidden" 
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="p-6 flex flex-col flex-grow">
                            @if ($tryout->discount_percentage > 0)
                            <div class="mb-3">
                                <span class="bg-green-600 text-white text-sm font-bold py-1 px-3 rounded-md shadow-md">
                                    DISKON {{ $tryout->discount_percentage }}%
                                </span>
                            </div>
                            @endif
                            <div class="min-h-[50px]"> 
                                <h3 class="font-bold text-lg text-gray-800 leading-tight line-clamp-2">
                                    {{ $tryout->title }}
                                </h3>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mb-6 mt-1">
                                @if($tryout->discount_percentage > 0)
                                    <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-medium">
                                        Hemat Rp {{ $tryout->formatted_discount }}
                                    </span>
                                @endif
                                @if($tryout->is_hots)
                                    <span class="bg-red-500 text-white px-4 py-1.5 rounded-full text-sm font-bold">
                                        HOTS
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-col items-start mb-6 mt-auto">
                                @if($tryout->discount && $tryout->discount > 0)
                                    <span class="text-gray-500 text-base line-through mb-1">
                                        Rp {{ number_format($tryout->price + $tryout->discount, 0, ',', '.') }}
                                    </span>
                                @endif
                                <span class="text-3xl font-extrabold text-green-700 block">
                                    Rp {{ number_format($tryout->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('tryout.payment', ['tryout_slug' => $tryout->slug]) }}"
                                    wire:navigate
                                    class="flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md w-full transition-colors duration-200">
                                    <i class="fa-solid fa-money-bill-wave mr-2"></i> Beli Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-4 text-center py-12" data-aos="fade-up">
                        <i class="fas fa-sad-tear text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Belum Ada Promo</h3>
                        <p class="text-gray-500">Saat ini belum ada promo tryout yang tersedia. Silakan cek kembali nanti!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- BAGIAN FAQ (FINAL VERSION) --}}
    {{-- ========================================================================= --}}
    <section id="faq" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Masih bingung? Berikut jawaban dari pertanyaan yang paling sering ditanyakan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                
                {{-- KOLOM KIRI --}}
                <div class="space-y-6">
                    
                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="100"
                         x-data="{ open: false }"> 
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Apa itu Ruang Juang?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" 
                               :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Ruang Juang adalah platform tryout dan bimbel khusus tes SKD Kedinasan dan CPNS. Kami menghadirkan simulasi soal dan tampilan terbaru sesuai standar tes SKD BKN, yang telah digunakan ribuan peserta untuk persiapan mereka.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="200"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Sejauh mana soal Ruang Juang mirip dengan tes asli?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Soal di Ruang Juang disusun dari kisi-kisi resmi dan selalu diperbarui sesuai update terbaru BKN. Dengan begitu, pola, tingkat kesulitan, hingga tampilan sangat mirip dengan tes SKD asli.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="300"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Apakah ada grup diskusi soal SKD?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Ya, ada grup diskusi/komunitas untuk saling <em>sharing</em> satu sama lain. <a href="https://chat.whatsapp.com/LZITffyUod3ErH7JjlD2hh" class="text-primary font-medium hover:underline" target="_blank"> (Klik di sini untuk info grup)</a>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="400"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Berapa lama masa aktif paket yang saya beli?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Masa aktif tryout adalah seumur hidup dan bisa diakses 24 jam beserta pembahasannya.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="500"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Bagaimana cara mendaftar dan mulai tryout?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Berikut langkah-langkah untuk mendaftar dan mulai tryout:<br><br>
                                1. Klik <strong>"Daftar"</strong> untuk membuat akun.<br>
                                2. Klik menu <strong>"Tryout/Bundle"</strong>.<br>
                                3. Pilih Tryout/Bundle yang ingin kamu beli.<br>
                                4. Klik tombol <strong>"Beli"</strong>.<br>
                                5. Klik <strong>"Untuk membuka pembayaran"</strong>.<br>
                                6. Pilih metode pembayaran dan lakukan pembayaran.<br>
                                7. Simpan bukti pembayaran yang sudah dilakukan.<br>
                                8. Jika pembayaran berhasil namun status masih pending, klik <strong>Refresh Status</strong>.<br>
                                9. Setelah pembayaran sukses, kembali ke dashboard.<br>
                                10. Klik menu <strong>"Tryoutku"</strong>.<br>
                                11. Selesai! Tryout kamu sudah siap dikerjakan kapan saja.
                            </div>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN --}}
                <div class="space-y-6">
                    
                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="100"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Apakah ada garansi jika saya mengalami kendala bug pada saat pelaksanaan tryout?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Kami menyediakan garansi dan akan bertanggungjawab penuh atas <em>bug</em> yang terjadi. Silakan adukan kendala ke <em>customer service</em> kami dan kamu akan mendapatkan 1 TO gratis bebas pilih.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="200"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Apakah hasil tryout langsung keluar dan ada peringkat nasional?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Ya, hasil tryout akan langsung muncul beserta pembahasan detail. Ruang Juang juga menyediakan peringkat nasional untuk mengukur posisi kamu.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="300"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Bagaimana cara mendapat hasil memuaskan di SKD dari tryout RJ?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Manfaatkan tryout secara berurutan, bahas kembali hasilnya, pelajari pembahasan, catat kesalahan. Kunci sukses adalah konsistensi dan evaluasi kelemahan.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="400"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Apakah bisa diakses lewat HP/laptop dan lebih maksimal memakai apa?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Ya, sistem berbasis web dan mobile friendly. Namun, tampilan tes SKD asli yang maksimal didapatkan saat menggunakan laptop/komputer.
                            </div>
                        </div>
                    </div>

                     <div class="faq-item bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300" 
                         data-aos="fade-up" data-aos-delay="500"
                         x-data="{ open: false }">
                        <button @click="open = !open" class="flex justify-between items-center w-full text-left font-semibold text-gray-800 hover:text-primary transition-colors focus:outline-none">
                            <span>Apakah saya bisa mengulang tryout yang sudah dikerjakan?</span>
                            <i class="fas fa-plus text-gray-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all duration-500"
                             :style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
                            <div class="pt-4 border-t border-gray-200 mt-4 text-gray-600">
                                Bisa. Tryout bisa dikerjakan sebanyak 3 kali setelah pembelian. Riwayat skor sebelumnya tetap tersimpan untuk evaluasi perkembanganmu.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section id="bantuan" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4">
                    Masih Ada Pertanyaan?
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Jangan ragu untuk menghubungi kami. Tim kami siap membantu.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 md:p-12 max-w-2xl mx-auto shadow-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="200">
                <div class="flex flex-wrap justify-center items-center space-y-6 md:space-y-0 md:space-x-8">
                    <a href="https://wa.me/6285769163218" target="_blank" class="flex flex-col items-center group w-1/3 md:w-auto p-2">
                        <div class="bg-green-100 p-4 rounded-full mb-3 transition-colors duration-300 group-hover:bg-green-200">
                            <i class="fab fa-whatsapp text-green-600 text-4xl"></i>
                        </div>
                        <span class="font-semibold text-gray-700 text-center text-sm">Customer Service 1</span>
                    </a>

                    <a href="https://chat.whatsapp.com/LZITffyUod3ErH7JjlD2hh" target="_blank" class="flex flex-col items-center group w-1/3 md:w-auto p-2">
                        <div class="bg-green-100 p-4 rounded-full mb-3 transition-colors duration-300 group-hover:bg-green-200">
                            <i class="fab fa-whatsapp text-green-600 text-4xl"></i>
                        </div>
                        <span class="font-semibold text-gray-700 text-center text-sm">Grup Komunitas WA</span> 
                    </a>

                    <a href="https://wa.me/6285769163218" target="_blank" class="flex flex-col items-center group w-1/3 md:w-auto p-2">
                        <div class="bg-green-100 p-4 rounded-full mb-3 transition-colors duration-300 group-hover:bg-green-200">
                            <i class="fab fa-whatsapp text-green-600 text-4xl"></i>
                        </div>
                        <span class="font-semibold text-gray-700 text-center text-sm">Customer Service 2</span>
                    </a>
                </div>
                <div class="w-full h-px bg-gray-200 my-8"></div>

                <div class="flex flex-col items-center">
                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                        <i class="fas fa-envelope text-gray-500 text-4xl"></i>
                    </div>
                    <p class="text-gray-700 font-medium text-center mb-1">Atau kirimkan pertanyaan Anda melalui email:</p>
                    <a href="mailto:inruangjuang@gmail.com" class="text-primary font-bold text-lg hover:underline transition-colors duration-300">
                        inruangjuang@gmail.com
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>