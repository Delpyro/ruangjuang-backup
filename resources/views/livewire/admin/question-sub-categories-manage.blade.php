<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Sub Kategori</h2>

            <button wire:click="openModal(false)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                <i class="fa-solid fa-plus w-4 h-4 mr-1"></i> Tambah Sub Kategori
            </button>
        </div>

        {{-- Tabs --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button wire:click="$set('showTrashed', false)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-layer-group w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button wire:click="$set('showTrashed', true)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        {{-- Search & Filter --}}
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-1/2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama sub kategori atau kategori induk..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
            </div>
            
            <div class="w-full md:w-1/4">
                <select wire:model.live="perPage" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Sub Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Induk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($subCategories as $index => $subCategory)
                        <tr wire:key="subCat-{{ $subCategory->id }}" class="hover:bg-gray-50 transition-colors duration-150 {{ $subCategory->trashed() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ ($subCategories->currentPage() - 1) * $subCategories->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-sm {{ $subCategory->trashed() ? 'text-gray-400' : 'text-gray-900' }}">{{ $subCategory->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $subCategory->category->name ?? 'Induk Dihapus' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subCategory->trashed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terhapus</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $subCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $subCategory->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$subCategory->trashed())
                                        <button wire:click="openModal(true, {{ $subCategory->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                        </button>

                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Soft Delete Sub Kategori?',
                                                text: 'Data akan diarsipkan ke tab terhapus.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#f59e0b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Soft Delete!'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) {
                                                    const res = await $wire.softDeleteSubCategory({{ $subCategory->id }});
                                                    if (res.status === 'success') {
                                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                    } else {
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
                                                title: 'Pulihkan Sub Kategori?',
                                                text: 'Data ini akan kembali aktif.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Pulihkan!'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) {
                                                    const res = await $wire.restoreSubCategory({{ $subCategory->id }});
                                                    if (res.status === 'success') {
                                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                    } else {
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
                                                    title: 'HAPUS PERMANEN?',
                                                    text: 'Tindakan ini tidak bisa dibatalkan!',
                                                    icon: 'error',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#ef4444',
                                                    cancelButtonColor: '#6b7280',
                                                    confirmButtonText: 'Ya, Hapus!'
                                                }).then(async (result) => {
                                                    if (result.isConfirmed) {
                                                        const res = await $wire.forceDeleteSubCategory({{ $subCategory->id }});
                                                        if (res.status === 'success') {
                                                            Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                        } else {
                                                            Swal.fire('Oops...', res.message, 'error');
                                                        }
                                                    }
                                                })
                                            " class="text-red-700 hover:text-red-900 font-bold bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                                <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus Permanen
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-layer-group w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada data terhapus.' : 'Tidak ada data sub kategori.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($subCategories->hasPages())
            <div class="mt-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                    <div class="flex-1 flex justify-end">
                        {{ $subCategories->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-20 transition-opacity backdrop-blur-sm"></div>
            <div class="flex items-center justify-center min-h-screen p-4 w-xl m-auto">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto max-h-[90vh] overflow-y-auto transform transition-all p-6">
                    
                    <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 bg-gray-100 rounded-full p-1">
                        <i class="fa-solid fa-xmark w-5 h-5"></i>
                    </button>

                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-plus' }} w-5 h-5 mr-2"></i>
                        {{ $isEdit ? 'Edit Sub Kategori' : 'Tambah Sub Kategori' }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Lengkapi informasi sub kategori di bawah ini.</p>

                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sub Kategori</label>
                                <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                @error('name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Induk</label>
                                <select wire:model="question_category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('question_category_id') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center">
                                <input wire:model="is_active" id="is_active" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 font-medium">Aktifkan Sub Kategori</label>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center">
                                <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm flex items-center">
                                <i class="fa-solid {{ $isEdit ? 'fa-check' : 'fa-floppy-disk' }} w-4 h-4 mr-1"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Toast Script --}}
@push('scripts')
<script>
    window.addEventListener('swal-toast', event => {
        const data = event.detail[0] || event.detail;
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