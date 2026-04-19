<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Bundles</h2>

            <div class="flex items-center gap-3">
                {{-- ✨ DYNAMIC ROUTE UNTUK TAMBAH BUNDLE ✨ --}}
                <a href="{{ route($this->rolePrefix . '.bundles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                    <i class="fa-solid fa-plus w-4 h-4 mr-1"></i> Tambah Bundle
                </a>
            </div>
        </div>

        {{-- Tabs untuk filter status --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button wire:click="$set('showTrashed', false)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button wire:click="$set('showTrashed', true)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        {{-- Search dan Filter --}}
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan Judul atau Slug..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
            <select wire:model.live="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
            <select wire:model.live="perPage" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                <option value="10">10 per halaman</option>
                <option value="25">25 per halaman</option>
                <option value="50">50 per halaman</option>
            </select>
        </div>

        {{-- Flash message Success --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Flash message Error --}}
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Error Message dari Livewire (Optional) --}}
        @if(isset($errorMessage) && $errorMessage)
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms class="mb-4 p-4 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-triangle-exclamation w-5 h-5 mr-3 text-yellow-600"></i>
                    <span>{{ $errorMessage }}</span>
                </div>
                <button type="button" @click="show = false; $wire.set('errorMessage', '')" class="text-yellow-600 hover:text-yellow-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Table Bundles --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bundle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tryout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($bundles as $index => $bundle)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $bundle->trashed() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ($bundles->currentPage() - 1) * $bundles->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <span class="font-medium text-sm block {{ $bundle->trashed() ? 'text-gray-400' : 'text-gray-900' }}">{{ $bundle->title }}</span>
                                    <span class="text-xs text-gray-500">{{ $bundle->slug }}</span>
                                    @if($bundle->trashed())
                                        <span class="text-xs text-red-600 ml-2">Terhapus</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium {{ $bundle->trashed() ? 'text-gray-400' : 'text-gray-900' }}">Rp {{ number_format($bundle->final_price, 0, ',', '.') }}</div>
                                @if($bundle->discount > 0)
                                    <div class="text-xs text-gray-400 line-through">Rp {{ number_format($bundle->price, 0, ',', '.') }}</div>
                                    <div class="text-xs text-red-500">Diskon {{ $bundle->discount_percentage }}%</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $bundle->tryouts_count }} Paket</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bundle->trashed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terhapus</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bundle->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $bundle->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$bundle->trashed())
                                        {{-- ✨ DYNAMIC ROUTE UNTUK EDIT BUNDLE ✨ --}}
                                        <a href="{{ route($this->rolePrefix . '.bundles.edit', $bundle) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                        </a>

                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Soft Delete Bundle?',
                                                text: 'Data akan disembunyikan ke tab Terhapus.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#f59e0b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Soft Delete!',
                                                cancelButtonText: 'Batal'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) { 
                                                    const res = await $wire.softDeleteBundle({{ $bundle->id }});
                                                    if (res && res.status === 'success') {
                                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                    } else if (res) {
                                                        Swal.fire('Oops...', res.message, 'error');
                                                    }
                                                }
                                            })
                                        " class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-box-archive w-4 h-4 mr-1"></i> Soft Delete
                                        </button>
                                    @else
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Restore Bundle?',
                                                text: 'Bundle ini akan kembali aktif.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Restore!',
                                                cancelButtonText: 'Batal'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) { 
                                                    const res = await $wire.restoreBundle({{ $bundle->id }});
                                                    if (res && res.status === 'success') {
                                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                    } else if (res) {
                                                        Swal.fire('Oops...', res.message, 'error');
                                                    }
                                                }
                                            })
                                        " class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Restore
                                        </button>

                                        @if(auth()->check() && auth()->user()->role === 'owner')
                                            <button type="button" x-data x-on:click="
                                                Swal.fire({
                                                    title: 'Hapus Permanen?',
                                                    text: 'PERINGATAN: Data yang dihapus tidak dapat dikembalikan!',
                                                    icon: 'error',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#ef4444',
                                                    cancelButtonColor: '#6b7280',
                                                    confirmButtonText: 'Ya, Hapus Permanen!',
                                                    cancelButtonText: 'Batal'
                                                }).then(async (result) => {
                                                    if (result.isConfirmed) { 
                                                        const res = await $wire.forceDeleteBundle({{ $bundle->id }});
                                                        if (res && res.status === 'success') {
                                                            Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                        } else if (res) {
                                                            Swal.fire('Oops...', res.message, 'error');
                                                        }
                                                    }
                                                })
                                            " class="text-red-700 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                                <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus Permanen
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-box-open w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada data bundle yang terhapus.' : 'Tidak ada data bundle.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($bundles->hasPages())
            <div class="mt-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                    <div class="flex-1 flex justify-end">
                        {{ $bundles->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>