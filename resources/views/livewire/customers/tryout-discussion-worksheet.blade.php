<div x-data="discussionApp(@js($questionsData))">
    {{-- Container Utama --}}
    <div id="main-container" class="flex h-screen relative overflow-hidden"> 
        
        {{-- Overlay untuk Mobile --}}
        <div id="mobile-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden transition-opacity duration-300 opacity-0"></div>

        {{-- SIDEBAR (DAFTAR SOAL) --}}
        <div id="sidebar" 
             class="fixed inset-y-0 left-0 z-50 w-3/4 max-w-xs bg-white border-r overflow-y-scroll p-4 shadow-2xl
                    transform -translate-x-full transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-64 md:shadow-none md:flex-shrink-0"> 
            
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button id="close-sidebar-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2 md:grid-cols-5"> 
                <template x-for="(q, index) in questions" :key="q.id">
                    <button 
                        @click="navigateToQuestion(index)"
                        class="w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm transition relative"
                        :class="getSidebarClass(index)"> 
                        <span x-text="index + 1"></span>
                    </button>
                </template>
            </div>
            
            <div class="mt-6 pt-4 border-t">
                <a href="{{ route('tryout.my-results', $tryout->slug) }}" class="w-full inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    &larr; Ke Ringkasan Hasil
                </a>
            </div>
        </div>

        {{-- MAIN CONTENT (SOAL & PEMBAHASAN) --}}
        <div class="flex-1 flex flex-col relative min-w-0"> 
            {{-- TOP HEADER --}}
            <div class="bg-blue-700 text-white flex justify-between items-center px-4 py-3 sticky top-0 z-30 md:px-6 shadow-md"> 
                
                <button id="toggle-sidebar-btn" class="md:hidden bg-blue-600 hover:bg-blue-800 px-3 py-1 rounded flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span id="btn-text">Daftar Soal</span>
                </button>
                
                <h2 class="font-bold text-lg hidden md:block">Pembahasan: {{ $tryout->title }}</h2>
                <h2 class="font-bold text-lg block md:hidden truncate">Soal No. <span x-text="currentIndex + 1"></span></h2>

                <div class="text-sm font-semibold bg-blue-600 px-3 py-0.5 rounded shadow-lg md:text-xl md:px-4 md:py-1">
                    Pembahasan
                </div>
            </div>

            {{-- AREA SOAL --}}
            <div class="flex-1 py-3 overflow-y-auto bg-gray-50 px-3 md:py-6 md:px-6"> 
                
                <template x-if="currentQuestion">
                    <div class="space-y-3"> 
                        
                        {{-- NAMA SUB KATEGORI --}}
                        <div class="p-2 mb-2 rounded-lg bg-blue-600 text-white font-bold text-center text-sm md:text-base uppercase" x-text="currentQuestion.category_name + ' - ' + currentQuestion.subcategory_name">
                        </div>
                        
                        <div class="mb-3 text-gray-800"> 
                            <span class="float-left mr-2 flex-shrink-0 font-bold text-base md:text-lg" x-text="(currentIndex + 1) + '.'"></span>
                            <div class="overflow-x-auto" x-html="currentQuestion.question_html"></div> 
                        </div>

                        {{-- Gambar Soal (jika ada) --}}
                        <template x-if="currentQuestion.has_image">
                            <div class="my-3 p-1 border rounded-md">
                                <img :src="currentQuestion.image_url" alt="Gambar Soal" class="max-w-full h-auto rounded-md mx-auto">
                            </div>
                        </template>

                        {{-- Pilihan Jawaban --}}
                        <div class="space-y-2 text-gray-700">
                            <template x-for="ans in currentQuestion.answers" :key="ans.id">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center px-3 py-2 border rounded transition w-full"
                                     :class="ans.highlight_class">
                                    {{-- Teks Jawaban --}}
                                    <div class="flex items-start flex-1 min-w-0 w-full">
                                        <span class="font-bold mr-2 w-4 flex-shrink-0 text-left text-base" x-text="ans.letter + '.'"></span>
                                        <div class="max-w-none flex-1 min-w-0 overflow-x-auto" x-html="ans.answer_html"></div>
                                    </div>
                                    
                                    {{-- Status Riwayat --}}
                                    <template x-if="ans.status_badges.length > 0">
                                        <div class="mt-2 md:mt-0 md:ml-4 flex flex-wrap gap-1 text-xs font-semibold flex-shrink-0 md:text-right"> 
                                            <template x-for="badge in ans.status_badges">
                                                <span class="block px-2 py-0.5 rounded" :class="badge.colorClass" x-text="badge.text"></span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        
                        {{-- SECTION PEMBAHASAN --}}
                        <div class="mt-6 pt-4 border-t border-dashed border-gray-300"> 
                            <details open>
                                <summary class="font-extrabold text-base md:text-lg cursor-pointer text-indigo-700 hover:text-indigo-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    Penjelasan
                                </summary>
                                <div class="max-w-none mt-3 p-4 bg-indigo-50 rounded-lg text-gray-800 overflow-x-auto">
                                    <template x-if="currentQuestion.explanation_html">
                                        <div x-html="currentQuestion.explanation_html"></div>
                                    </template>
                                    <template x-if="!currentQuestion.explanation_html">
                                        <p class="text-sm italic text-gray-600">Mohon maaf, pembahasan belum tersedia untuk soal ini.</p>
                                    </template>
                                </div>
                            </details>
                        </div>

                        {{-- Tombol Navigasi Bawah --}}
                        <div class="mt-4 pb-4 flex flex-row flex-wrap justify-center md:justify-start items-center gap-2">
                            
                            <button 
                                @click="previousQuestion"
                                :disabled="currentIndex === 0"
                                class="bg-blue-600 text-white font-semibold px-3 py-1 rounded-lg shadow-md w-auto h-8 text-sm"
                                :class="currentIndex === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'">
                                &larr; Sebelumnya
                            </button>
                            
                            <a href="{{ route('tryout.my-results', $tryout->slug) }}"
                               class="bg-gray-600 text-white font-semibold px-3 py-1 rounded-lg shadow-md w-auto h-8 hover:bg-gray-700 text-center text-sm">
                                Ke Ringkasan
                            </a>
                            
                            <button 
                                @click="nextQuestion"
                                :disabled="currentIndex === totalQuestions - 1"
                                class="bg-blue-600 text-white font-semibold px-3 py-1 rounded-lg shadow-md w-auto h-8 text-sm"
                                :class="currentIndex === totalQuestions - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'">
                                Selanjutnya &rarr;
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="!currentQuestion">
                    <div class="p-6 border rounded-lg shadow-lg bg-white w-full"> 
                        <p class="text-lg font-semibold text-gray-700 text-center">Gagal memuat soal. Silakan muat ulang halaman.</p>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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

    /* Animasi untuk details summary */
    details[open] summary svg {
        transform: rotate(180deg);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('discussionApp', (questionsData) => ({
            questions: questionsData,
            currentIndex: 0,

            get currentQuestion() {
                return this.questions[this.currentIndex] || null;
            },
            get totalQuestions() {
                return this.questions.length;
            },

            nextQuestion() {
                if (this.currentIndex < this.totalQuestions - 1) this.currentIndex++;
            },
            previousQuestion() {
                if (this.currentIndex > 0) this.currentIndex--;
            },
            navigateToQuestion(index) {
                this.currentIndex = index;
                if (window.matchMedia('(max-width: 767px)').matches) {
                    document.getElementById('close-sidebar-btn').click();
                }
            },

            // Fungsi Sidebar dengan warna Merah/Hijau dan Border Biru saat aktif
            getSidebarClass(index) {
                let q = this.questions[index];
                
                // 1. Tentukan warna dasar (Hijau jika benar, Merah jika salah/tidak dijawab)
                let baseColor = q.is_correct ? 'bg-green-600' : 'bg-red-600';

                // 2. Jika sedang dipilih, tambahkan border biru (cyan) persis layout lama
                if (this.currentIndex === index) {
                    return `${baseColor} border-2 border-green-700 z-10 scale-105 border-2 border-cyan-400 ring-2 ring-cyan-200 shadow-lg`;
                }
                
                return baseColor;
            }
        }));
    });

    // Script DOM Toggle Sidebar
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');
        const overlay = document.getElementById('mobile-overlay');

        if (!sidebar || !toggleBtn || !closeBtn || !overlay) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden', 'opacity-0');
            overlay.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300); 
        }

        toggleBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        if (window.matchMedia('(min-width: 768px)').matches) {
             sidebar.classList.remove('-translate-x-full'); 
             overlay.classList.add('hidden'); 
        }
    });
</script>
@endpush