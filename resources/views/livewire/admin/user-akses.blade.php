<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        
        {{-- Header & Breadcrumbs --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-3">
                {{-- ✨ DYNAMIC ROUTE: Dashboard ✨ --}}
                <a href="{{ route($this->rolePrefix . '.dashboard') ?? '#' }}" class="hover:text-indigo-600 transition-colors duration-200">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                
                {{-- ✨ DYNAMIC ROUTE: Users ✨ --}}
                <a href="{{ route($this->rolePrefix . '.users') }}" class="hover:text-indigo-600 transition-colors duration-200">Pengguna</a>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                <span class="text-slate-800 font-medium">Akses Tryout</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    {{-- ✨ DYNAMIC ROUTE: Tombol Kembali ✨ --}}
                    <a href="{{ route($this->rolePrefix . '.users') }}" 
                       class="group w-10 h-10 rounded-xl bg-white/90 backdrop-blur-sm border border-slate-200/80 flex items-center justify-center shadow-sm hover:shadow-md hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-300">
                        <i class="fa-solid fa-arrow-left text-slate-500 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            @if($user->image)
                                <img src="{{ Storage::url($user->image) }}" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm bg-white">
                            @else
                                <div class="w-12 h-12 rounded-full border-2 border-white shadow-sm bg-gradient-to-br from-indigo-50 to-slate-100 flex items-center justify-center">
                                    <i class="fa-solid fa-user text-xl text-slate-400"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-slate-800 via-indigo-800 to-slate-800 bg-clip-text text-transparent">
                                Akses Tryout
                            </h1>
                            <p class="text-sm text-slate-500 mt-1">
                                <span class="font-semibold text-slate-700">{{ $user->name }}</span> &bull; {{ $user->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 shadow-sm flex items-center justify-between animate-in fade-in slide-in-from-top-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100 p-1.5 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-200 shadow-sm flex items-center justify-between animate-in fade-in slide-in-from-top-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-rose-500 hover:text-rose-700 hover:bg-rose-100 p-1.5 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        {{-- Main Table Section --}}
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/80 overflow-hidden hover:shadow-xl transition-all duration-300">
            
            <div class="p-6 border-b border-slate-200/60 bg-slate-50/30">
                <h3 class="text-lg font-bold text-slate-800">
                    <i class="fa-solid fa-key mr-2 text-indigo-500"></i> Daftar Percobaan (Attempt)
                </h3>
                <p class="text-sm text-slate-500 mt-1">Riwayat akses dan status pengerjaan tryout pengguna.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Tryout</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Attempt</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Mulai</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($aksesTryouts as $akses)
                            <tr class="hover:bg-slate-50 transition-all duration-200 group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center border border-indigo-100 group-hover:bg-indigo-100 transition-colors">
                                            <i class="fa-solid fa-file-alt text-indigo-500"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $akses->tryout->title ?? 'Tryout Dihapus' }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">Order ID: #{{ $akses->order_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-bold text-sm border border-slate-200 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-hover:border-indigo-200 transition-colors">
                                        {{ $akses->attempt }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($akses->started_at)
                                        <p class="text-sm font-medium text-slate-700">
                                            <i class="fa-regular fa-clock text-slate-400 mr-1.5"></i>
                                            {{ \Carbon\Carbon::parse($akses->started_at)->format('d M Y, H:i') }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-400 italic px-2.5 py-1 bg-slate-100 rounded-md">Belum dimulai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($akses->is_completed)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                            <i class="fa-solid fa-circle-check"></i> Selesai
                                        </span>
                                    @elseif($akses->started_at)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                                            <i class="fa-solid fa-spinner fa-spin"></i> Dikerjakan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 shadow-sm">
                                            <i class="fa-solid fa-minus"></i> Belum Mulai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($akses->started_at || $akses->is_completed)
                                        <button wire:click="confirmReset({{ $akses->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg text-rose-600 bg-rose-50 border border-rose-200 hover:bg-rose-500 hover:text-white transition-all duration-200 shadow-sm focus:ring-2 focus:ring-rose-200"
                                            title="Reset Akses Ini">
                                            <i class="fa-solid fa-rotate-left"></i> Reset
                                        </button>
                                    @else
                                        <span class="text-slate-300 text-xs italic"><i class="fa-solid fa-ban"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center bg-slate-50/50">
                                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 mb-4 border border-slate-200">
                                        <i class="fa-solid fa-folder-open text-3xl text-slate-300"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold text-slate-700 mb-1">Belum Ada Akses</h4>
                                    <p class="text-sm text-slate-400">Pengguna ini belum memiliki akses tryout apapun.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($aksesTryouts->hasPages())
                <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-50">
                    {{ $aksesTryouts->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Reset --}}
    @if($confirmingReset)
        <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-md p-4 mx-auto animate-in fade-in zoom-in duration-200">
                
                <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden">
                    
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 rounded-full bg-rose-50 border-4 border-rose-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-triangle-exclamation text-2xl text-rose-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Konfirmasi Reset Akses</h3>
                        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                            Apakah kamu yakin ingin me-reset akses ini? <br>
                            <span class="text-rose-600 font-semibold bg-rose-50 px-2 py-0.5 rounded text-xs">Semua riwayat jawaban dan skor akan dihapus permanen!</span>
                        </p>

                        <div class="flex items-center justify-center gap-3">
                            <button wire:click="cancelReset" 
                                class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-colors focus:ring-2 focus:ring-slate-200 focus:outline-none w-full">
                                Batal
                            </button>
                            <button wire:click="resetAkses" 
                                class="px-5 py-2.5 text-sm font-bold text-white bg-rose-500 border border-transparent rounded-xl hover:bg-rose-600 transition-colors shadow-sm shadow-rose-200 focus:ring-2 focus:ring-rose-500 w-full flex items-center justify-center gap-2">
                                <i class="fa-solid fa-rotate-left"></i> Ya, Reset
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>