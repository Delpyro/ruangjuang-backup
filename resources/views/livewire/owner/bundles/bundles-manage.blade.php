<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Manajemen Bundles (Owner)</h2>
        <a href="{{ route('owner.bundles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 shadow-md flex items-center">
            <i class="fa-solid fa-plus w-5 h-5 mr-2"></i> Tambah Bundle Baru
        </a>
    </div>

    {{-- FLASH MESSAGE DENGAN ANIMASI ALPINE.JS (AUTO DISMISS 3 DETIK) --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 transition-colors duration-200">
                <i class="fa-solid fa-xmark w-5 h-5"></i>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                <i class="fa-solid fa-xmark w-5 h-5"></i>
            </button>
        </div>
    @endif

    {{-- Tabs untuk filter status --}}
    <div class="mb-4 flex border-b border-gray-200">
        <button wire:click="$set('showTrashed', false)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Aktif
        </button>
        <button wire:click="$set('showTrashed', true)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
        </button>
    </div>

    {{-- Filter dan Pencarian --}}
    <div class="mb-4 bg-white p-4 rounded-lg shadow-sm flex space-x-4">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Cari berdasarkan Judul atau Slug..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
        >

        <select wire:model.live="perPage" class="border border-gray-300 rounded-lg px-4 py-2">
            <option value="10">10 per halaman</option>
            <option value="25">25 per halaman</option>
            <option value="50">50 per halaman</option>
        </select>

        <select wire:model.live="status" class="border border-gray-300 rounded-lg px-4 py-2">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>

    {{-- Tabel Bundles --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Asli (Rp)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual (Rp)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tryout</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($bundles as $bundle)
                    {{-- WIRE KEY UNTUK REALTIME DOM DIFFING --}}
                    <tr wire:key="bundle-{{ $bundle->id }}" class="{{ $bundle->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $bundle->title }}
                            
                            @if($bundle->trashed())
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800 border border-red-300">
                                    <i class="fa-solid fa-trash-can mr-1 text-[10px] mt-[3px]"></i> Terhapus
                                </span>
                            @endif

                            <div class="text-xs text-gray-500 mt-1">Slug: {{ $bundle->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($bundle->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold @if($bundle->discount > 0) text-green-600 @else text-gray-900 @endif">
                            {{ number_format($bundle->final_price, 0, ',', '.') }}
                            @if($bundle->discount > 0)
                                <span class="text-xs text-red-500 ml-1">(-{{ $bundle->discount_percentage }}%)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $bundle->tryouts_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($bundle->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                @if ($bundle->trashed())
                                    {{-- TOMBOL RESTORE (EMERALD) --}}
                                    <button
                                        type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: 'Pulihkan Bundle?',
                                                text: 'Bundle ini akan diaktifkan kembali.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Pulihkan!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.restoreBundle({{ $bundle->id }})
                                                }
                                            })
                                        " 
                                        class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm"
                                    >
                                        <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Restore
                                    </button>

                                    {{-- TOMBOL HAPUS PERMANEN (MERAH DENGAN IKON TONG SAMPAH) --}}
                                    <button
                                        type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: 'HAPUS PERMANEN?',
                                                text: 'Data yang dihapus permanen TIDAK BISA dikembalikan sama sekali!',
                                                icon: 'error',
                                                showCancelButton: true,
                                                confirmButtonColor: '#991b1b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'SAYA YAKIN, HAPUS PERMANEN!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.forceDeleteBundle({{ $bundle->id }})
                                                }
                                            })
                                        "
                                        class="text-red-700 hover:text-red-900 font-bold bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm"
                                    >
                                        <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus Permanen
                                    </button>
                                @else
                                    {{-- TOMBOL EDIT (INDIGO) --}}
                                    <a href="{{ route('owner.bundles.edit', $bundle) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                        <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                    </a>

                                    {{-- TOMBOL SOFT DELETE (AMBER) --}}
                                    <button
                                        type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: 'Soft Delete Bundle?',
                                                text: 'Data akan disembunyikan dan masih bisa dipulihkan nanti.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#f59e0b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Soft Delete!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteBundle({{ $bundle->id }})
                                                }
                                            })
                                        "
                                        class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm"
                                    >
                                        <i class="fa-solid fa-box-archive w-4 h-4 mr-1"></i> Soft Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data bundle yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $bundles->links() }}
    </div>
</div>