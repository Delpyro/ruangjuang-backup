<div class="flex flex-col h-screen"
    x-cloak
    x-data="{
        currentIndex: @entangle('currentIndex'),
        questionIds: @js($questionIds),
        savedAnswers: @entangle('questionStatus').live, {{-- Hanya bawa ID & status --}}
        localAnswerId: null,
        localIsDoubtful: false,
        isSaving: false,
        showSidebar: false,

        init() {
            this.syncLocalWithServer();
            this.initializeTimer();

            {{-- Watcher: Setiap pindah nomor soal, update pilihan radio di layar --}}
            $watch('currentIndex', () => {
                this.syncLocalWithServer();
            });
        },

        syncLocalWithServer() {
            let qId = this.questionIds[this.currentIndex];
            let data = this.savedAnswers[qId] || {};
            this.localAnswerId = data.selected_id || null;
            this.localIsDoubtful = data.is_doubtful || false;
        },

        getNavClass(index) {
            let qId = this.questionIds[index];
            let dbData = this.savedAnswers[qId];
            let baseClass = 'w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm hover:opacity-90 transition-all duration-150 ';
            
            if (dbData?.is_doubtful) {
                baseClass += 'bg-[#F9A825] '; {{-- Oranye (Ragu-ragu) --}}
            } else if (dbData?.answered) {
                baseClass += 'bg-[#16a34a] '; {{-- Hijau (Terjawab) --}}
            } else {
                baseClass += 'bg-[#dc2626] '; {{-- Merah (Belum) --}}
            }

            if (this.currentIndex === index) {
                baseClass += 'border-2 border-[#03A9F4] ring-2 ring-[#03A9F4]/50 ';
            }

            return baseClass;
        },

        async saveToDatabase() {
            this.isSaving = true;
            try {
                await $wire.saveAnswer(this.localAnswerId, this.localIsDoubtful);
            } catch (e) {
                console.error('Gagal menyimpan:', e);
            } finally {
                this.isSaving = false;
            }
        },

        async saveAndNext() {
            await this.saveToDatabase();
            this.scrollToTop();
        },

        async prev() {
            if (this.currentIndex > 0) {
                await $wire.goToQuestion(this.currentIndex - 1);
                this.scrollToTop();
            }
        },

        async goTo(index) {
            await $wire.goToQuestion(index);
            if (window.innerWidth < 768) this.showSidebar = false;
            this.scrollToTop();
        },

        scrollToTop() {
            let el = document.getElementById('question-scroll-viewport');
            if(el) el.scrollTop = 0;
        },

        initializeTimer() {
            const endTime = @js($endTime);
            if (!endTime) return;
            const targetTime = new Date(endTime).getTime();
            const timerEl = document.getElementById('timer');

            const timerInterval = setInterval(() => {
                const now = new Date().getTime();
                const diff = targetTime - now;
                if (diff > 0) {
                    const m = Math.floor(diff / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    timerEl.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                } else {
                    clearInterval(timerInterval);
                    timerEl.textContent = '00:00';
                    $wire.finishExam();
                }
            }, 1000);
        }
    }"
