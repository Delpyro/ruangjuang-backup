{{-- Data soal dari server — di script tag agar tidak merusak atribut HTML --}}
<script>
    window.__CBT__ = {
        questions:       {!! $questionsJson !!},
        endedAt:         '{{ $endedAt }}',
        userTryoutId:    {{ $userTryoutId }},
        userId:          {{ Auth::id() ?? 0 }},
        serverTimestamp: {{ $serverTimestamp }},
    };
</script>

<div class="flex flex-col h-screen"
    wire:ignore
    x-cloak
    x-data="cbtApp()"
    x-init="init()"
>

    {{-- ================================================================== --}}
    {{-- HEADER --}}
    {{-- ================================================================== --}}
    <header class="flex-shrink-0 bg-white border-b border-gray-200 px-4 py-2 flex justify-between items-center z-30">
        <div class="flex items-center space-x-2 text-gray-700 font-semibold">
            <i class="fas fa-user-circle text-blue-600 text-xl"></i>
            <span>{{ Auth::user()->name ?? 'Pengguna' }}</span>
        </div>
        <div id="timer" class="bg-[#2563EA] text-white px-4 py-1 rounded shadow font-bold text-xl transition-colors duration-500">
            --:--
        </div>
    </header>

    {{-- ================================================================== --}}
    {{-- BODY --}}
    {{-- ================================================================== --}}
    <div class="flex flex-1 overflow-hidden relative">

        {{-- Overlay Mobile --}}
        <div x-show="showSidebar"
             x-transition.opacity
             @click="showSidebar = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>

        {{-- ========================================================== --}}
        {{-- SIDEBAR --}}
        {{-- ========================================================== --}}
        <div :class="showSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
             class="fixed inset-y-0 left-0 z-50 w-11/12 max-w-sm bg-white border-r overflow-y-auto p-4 shadow-xl
                    transform transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-64 md:shadow-none md:flex-shrink-0 md:z-auto">

            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button @click="showSidebar = false" class="text-gray-500 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2">
                <template x-for="(q, idx) in questions" :key="q.id">
                    <button @click="navigateTo(idx)" :class="getNavClass(idx)" x-text="idx + 1"></button>
                </template>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- AREA TENGAH --}}
        {{-- ========================================================== --}}
        <div class="flex-1 flex flex-col relative overflow-hidden bg-gray-50">

            {{-- Loading Overlay saat finishing --}}
            <div x-show="isFinishing"
                 x-transition.opacity.duration.200ms
                 style="display: none;"
                 class="absolute inset-0 z-50 flex items-center justify-center bg-white/70 backdrop-blur-sm">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-12 w-12 text-[#2563EA] mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    <span class="text-gray-700 font-semibold text-lg">Menyimpan Jawaban...</span>
                </div>
            </div>

            {{-- Mobile Header --}}
            <div class="sticky top-0 z-10 flex-shrink-0 bg-white border-b border-gray-200 flex justify-between items-center px-4 py-3 md:px-6 shadow md:hidden">
                <button @click="showSidebar = true"
                        class="bg-[#2563EA] hover:bg-[#1a47b3] px-3 py-1 rounded flex items-center gap-2 text-white text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
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
                    <div class="bg-[#2563EA] h-full absolute top-0 left-0 transition-all duration-500"
                         :style="'width: ' + progressPercent + '%'"
                         style="box-shadow: 0 0 10px rgba(37,99,235,0.3);"></div>
                    <span class="text-sm font-semibold z-10 text-black" x-text="Math.round(progressPercent) + '%'"></span>
                </div>

                <template x-if="questions.length > 0 && currentQuestion">
                    <div>
                        {{-- Header Kategori --}}
                        <div class="bg-[#2563EA] text-white p-4 rounded-lg mb-4">
                            <h4 class="font-bold text-lg uppercase" x-text="currentQuestion.subcategory"></h4>
                        </div>

                        <div class="p-0 bg-gray-50 w-full">

                            {{-- Teks Soal --}}
                            <div class="mb-6 text-gray-800 text-base md:text-lg tinymce-content">
                                <span class="float-left mr-2 font-bold" x-text="(currentIndex + 1) + '.'"></span>
                                <div class="overflow-x-auto" x-html="currentQuestion.html"></div>
                            </div>

                            {{-- Gambar Soal --}}
                            <template x-if="currentQuestion.image">
                                <div class="my-4 p-2 border rounded-md bg-white shadow-sm">
                                    <img :src="currentQuestion.image" class="max-w-full h-auto rounded-md mx-auto" alt="Gambar soal">
                                </div>
                            </template>

                            {{-- Pilihan Jawaban --}}
                            <div class="space-y-2 text-gray-700">
                                <template x-for="ans in currentQuestion.answers" :key="ans.id">
                                    <label
                                        class="group flex items-start space-x-3 cursor-pointer p-3 rounded-lg border border-transparent hover:bg-gray-100 transition-colors duration-150 w-full"
                                        :class="pendingAnswerId === ans.id ? 'border-[#2563EA] bg-blue-50' : ''"
                                        @click.prevent="pendingAnswerId = ans.id"
                                    >
                                        <input type="radio"
                                               :name="'jawaban_' + currentQuestion.id"
                                               :checked="pendingAnswerId === ans.id"
                                               @click.stop="pendingAnswerId = ans.id"
                                               class="radio-custom-blue flex-shrink-0 mt-1"
                                               style="pointer-events:none;">

                                        <div class="flex-1 min-w-0 text-base md:text-lg text-gray-800 tinymce-content">
                                            <div class="prose max-w-none break-words" x-html="ans.html"></div>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            {{-- TOMBOL NAVIGASI BAWAH --}}
                            <div class="fixed bottom-0 left-0 right-0 w-full bg-white border-t border-gray-200 p-3 z-20 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]
                                        flex items-center justify-between gap-2
                                        md:static md:w-auto md:bg-transparent md:border-none md:shadow-none md:p-0 md:mt-8 md:justify-start md:gap-3">

                                {{-- Sebelumnya --}}
                                <button @click="navigateTo(currentIndex - 1)"
                                        :disabled="currentIndex === 0 || isFinishing || isSaving"
                                        class="flex-1 md:flex-none md:w-auto bg-[#2563EA] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-1
                                               flex items-center justify-center gap-1 text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                                    Sebelumnya
                                </button>

                                {{-- Checkbox Ragu-ragu --}}
                                <div class="flex-none md:w-auto flex justify-center md:justify-start md:order-2">
                                    <label class="flex flex-col md:flex-row items-center gap-1 md:gap-2 cursor-pointer px-1">
                                        <input type="checkbox" x-model="pendingIsDoubtful" class="checkbox-ragu-ragu">
                                        <span class="text-gray-700 font-medium text-[10px] md:text-sm">Ragu-ragu</span>
                                    </label>
                                </div>

                                {{-- Simpan & Lanjutkan --}}
                                <button x-show="currentIndex < questions.length - 1"
                                        @click="saveAndNext()"
                                        :disabled="isFinishing || isSaving"
                                        class="flex-1 md:flex-none md:w-auto bg-[#2563EA] hover:bg-[#1a47b3] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-3
                                               flex items-center justify-center gap-1 text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="!isSaving">
                                        <span>Simpan &amp; Lanjutkan</span>
                                    </template>
                                    <template x-if="isSaving">
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            Menyimpan...
                                        </span>
                                    </template>
                                </button>

                                {{-- Simpan & Kumpulkan --}}
                                <button x-show="currentIndex >= questions.length - 1"
                                        @click="saveAndShowFinishAlert()"
                                        :disabled="isFinishing || isSaving"
                                        class="flex-1 md:flex-none md:w-auto bg-[#EF4444] hover:bg-[#B91C1C] text-white font-semibold px-2 py-2 md:px-4 rounded-lg shadow-md h-10 md:order-3
                                               flex items-center justify-center gap-1 text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                                    Simpan &amp; Kumpulkan
                                </button>

                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="questions.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <p class="text-gray-500 font-medium text-lg">Soal tidak tersedia</p>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .radio-custom-blue {
        appearance: none; background-color: #fff; margin: 0;
        color: #2563EA; width: 1.25em; height: 1.25em;
        border: 2px solid #D1D5DB; border-radius: 50%;
        display: grid; place-content: center; cursor: pointer;
    }
    .radio-custom-blue::before {
        content: ""; width: 0.65em; height: 0.65em; border-radius: 50%;
        transform: scale(0); transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em #2563EA;
    }
    .radio-custom-blue:checked::before { transform: scale(1); }
    .radio-custom-blue:checked { border-color: #2563EA; }

    .checkbox-ragu-ragu {
        appearance: none; -webkit-appearance: none; background-color: #fff;
        border: 2px solid #F9A825 !important; width: 1.25rem; height: 1.25rem;
        border-radius: 0.25rem; display: inline-grid; place-content: center;
        cursor: pointer; transition: all 0.2s ease-in-out;
    }
    .checkbox-ragu-ragu:checked { background-color: #F9A825 !important; background-image: none !important; }
    .checkbox-ragu-ragu:checked::before {
        content: ""; width: 0.65rem; height: 0.65rem;
        clip-path: polygon(14% 44%, 0 58%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        background-color: white !important; transform: scale(1);
    }
    .checkbox-ragu-ragu:focus {
        outline: none !important;
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(249,168,37,0.5) !important;
    }
    .tinymce-content p { margin: 0 !important; }
</style>
@endpush

@push('scripts')
<script>


// ============================================================================
// [FIX #4] Global Livewire 419 / session expired handler
// ============================================================================
document.addEventListener('livewire:request-failed', (e) => {
    const status = e.detail?.response?.status || e.detail?.status;
    if (status === 419 || status === 401) {
        Swal.fire({
            title: 'Sesi Login Habis',
            html: 'Sesi kamu sudah berakhir.<br><b>Jawaban yang sudah disimpan tetap aman.</b><br>Silakan login ulang untuk melanjutkan.',
            icon: 'warning',
            confirmButtonText: 'Login Ulang',
            confirmButtonColor: '#2563EA',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then(() => {
            window.location.href = '/login';
        });
    }
});

// ============================================================================
// Alpine Component
// ============================================================================
function cbtApp() {
    const cbt = window.__CBT__ || {};

    return {
        questions:    cbt.questions    || [],
        userTryoutId: cbt.userTryoutId || 0,
        userId:       cbt.userId       || 0,

        currentIndex: 0,

        clockOffset: (cbt.serverTimestamp || Date.now()) - Date.now(),

        // STATE DUA LAPISAN
        committedAnswers: {},
        pendingAnswerId:   null,
        pendingIsDoubtful: false,
        dirtyIds:          new Set(),

        // UI flags
        showSidebar: false,
        isFinishing: false,
        isSaving:    false,

        // [FIX #C] Anti-concurrent flush flag
        isFlushing: false,

        // [FIX #D] Interval refs untuk cleanup
        _timerInterval:    null,
        _autoSaveInterval: null,
        _pingInterval:     null,

        // [FIX #E] AbortController untuk event listeners
        _abortController: null,

        // ============================
        // INIT
        // ============================
        init() {
            // Restore committedAnswers dari server
            this.questions.forEach(q => {
                if (q.savedAnswerId !== null || q.savedDoubtful) {
                    this.committedAnswers[q.id] = {
                        answerId:   q.savedAnswerId ? parseInt(q.savedAnswerId) : null,
                        isDoubtful: !!q.savedDoubtful,
                    };
                }
            });

            this.loadPendingFromCommitted(0);
            this.initTimer();
            this.initAutoSave();
            this.restoreFromLocalStorage();
            this.registerEventListeners();
        },

        // [FIX #D,E] Lifecycle cleanup — dipanggil Alpine saat komponen di-destroy
        destroy() {
            if (this._timerInterval)    clearInterval(this._timerInterval);
            if (this._autoSaveInterval) clearInterval(this._autoSaveInterval);
            if (this._pingInterval)     clearInterval(this._pingInterval);
            if (this._abortController)  this._abortController.abort();
        },

        // [FIX #E] Register semua event listeners dengan AbortController
        registerEventListeners() {
            // Abort listeners lama jika ada (re-render)
            if (this._abortController) this._abortController.abort();
            this._abortController = new AbortController();
            const signal = this._abortController.signal;

            // Finish trigger dari SweetAlert confirm
            window.addEventListener(
                'do-safe-finish-exam',
                () => this.doFinish(),
                { signal }
            );

            // [FIX BUG #3] SweetAlert finish alert
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
                        window.dispatchEvent(new CustomEvent('do-safe-finish-exam'));
                    }
                });
            }, { signal });

            // [FIX #A] beforeunload: gunakan fetch keepalive (browser guarantee delivery)
            window.addEventListener('beforeunload', () => {
                if (this.dirtyIds.size === 0) return;
                const payload = [...this.dirtyIds].map(qId => {
                    const c = this.committedAnswers[qId] ?? {};
                    return {
                        questionId: qId,
                        answerId:   c.answerId ? parseInt(c.answerId) : null,
                        isDoubtful: !!c.isDoubtful,
                    };
                });
                const token = document.querySelector('meta[name=csrf-token]')?.content || '';
                // keepalive: true = browser jamin kirim meski tab ditutup
                fetch('/cbt/beacon-save', {
                    method:    'POST',
                    headers:   { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body:       JSON.stringify({ userTryoutId: this.userTryoutId, payload }),
                    keepalive: true,
                }).catch(() => {});
            }, { signal });

            // [FIX #C] visibilitychange: flush saat tab disembunyikan
            window.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') this.flushDirty();
            }, { signal });

            // Keep-alive ping tiap 4 menit
            if (this._pingInterval) clearInterval(this._pingInterval);
            this._pingInterval = setInterval(() => { try { $wire.ping(); } catch(e) {} }, 4 * 60 * 1000);
        },

        // ============================
        // COMPUTED
        // ============================
        get currentQuestion() { return this.questions[this.currentIndex] ?? null; },

        get savedCount() {
            return Object.values(this.committedAnswers).filter(a => a.answerId !== null).length;
        },
        get doubtfulCount() {
            return Object.values(this.committedAnswers).filter(a => a.isDoubtful).length;
        },
        get progressPercent() {
            if (!this.questions.length) return 0;
            return Math.round((this.savedCount / this.questions.length) * 100);
        },

        // ============================
        // NAVIGASI — DISCARD PENDING
        // ============================
        navigateTo(index) {
            if (index < 0 || index >= this.questions.length || this.isFinishing || this.isSaving) return;
            this.isSaving = true;
            this.currentIndex = index;
            this.loadPendingFromCommitted(index);
            document.getElementById('question-scroll-viewport')?.scrollTo({ top: 0, behavior: 'smooth' });
            this.showSidebar = false;
            setTimeout(() => this.isSaving = false, 400);
        },

        loadPendingFromCommitted(index) {
            const q = this.questions[index];
            if (!q) return;
            const c = this.committedAnswers[q.id];
            this.pendingAnswerId   = c?.answerId ? parseInt(c.answerId) : null;
            this.pendingIsDoubtful = !!c?.isDoubtful;
        },

        // ============================
        // SIMPAN & LANJUTKAN
        // ============================
        saveAndNext() {
            if (!this.currentQuestion || this.isFinishing || this.isSaving) return;
            this.isSaving = true;

            const qId = this.currentQuestion.id;
            this.committedAnswers[qId] = {
                answerId:   this.pendingAnswerId ? parseInt(this.pendingAnswerId) : null,
                isDoubtful: this.pendingIsDoubtful,
            };
            this.committedAnswers = { ...this.committedAnswers };
            this.dirtyIds.add(qId);
            this.saveToLocalStorage();

            const next = this.currentIndex + 1;
            if (next < this.questions.length) {
                this.currentIndex = next;
                this.loadPendingFromCommitted(next);
                document.getElementById('question-scroll-viewport')?.scrollTo({ top: 0, behavior: 'smooth' });
            }

            setTimeout(() => this.isSaving = false, 500);
        },

        // [FIX #B] Tambah guard isSaving
        saveAndShowFinishAlert() {
            if (!this.currentQuestion || this.isFinishing || this.isSaving) return;
            this.isSaving = true;

            const qId = this.currentQuestion.id;
            this.committedAnswers[qId] = {
                answerId:   this.pendingAnswerId ? parseInt(this.pendingAnswerId) : null,
                isDoubtful: this.pendingIsDoubtful,
            };
            this.committedAnswers = { ...this.committedAnswers };
            this.dirtyIds.add(qId);
            this.saveToLocalStorage();

            // Reset isSaving setelah SweetAlert muncul (delay sedikit)
            setTimeout(() => {
                this.isSaving = false;
                window.dispatchEvent(new CustomEvent('show-finish-alert'));
            }, 200);
        },

        // ============================
        // DO FINISH — [FIX #5] retry 3x
        // ============================
        async doFinish() {
            if (this.isFinishing) return;
            this.isFinishing = true;

            // Tunggu flush yang sedang berjalan
            while (this.isFlushing) {
                await new Promise(r => setTimeout(r, 100));
            }
            
            await this.flushDirty();
            if (this.dirtyIds.size > 0) {
                // Berhenti jika offline (autoSave gagal)
                this.isFinishing = false;
                Swal.fire({
                    title: 'Gagal Menyimpan',
                    text: 'Koneksi terputus saat menyimpan sisa jawaban. Silakan cek internet dan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#2563EA',
                });
                return;
            }

            for (let attempt = 0; attempt < 3; attempt++) {
                try {
                    await $wire.finishExam();
                    try { localStorage.removeItem(`cbt_${this.userId}_${this.userTryoutId}`); } catch(e) {}
                    return;
                } catch(e) {
                    if (attempt < 2) await new Promise(r => setTimeout(r, 1500 * (attempt + 1)));
                }
            }

            this.isFinishing = false;
            Swal.fire({
                title: 'Gagal Mengumpulkan',
                text: 'Tidak dapat menghubungi server setelah 3 percobaan.',
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#2563EA',
                allowOutsideClick: false,
            }).then((r) => { if (r.isConfirmed) this.doFinish(); });
        },

        // ============================
        // SIDEBAR STYLING
        // ============================
        getNavClass(idx) {
            const q  = this.questions[idx];
            const c  = q ? (this.committedAnswers[q.id] ?? null) : null;
            let base = 'w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm hover:opacity-90 transition-all duration-150 ';

            if (c?.isDoubtful)                            base += 'bg-[#F9A825] ';
            else if (c?.answerId !== null && c?.answerId) base += 'bg-[#16a34a] ';
            else                                          base += 'bg-[#dc2626] ';

            if (this.currentIndex === idx) base += 'border-2 border-[#03A9F4] ring-2 ring-[#03A9F4]/50 ';
            return base;
        },

        // ============================
        // TIMER — [FIX #D] simpan interval ref
        // ============================
        initTimer() {
            const deadline = new Date(cbt.endedAt).getTime();
            const timerEl  = document.getElementById('timer');
            const serverNow = () => Date.now() + this.clockOffset;

            const tick = () => {
                const remaining = Math.floor((deadline - serverNow()) / 1000);
                if (remaining <= 0) {
                    timerEl.textContent = '00:00';
                    timerEl.classList.add('bg-red-600', 'animate-pulse');
                    timerEl.classList.remove('bg-[#2563EA]');
                    this.forceFinish();
                    return;
                }
                const m = Math.floor(remaining / 60).toString().padStart(2, '0');
                const s = (remaining % 60).toString().padStart(2, '0');
                timerEl.textContent = `${m}:${s}`;
                if (remaining <= 300) {
                    timerEl.classList.add('animate-pulse', 'bg-red-600');
                    timerEl.classList.remove('bg-[#2563EA]');
                }
            };

            tick();

            // [FIX #D] Clear interval lama sebelum buat baru
            if (this._timerInterval) clearInterval(this._timerInterval);
            this._timerInterval = setInterval(tick, 1000);

            // [FIX BUG #6] Tambahkan signal agar listener dihapus saat re-render
            const signal = this._abortController ? this._abortController.signal : undefined;
            document.addEventListener('visibilitychange', () => { if (!document.hidden) tick(); }, { signal });
        },

        async forceFinish() {
            if (this.isFinishing) return;
            this.isFinishing = true;

            // Retry flush dirty up to 3 times jika gagal
            for (let i = 0; i < 3; i++) {
                await this.flushDirty();
                if (this.dirtyIds.size === 0) break;
                await new Promise(r => setTimeout(r, 1000));
            }

            try {
                await $wire.finishExam();
                try { localStorage.removeItem(`cbt_${this.userId}_${this.userTryoutId}`); } catch(e) {}
            } catch(e) {
                Swal.fire({
                    title: 'Waktu Habis',
                    text: 'Waktu habis dan koneksi terputus. Jawaban offline telah di-backup di browser. Refresh halaman saat online untuk sinkronisasi.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2563EA',
                });
            }
        },

        // ============================
        // AUTO-SAVE — [FIX #C,D] isFlushing + simpan interval ref
        // ============================
        initAutoSave() {
            if (this._autoSaveInterval) clearInterval(this._autoSaveInterval);
            this._autoSaveInterval = setInterval(() => this.flushDirty(), 15_000);
        },

        // [FIX #C] Prevent concurrent flush dengan isFlushing flag
        async flushDirty() {
            if (this.dirtyIds.size === 0 || this.isFlushing) return;
            this.isFlushing = true;

            const payload = [...this.dirtyIds].map(qId => {
                const c = this.committedAnswers[qId] ?? {};
                return {
                    questionId: qId,
                    answerId:   c.answerId ? parseInt(c.answerId) : null,
                    isDoubtful: !!c.isDoubtful,
                };
            });

            this.dirtyIds.clear();

            try {
                await $wire.autoSave(payload);
            } catch(e) {
                payload.forEach(p => this.dirtyIds.add(p.questionId));
                this.saveToLocalStorage();
                console.warn('[CBT] Auto-save gagal, retry berikutnya.', e);
            } finally {
                this.isFlushing = false;
            }
        },

        // ============================
        // LOCAL STORAGE
        // ============================
        saveToLocalStorage() {
            try {
                localStorage.setItem(`cbt_${this.userId}_${this.userTryoutId}`, JSON.stringify({
                    ts:               Date.now(),
                    committedAnswers: this.committedAnswers,
                    dirtyIds:        [...this.dirtyIds],
                }));
            } catch(e) {}
        },

        restoreFromLocalStorage() {
            try {
                const raw = localStorage.getItem(`cbt_${this.userId}_${this.userTryoutId}`);
                if (!raw) return;
                const { ts, committedAnswers, dirtyIds: savedDirty } = JSON.parse(raw);

                if (Date.now() - ts > 30 * 60 * 1000) {
                    localStorage.removeItem(`cbt_${this.userId}_${this.userTryoutId}`);
                    return;
                }

                let merged = false;
                Object.entries(committedAnswers || {}).forEach(([qId, state]) => {
                    const id = parseInt(qId);
                    // [FIX #H] Merge semua entry termasuk answerId=null (misal doubtful saja)
                    if (!this.committedAnswers[id]) {
                        this.committedAnswers[id] = {
                            answerId:   state.answerId ? parseInt(state.answerId) : null,
                            isDoubtful: !!state.isDoubtful,
                        };
                        merged = true;
                    }
                });

                if (Array.isArray(savedDirty)) {
                    savedDirty.forEach(id => this.dirtyIds.add(parseInt(id)));
                }

                if (merged) {
                    this.committedAnswers = { ...this.committedAnswers };
                    this.loadPendingFromCommitted(this.currentIndex);
                }
            } catch(e) {}
        },
    };
}
</script>
@endpush
