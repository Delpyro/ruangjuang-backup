{{-- 
    FILE: resources/views/livewire/customers/tryout-worksheet.blade.php 
--}}

<div class="flex flex-col h-screen">

    {{-- 1. HEADER GLOBAL (Nama User & Timer) --}}
    <div class="flex-shrink-0 bg-white shadow z-30 border-b border-gray-200">
        <div class="flex justify-between items-center py-1 px-4 md:py-2">
            
            {{-- Nama User --}}
            <div class="text-sm md:text-base font-semibold text-gray-700 truncate max-w-[50%] flex items-center space-x-2">
                <i class="fas fa-user-circle text-lg text-blue-600"></i>
                <span class="hidden sm:inline">
                    {{ Auth::user()->name ?? 'Pengguna' }}
                </span>
                <span class="inline sm:hidden">
                    {{ Auth::user()->name ?? 'Pengguna' }}
                </span>
            </div>

            {{-- Timer --}}
            <div id="timer" class="text-base md:text-xl font-semibold bg-timer-blue text-white px-3 md:px-4 py-1 rounded shadow-lg transition duration-500 ease-in-out" wire:ignore>
                --:--
            </div>
        </div>
    </div>

    {{-- 2. KONTAINER UTAMA --}}
    <div id="main-container" class="flex flex-1 relative overflow-hidden">

        {{-- Overlay Mobile (Saat Sidebar Terbuka) --}}
        <div id="mobile-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden transition-opacity duration-300 opacity-0"></div>

        {{-- SIDEBAR NAVIGASI SOAL --}}
        <div id="sidebar"
             class="fixed inset-y-0 left-0 z-50 w-11/12 max-w-sm bg-white border-r overflow-y-auto p-4 shadow-xl
                    transform -translate-x-full transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-64 md:shadow-none md:flex-shrink-0 md:z-auto">
                    
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button id="close-sidebar-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2">
                @for ($index = 0; $index < $totalQuestions; $index++)
                    <button
                        wire:click="navigateToQuestion({{ $index }})"
                        class="w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm
                                hover:opacity-80 transition-all duration-150
                                {{ $this->getQuestionStatusClass($index) }}
                                @if($currentQuestionIndex === $index)
                                    border-2 border-[#03A9F4] ring-2 ring-[rgba(3,169,244,0.5)]
                                @endif
                                ">
                        {{ $index + 1 }}
                    </button>
                @endfor
            </div>
        </div>

        {{-- AREA TENGAH (SOAL) --}}
        <div class="flex-1 flex flex-col relative overflow-y-auto bg-gray-50">

            {{-- Header Mobile (Tombol Buka Sidebar) --}}
            <div class="sticky top-0 z-10 flex-shrink-0 bg-white border-b border-gray-200 flex justify-between items-center px-4 py-3 md:px-6 shadow md:hidden">
                <button id="toggle-sidebar-btn" class="bg-[#2563EA] hover:bg-[#1a47b3] px-3 py-1 rounded flex items-center gap-2 text-white text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    Daftar Soal
                </button>
                <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg font-semibold text-sm">
                    Soal No. {{ $currentQuestionIndex + 1 }}
                </div>
            </div>

            {{-- KONTEN SOAL SCROLLABLE --}}
            {{-- NOTE: pb-32 untuk memberi ruang tombol fixed di mobile --}}
            <div class="flex-1 py-4 md:py-6 px-4 md:px-8 pb-32 md:pb-6">
                @if ($currentQuestion)

                    {{-- Progress Bar --}}
                    <div class="mb-3 relative w-full bg-gray-300 h-4 flex items-center justify-center overflow-hidden rounded">
                        <div class="bg-[#2563EA] h-full absolute top-0 left-0 transition-all duration-500" style="width: {{ $progressPercent }}%;"></div>
                        <span class="text-sm font-semibold z-10 text-black shadow-text">{{ round($progressPercent) }}%</span>
                    </div>

                    {{-- Kategori --}}
                    <div class="bg-[#2563EA] text-white p-4 rounded-lg mb-4">
                        <h4 class="font-bold text-lg uppercase">
                            {{ $currentQuestion->subCategory->name ?? 'Subkategori Soal' }}
                        </h4>
                    </div>

                    {{-- Wrapper Soal & Jawaban --}}
                    <div class="p-0 bg-gray-50 w-full"
                           wire:key="question-{{ $currentQuestion->id }}"
                           x-data="{ 
                               selected: @entangle('selectedAnswerId').live, 
                               doubtful: @entangle('isDoubtful').live 
                           }">

                        {{-- Teks Soal --}}
                        <div class="mb-6 text-gray-800 text-base md:text-lg tinymce-content">
                            <span class="float-left mr-2 font-bold">{{ $currentQuestionIndex + 1 }}.</span>
                            <div class="overflow-x-auto">{!! $currentQuestion->question !!}</div>
                        </div>

                        {{-- Gambar Soal --}}
                        @if ($currentQuestion->image)
                            <div class="my-4 p-2 border rounded-md bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $currentQuestion->image) }}" alt="Gambar Soal" class="max-w-full h-auto rounded-md mx-auto">
                            </div>
                        @endif

                        {{-- 
                            =========================================================================
                            BAGIAN PILIHAN JAWABAN (FIXED WORD BREAK)
                            =========================================================================
                        --}}
                        <div class="space-y-2 text-gray-700">
                            @foreach ($currentQuestion->answers as $answer)
                                <label class="group flex items-start space-x-3 cursor-pointer p-3 rounded-lg border border-transparent hover:bg-gray-100 transition-colors duration-150 w-full
                                            @if($selectedAnswerId == $answer->id) border-[#2563EA] bg-blue-50 @endif">
                                    
                                    <input type="radio"
                                           x-model="selected"
                                           @click="doubtful = false"
                                           wire:loading.attr="disabled" 
                                           name="jawaban_{{ $currentQuestion->id }}"
                                           value="{{ $answer->id }}"
                                           class="radio-custom-blue flex-shrink-0 mt-1">

                                    {{-- 
                                        REVISI DI SINI: 
                                        1. Tetap pakai min-w-0 dan w-0 untuk atasi overflow.
                                        2. HAPUS 'break-all', hanya sisakan 'break-words'.
                                        3. break-words = Bungkus teks per kata (Whole Word Wrapping).
                                    --}}
                                    <div class="flex-1 min-w-0 w-0 text-base md:text-lg text-gray-800 tinymce-content">
                                        <div class="prose max-w-none break-words">
                                            {!! $answer->answer !!}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- 
                           ================================================================
                           AREA TOMBOL NAVIGASI
                           ================================================================
                        --}}
                        <div class="
                            fixed bottom-0 left-0 right-0 w-full bg-white border-t border-gray-200 p-3 z-20 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]
                            flex items-center justify-between gap-2
                            md:static md:w-auto md:bg-transparent md:border-none md:shadow-none md:p-0 md:mt-8 md:justify-start md:gap-3
                        ">

                            {{-- 1. Tombol Sebelumnya --}}
                            <button
                                wire:click="previousQuestion"
                                @if($currentQuestionIndex == 0) disabled @endif
                                class="flex-1 md:flex-none md:w-auto bg-[#2563EA] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-1
                                       flex items-center justify-center gap-1 text-sm md:text-base
                                       {{ $currentQuestionIndex == 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#1a47b3]' }}">
                                Sebelumnya
                            </button>

                            {{-- 2. Checkbox Ragu-ragu --}}
                            <div class="flex-none md:w-auto flex justify-center md:justify-start md:order-2">
                                <label class="flex flex-col md:flex-row items-center gap-1 md:gap-2 cursor-pointer px-1">
                                    <input type="checkbox"
                                           x-model="doubtful"
                                           class="text-ragu-ragu w-5 h-5 border-ragu-ragu rounded focus:ring-ragu-ragu">
                                    <span class="text-gray-700 font-medium text-[10px] md:text-sm">Ragu-ragu</span>
                                </label>
                            </div>

                            {{-- 3. Tombol Lanjut / Selesai --}}
                            @if ($currentQuestionIndex < $totalQuestions - 1)
                                <button
                                    wire:click="saveAndNext"
                                    class="flex-1 md:flex-none md:w-auto bg-[#2563EA] hover:bg-[#1a47b3] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-3
                                            flex items-center justify-center gap-1 text-sm md:text-base">
                                    Simpan & Lanjut
                                </button>
                            @else
                                <button
                                    wire:click.prevent="showFinishConfirmation" 
                                    class="flex-1 md:flex-none md:w-auto bg-[#EF4444] hover:bg-[#B91C1C] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 transition-colors duration-150 md:order-3
                                            flex items-center justify-center gap-1 text-sm md:text-base">
                                    Kumpulkan
                                </button>
                            @endif

                        </div>
                    </div> 
                @else
                {{-- Jika Error Memuat Soal --}}
                <div class="p-6 border rounded-lg shadow-lg bg-white mx-auto text-center">
                    <p class="text-lg font-semibold text-gray-700">Gagal memuat soal. Silakan muat ulang halaman.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- CSS TAMBAHAN --}}