>
    {{-- HEADER --}}
    <header class="flex-shrink-0 bg-white border-b border-gray-200 px-4 py-2 flex justify-between items-center z-30">
        <div class="flex items-center space-x-2 text-gray-700 font-semibold">
            <i class="fas fa-user-circle text-blue-600 text-xl"></i>
            <span>{{ Auth::user()->name ?? 'Pengguna' }}</span>
        </div>
        <div id="timer" class="bg-[#2563EA] text-white px-4 py-1 rounded shadow font-bold text-xl" wire:ignore>
            --:--
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        {{-- Overlay Mobile --}}
        <div x-show="showSidebar" 
            x-transition.opacity
            @click="showSidebar = false" 
            class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>

        {{-- SIDEBAR NAVIGASI SOAL --}}
        <div :class="showSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-11/12 max-w-sm bg-white border-r overflow-y-auto p-4 shadow-xl
                    transform transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-64 md:shadow-none md:flex-shrink-0 md:z-auto">
                    
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button @click="showSidebar = false" class="text-gray-500 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2">
                @foreach($questionIds as $index => $id)
                    <button @click="goTo({{ $index }})"
                            :class="getNavClass({{ $index }})">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- AREA TENGAH (SOAL) --}}
        <div class="flex-1 flex flex-col relative overflow-hidden bg-gray-50">

            {{-- Header Mobile --}}
            <div class="sticky top-0 z-10 flex-shrink-0 bg-white border-b border-gray-200 flex justify-between items-center px-4 py-3 md:px-6 shadow md:hidden">
                <button @click="showSidebar = true" class="bg-[#2563EA] hover:bg-[#1a47b3] px-3 py-1 rounded flex items-center gap-2 text-white text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    Daftar Soal
                </button>
                <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg font-semibold text-sm">
                    Soal No. <span x-text="currentIndex + 1"></span>
                </div>
            </div>

            {{-- KONTEN SOAL SCROLLABLE --}}
            <div id="question-scroll-viewport" class="flex-1 overflow-y-auto py-4 md:py-6 px-4 md:px-8 pb-32 md:pb-6">
                
                {{-- Progress Bar --}}
                <div class="mb-3 relative w-full bg-gray-300 h-4 flex items-center justify-center overflow-hidden rounded">
                    <div class="bg-[#2563EA] h-full absolute top-0 left-0 transition-all duration-500" :style="`width: ${$wire.progressPercent}%` shadow: 0 0 10px rgba(37,99,235,0.3)"></div>
                    <span class="text-sm font-semibold z-10 text-black" x-text="Math.round($wire.progressPercent) + '%'"></span>
                </div>

                {{-- Kategori --}}
                <div class="bg-[#2563EA] text-white p-4 rounded-lg mb-4">
                    <h4 class="font-bold text-lg uppercase">
                        {{ $currentQuestion->subCategory->name ?? 'Kategori' }}
                    </h4>
                </div>

                {{-- Wrapper Soal --}}
                <div class="p-0 bg-gray-50 w-full">

                    {{-- Teks Soal --}}
                    <div class="mb-6 text-gray-800 text-base md:text-lg tinymce-content">
                        <span class="float-left mr-2 font-bold">{{ $currentIndex + 1 }}.</span>
                        <div class="overflow-x-auto">
                            {!! $currentQuestion->question !!}
                        </div>
                    </div>

                    {{-- Gambar Soal --}}
                    @if($currentQuestion->image)
                        <div class="my-4 p-2 border rounded-md bg-white shadow-sm">
                            <img src="{{ asset('storage/' . $currentQuestion->image) }}" class="max-w-full h-auto rounded-md mx-auto">
                        </div>
                    @endif

                    {{-- Pilihan Jawaban --}}
                    <div class="space-y-2 text-gray-700">
                        @foreach($currentQuestion->answers as $ans)
                            <label class="group flex items-start space-x-3 cursor-pointer p-3 rounded-lg border border-transparent hover:bg-gray-100 transition-colors duration-150 w-full"
                                :class="localAnswerId == {{ $ans->id }} ? 'border-[#2563EA] bg-blue-50' : ''">
                                
                                <input type="radio"
                                    name="jawaban"
                                    value="{{ $ans->id }}"
                                    x-model="localAnswerId"
                                    class="radio-custom-blue flex-shrink-0 mt-1">

                                <div class="flex-1 min-w-0 text-base md:text-lg text-gray-800 tinymce-content">
                                    <div class="prose max-w-none break-words">
                                        {!! $ans->answer !!}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- TOMBOL NAVIGASI --}}
                    <div class="fixed bottom-0 left-0 right-0 w-full bg-white border-t border-gray-200 p-3 z-20 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]
                                flex items-center justify-between gap-2
                                md:static md:w-auto md:bg-transparent md:border-none md:shadow-none md:p-0 md:mt-8 md:justify-start md:gap-3">

                        <button @click="prev()"
                                :disabled="currentIndex === 0"
                                class="flex-1 md:flex-none md:w-auto bg-[#2563EA] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-1
                                    flex items-center justify-center gap-1 text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                            Sebelumnya
                        </button>

                        <div class="flex-none md:w-auto flex justify-center md:justify-start md:order-2">
                            <label class="flex flex-col md:flex-row items-center gap-1 md:gap-2 cursor-pointer px-1">
                                <input type="checkbox"
                                    x-model="localIsDoubtful"
                                    class="text-ragu-ragu w-5 h-5 border-ragu-ragu rounded focus:ring-ragu-ragu">
                                <span class="text-gray-700 font-medium text-[10px] md:text-sm">Ragu-ragu</span>
                            </label>
                        </div>

                        @if($currentIndex < $totalQuestions - 1)
                            <button @click="saveAndNext()"
                                    :disabled="isSaving"
                                    class="flex-1 md:flex-none md:w-auto bg-[#2563EA] hover:bg-[#1a47b3] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-3
                                            flex items-center justify-center gap-1 text-sm md:text-base">
                                <span x-show="!isSaving">Simpan & Lanjut</span>
                                <span x-show="isSaving" class="flex items-center gap-1">
                                    <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                        @else
                            <button @click="await saveToDatabase(); $dispatch('show-finish-alert')"
                                    class="flex-1 md:flex-none md:w-auto bg-[#EF4444] hover:bg-[#B91C1C] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-3
                                            flex items-center justify-center gap-1 text-sm md:text-base">
                                Kumpulkan
                            </button>
                        @endif
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    .radio-custom-blue {
        appearance: none;
        background-color: #fff;
        margin: 0;
        font: inherit;
        color: #2563EA;
        width: 1.25em;
        height: 1.25em;
        border: 2px solid #D1D5DB;
        border-radius: 50%;
        display: grid;
        place-content: center;
        cursor: pointer;
    }
    .radio-custom-blue::before {
        content: "";
        width: 0.65em;
        height: 0.65em;
        border-radius: 50%;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em #2563EA;
    }
    .radio-custom-blue:checked::before { transform: scale(1); }
    .radio-custom-blue:checked { border-color: #2563EA; }

    .tinymce-content p { margin: 0 !important; }
    .text-ragu-ragu { color: #F9A825; }
    .border-ragu-ragu { border-color: #F9A825; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('show-finish-alert', () => {
        Swal.fire({
            title: "Kumpulkan Jawaban?",
            text: "Pastikan semua soal sudah kamu kerjakan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#16a34a",
            cancelButtonColor: "#dc2626",
            confirmButtonText: "Ya, Selesaikan!",
            cancelButtonText: "Cek Lagi"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('finishExam');
            }
        });
    });
</script>
@endpush