<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Reviews</h2>
            {{-- Tombol 'Tambah User' tidak ada, sesuai referensi UsersManage --}}
        </div>

        {{-- Tabs untuk filter status --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button
                wire:click="$set('filterStatus', 'all')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Semua
            </button>
            <button
                wire:click="$set('filterStatus', 'published')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'published' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-eye w-4 h-4 mr-1 inline"></i> Published
            </button>
            <button
                wire:click="$set('filterStatus', 'hidden')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'hidden' ? 'border-gray-500 text-gray-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-eye-slash w-4 h-4 mr-1 inline"></i> Hidden
            </button>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari review, user, atau tryout..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            />
        </div>

        {{-- Flash message --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center justify-between shadow-sm"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif
        
        {{-- Table Reviews --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tryout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ulasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" wire:key="review-{{ $review->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-sm text-gray-900">{{ $review->user->name ?? 'User Dihapus' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Illuminate\Support\Str::limit($review->tryout->title ?? 'Tryout Dihapus', 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-semibold">
                                {{ $review->rating }} <i class="fa-solid fa-star text-xs"></i>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700" style="min-width: 250px; max-width: 400px; white-space: normal;">
                                {{ $review->review_text ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                
                                {{-- Tombol Toggle ada di sini, BUKAN di kolom Aksi --}}
                                <button
                                    wire:click="toggleStatus({{ $review->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleStatus({{ $review->id }})"
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full transition-colors duration-200
                                    {{ $review->is_published ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                    
                                    {{-- Tampilkan spinner saat loading --}}
                                    <span wire:loading.remove wire:target="toggleStatus({{ $review->id }})">
                                        <i class="fa-solid {{ $review->is_published ? 'fa-eye' : 'fa-eye-slash' }} w-4 h-4 mr-1.5"></i>
                                    </span>
                                    <span wire:loading wire:target="toggleStatus({{ $review->id }})">
                                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    
                                    {{ $review->is_published ? 'Published' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- Tombol Edit dihapus, hanya ada tombol Hapus --}}
                                <button
                                    wire:click="confirmDelete({{ $review->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmDelete({{ $review->id }})"
                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                    <i class="fa-solid fa-trash-can w-4 h-4"></i>
                                    <span class="sr-only">Hapus</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-comment-slash w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>Tidak ada review ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    </div>

    {{-- Modal Edit SUDAH DIHAPUS --}}

    {{-- Konfirmasi Delete Overlay (Tetap ada, sesuai referensi UsersManage) --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="delete-confirmation-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <i class="fa-solid fa-triangle-exclamation w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-2" id="delete-confirmation-title">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Apakah kamu yakin ingin menghapus review ini? Tindakan ini akan menghapus data secara permanen dan **tidak dapat** dikembalikan.
                    </p>

                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                        </button>
                        <button wire:click="delete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>