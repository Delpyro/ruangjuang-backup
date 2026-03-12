<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($user->image)
                    <img src="{{ Storage::url($user->image) }}" class="w-16 h-16 rounded-full object-cover border-2 border-indigo-100">
                @else
                    <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-indigo-200">
                        <i class="fa-solid fa-user text-2xl text-indigo-500"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Akses Tryout: {{ $user->name }}</h2>
                    <p class="text-gray-500">{{ $user->email }} | {{ $user->phone_number ?? 'No HP tidak tersedia' }}</p>
                </div>
            </div>
            
            <a href="{{ route('admin.users') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                <i class="fa-solid fa-arrow-left w-4 h-4 mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Daftar Akses / Percobaan (Attempt)</h3>

        {{-- Flash message --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Table Akses --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tryout</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Attempt Ke-</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($aksesTryouts as $akses)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-sm text-gray-900">{{ $akses->tryout->title ?? 'Tryout Dihapus' }}</div>
                                <div class="text-xs text-gray-500">Order ID: {{ $akses->order_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-indigo-600">
                                {{ $akses->attempt }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($akses->started_at)
                                    {{ \Carbon\Carbon::parse($akses->started_at)->format('d M Y H:i') }}
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($akses->is_completed)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fa-solid fa-check mr-1 mt-0.5"></i> Selesai
                                    </span>
                                @elseif($akses->started_at)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fa-solid fa-spinner fa-spin mr-1 mt-0.5"></i> Sedang Dikerjakan
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Belum Mulai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($akses->started_at || $akses->is_completed)
                                    <button 
                                        wire:click="confirmReset({{ $akses->id }})"
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-md transition-colors duration-200 inline-flex items-center shadow-sm"
                                        title="Reset Akses Ini">
                                        <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Reset Akses
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum bisa di-reset</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-file-circle-xmark w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>User ini belum memiliki akses tryout.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($aksesTryouts->hasPages())
            <div class="mt-6">
                {{ $aksesTryouts->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Reset --}}
    @if($confirmingReset)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="reset-confirmation-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <i class="fa-solid fa-triangle-exclamation w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-2" id="reset-confirmation-title">Konfirmasi Reset Akses</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Apakah kamu yakin ingin me-reset akses ini? <br>
                        <strong class="text-red-600">PERINGATAN:</strong> Semua riwayat jawaban dan skor pada attempt ini akan dihapus secara permanen, dan user harus mengulang dari awal.
                    </p>

                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelReset" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                        </button>
                        <button wire:click="resetAkses" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Ya, Reset Akses
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>