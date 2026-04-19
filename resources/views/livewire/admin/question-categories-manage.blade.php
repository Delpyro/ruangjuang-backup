<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Kategori Pertanyaan</h2>

            <button wire:click="openModal(false)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                <i class="fa-solid fa-plus w-4 h-4 mr-1"></i> Tambah Kategori
            </button>
        </div>

        {{-- FLASH MESSAGE HTML LAMA SUDAH DIHAPUS DIGANTI SWEETALERT JS --}}

        {{-- Tabs untuk filter status --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button wire:click="$set('showTrashed', false)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button wire:click="$set('showTrashed', true)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        {{-- Search & Filter --}}
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-1/2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kategori..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
            </div>
            
            <div class="w-full md:w-1/4">
                <select wire:model.live="perPage" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                </select>
            </div>
        </div>

        {{-- Table Categories --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passing Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Sub Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($categories as $index => $category)
                        <tr wire:key="category-{{ $category->id }}" class="hover:bg-gray-50 transition-colors duration-150 {{ $category->trashed() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="font-medium text-sm {{ $category->trashed() ? 'text-gray-400' : 'text-gray-900' }}">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $category->passing_grade }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $category->subCategories->count() }} Sub Kategori
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->trashed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Terhapus
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$category->trashed())
                                        {{-- JIKA NORMAL: Tampilkan Edit dan Soft Delete --}}
                                        <button wire:click="openModal(true, {{ $category->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                        </button>

                                        @if($category->subCategories->count() > 0)
                                            {{-- Disable tombol hapus jika ada subkategori --}}
                                            <button disabled class="text-gray-400 bg-gray-100 px-3 py-2 rounded-md flex items-center shadow-sm cursor-not-allowed" title="Tidak dapat dihapus karena memiliki subkategori">
                                                <i class="fa-solid fa-box-archive w-4 h-4 mr-1"></i> Soft Delete
                                            </button>
                                        @else
                                            {{-- SweetAlert Soft Delete --}}
                                            <button type="button" x-data x-on:click="
                                                Swal.fire({
                                                    title: 'Soft Delete Kategori?',
                                                    text: 'Kategori akan disembunyikan dan dapat dipulihkan.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#f59e0b',
                                                    cancelButtonColor: '#6b7280',
                                                    confirmButtonText: 'Ya, Soft Delete!',
                                                    cancelButtonText: 'Batal'
                                                }).then(async (result) => {
                                                    if (result.isConfirmed) {
                                                        const res = await $wire.softDeleteCategory({{ $category->id }});
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
                                        @endif
                                    @else
                                        {{-- JIKA TERHAPUS: HANYA Tampilkan Restore --}}
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Pulihkan Kategori?',
                                                text: 'Kategori ini akan diaktifkan kembali.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Pulihkan!',
                                                cancelButtonText: 'Batal'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) {
                                                    const res = await $wire.restoreCategory({{ $category->id }});
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
                                            @if($category->subCategories->count() > 0)
                                                <button disabled class="text-gray-400 bg-gray-100 px-3 py-2 rounded-md flex items-center shadow-sm cursor-not-allowed" title="Tidak dapat dihapus permanen karena memiliki subkategori">
                                                    <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus Permanen
                                                </button>
                                            @else
                                                <button type="button" x-data x-on:click="
                                                    Swal.fire({
                                                        title: 'HAPUS PERMANEN?',
                                                        text: 'Tindakan ini tidak bisa dibatalkan!',
                                                        icon: 'error',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#ef4444',
                                                        cancelButtonColor: '#6b7280',
                                                        confirmButtonText: 'Ya, Hapus Permanen!',
                                                        cancelButtonText: 'Batal'
                                                    }).then(async (result) => {
                                                        if (result.isConfirmed) {
                                                            const res = await $wire.forceDeleteCategory({{ $category->id }});
                                                            if (res && res.status === 'success') {
                                                                Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                            } else if (res) {
                                                                Swal.fire('Oops...', res.message, 'error');
                                                            }
                                                        }
                                                    })
                                                " class="text-red-700 hover:text-red-900 font-bold bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                                    <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus Permanen
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-list w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada data kategori yang terhapus.' : 'Tidak ada data kategori.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION STYLING --}}
        @if ($categories->hasPages())
            <div class="mt-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                    <div class="flex-1 flex justify-end">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL BAWAAN (CREATE/EDIT) --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-20 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4 w-xl m-auto">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto max-h-[90vh] overflow-y-auto transform transition-all">
                    
                    <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200 bg-gray-100 hover:bg-gray-200 rounded-full p-1">
                        <i class="fa-solid fa-xmark w-5 h-5"></i>
                    </button>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-plus' }} w-5 h-5 mr-2"></i>
                            {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">Isi form berikut untuk {{ $isEdit ? 'mengedit' : 'menambah' }} kategori</p>

                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                    <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Passing Grade</label>
                                    <input type="number" step="1" min="0" max="200" wire:model="passing_grade" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors">                                    @error('passing_grade') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <input wire:model="is_active" id="is_active" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900 font-medium">Aktif</label>
                                    </div>
                                    @error('is_active') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                                    <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                                </button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                                    <i class="fa-solid {{ $isEdit ? 'fa-check' : 'fa-floppy-disk' }} w-4 h-4 mr-1"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- SCRIPT LISTENER UNTUK TOAST SWEETALERT --}}
@push('scripts')
<script>
    window.addEventListener('swal-toast', event => {
        const data = event.detail[0] || event.detail; // Handle kompatibilitas argumen Livewire v3
        Swal.fire({
            icon: data.icon || 'success',
            title: data.title || 'Berhasil!',
            text: data.text,
            showConfirmButton: false,
            timer: 2000,
            toast: true,
            position: 'top-end'
        });
    });
</script>
@endpush