@push('styles')
<style>
    /* === FIX CSS UNTUK KONTEN TINYMCE YANG OVERFLOW === */
    .tinymce-content * {
        white-space: normal !important;       /* Paksa teks wrap normal */
        word-wrap: break-word !important;     /* Potong perkata jika perlu */
        overflow-wrap: break-word !important; 
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    /* Pastikan table di dalam soal juga responsive */
    .tinymce-content table {
        display: block;
        width: 100% !important;
        overflow-x: auto;
    }
    
    .tinymce-content img {
        height: auto !important;
        max-width: 100% !important;
        display: block;
    }
    
    .tinymce-content p {
        margin-bottom: 0.5rem !important;
        margin-top: 0 !important;
    }

    /* === ANIMASI & UTILS LAINNYA === */
    @keyframes blink-timer-animation {
        0%, 100% { background-color: white; color: #EF4444; }
        50% { background-color: #EF4444; color: white; }
    }
    .blink {
        animation: blink-timer-animation 1s infinite;
    }

    .shadow-text {
        text-shadow: 0 0 2px rgba(255, 255, 255, 0.7);
    }
    .radio-custom-blue {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        border: 2px solid #D1D5DB;
        background-color: white;
        flex-shrink: 0;
        margin-top: 0.25rem;
        cursor: pointer;
    }
    .radio-custom-blue:checked {
        background-color: #2563EA !important;
        border-color: #2563EA !important;
        box-shadow: inset 0 0 0 4px white;
    }
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
    input[type="checkbox"].text-ragu-ragu:checked {
        background-color: #F9A825 !important;
        border-color: #F9A825 !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-size: 1em 1em;
        background-position: center center;
        background-repeat: no-repeat;
    }
</style>
@endpush

@push('scripts')
<script>
    window.appTimerInterval = null;

    function initializeTimer() {
        // Ambil waktu selesai dari properti Livewire/PHP
        const endTimeElement = document.querySelector('[wire\\:key^="question-"], [x-data]'); 
        const endTime = @js($endTime ?? null); 
        
        if (!endTime) {
            return;
        }

        const targetTime = new Date(endTime).getTime();
        const timerEl = document.getElementById('timer');

        if (!timerEl) {
            return;
        }

        // Reset interval jika sebelumnya sudah jalan (penting saat navigasi livewire)
        if (window.appTimerInterval) {
            clearInterval(window.appTimerInterval);
        }

        function updateTimer() {
            const now = new Date().getTime();
            const distance = targetTime - now;

            if (distance > 0) {
                // ============================================================
                // PERBAIKAN DI SINI:
                // Menghitung total menit secara mutlak tanpa membagi modulus jam
                // ============================================================
                const minutes = Math.floor(distance / (1000 * 60)); 
                
                // Detik tetap pakai modulus 60
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Format string menit:detik (misal: 120:05)
                const timeText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                timerEl.textContent = timeText;
                
                // Logika blink (merah) jika waktu kurang dari 10 menit (600.000 ms)
                if (distance <= 600000) { 
                    timerEl.classList.add('blink');
                    timerEl.classList.remove('bg-timer-blue'); 
                    timerEl.classList.remove('text-white'); 
                } else {
                    timerEl.classList.remove('blink');
                    timerEl.classList.add('bg-timer-blue');
                    timerEl.classList.add('text-white'); 
                }
            } else {
                // Waktu Habis
                clearInterval(window.appTimerInterval);
                timerEl.textContent = '00:00';
                timerEl.classList.remove('blink'); 
                
                // Panggil method Livewire finishExam
                @this.call('finishExam');
            }
        }

        updateTimer(); // Jalankan sekali agar tidak delay 1 detik
        window.appTimerInterval = setInterval(updateTimer, 1000); 
    }

    function setupSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');
        const overlay = document.getElementById('mobile-overlay');

        if (!sidebar || !toggleBtn || !closeBtn || !overlay) {
            return;
        }
        
        const body = document.body; 

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden', 'opacity-0');
            overlay.classList.add('opacity-100');
            body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            body.classList.remove('overflow-hidden');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300); 
        }

        // Cloning elemen untuk menghapus event listener lama (mencegah duplikasi)
        toggleBtn.replaceWith(toggleBtn.cloneNode(true));
        closeBtn.replaceWith(closeBtn.cloneNode(true));
        overlay.replaceWith(overlay.cloneNode(true));

        // Re-attach event listeners
        document.getElementById('toggle-sidebar-btn').addEventListener('click', openSidebar);
        document.getElementById('close-sidebar-btn').addEventListener('click', closeSidebar);
        document.getElementById('mobile-overlay').addEventListener('click', closeSidebar);

        // Tutup sidebar otomatis di mobile saat navigasi soal via Livewire
        Livewire.hook('message.processed', (message, component) => {
            const isQuestionNavigation = message.updateQueue.some(update => 
                update.type === 'call' && 
                ['navigateToQuestion', 'saveAndNext', 'previousQuestion'].includes(update.method)
            );
            
            if (isQuestionNavigation && window.matchMedia('(max-width: 767px)').matches) {
                closeSidebar();
            }
        });
    }

    function setupCustomAlerts() {
        Livewire.on('show-finish-alert', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "Yakin Selesaikan Ujian?",
                    text: "Pastikan semua soal sudah dijawab. Anda akan diarahkan ke halaman hasil Tryout.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#2563EA",
                    cancelButtonColor: "#EF4444",
                    confirmButtonText: "Ya, Selesaikan!",
                    cancelButtonText: "Batal, Cek Lagi",
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('finishExam'); 
                    }
                });
            } else {
                if(confirm("Anda yakin ingin menyelesaikan ujian ini? Setelah ini, Anda akan diarahkan ke hasil Tryout.")) {
                    @this.call('finishExam');
                }
            }
        });
    }

    function initWorksheetPage() {
        initializeTimer();
        setupSidebar();
        setupCustomAlerts();
    }

    // Jalankan saat load awal & saat navigasi Livewire (SPA mode)
    document.addEventListener('DOMContentLoaded', initWorksheetPage);
    document.addEventListener('livewire:navigated', initWorksheetPage);

</script>
@endpush