<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Reviews (Owner)</h2>
        </div>

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

        <div class="mb-4 flex border-b border-gray-200">
            <button wire:click="$set('showTrashed', false)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button wire:click="$set('showTrashed', true)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-2/5">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ulasan, nama user, atau tryout..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
            </div>
            <div class="w-full md:w-1/4">
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="all">Semua Status</option>
                    <option value="published">Published</option>
                    <option value="hidden">Hidden</option>
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <select wire:model.live="perPage" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tryout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ulasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Publikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $review->trashed() ? 'bg-red-50' : '' }}" wire:key="review-{{ $review->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-sm {{ $review->trashed() ? 'text-gray-400' : 'text-gray-900' }}">{{ $review->user->name ?? 'User Dihapus' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Illuminate\Support\Str::limit($review->tryout->title ?? 'Tryout Dihapus', 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-semibold">
                                {{ $review->rating }} <i class="fa-solid fa-star text-xs"></i>
                            </td>
                            <td class="px-6 py-4 text-sm {{ $review->trashed() ? 'text-gray-400' : 'text-gray-700' }}" style="min-width: 250px; max-width: 400px; white-space: normal;">
                                {{ $review->review_text ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleStatus({{ $review->id }})" wire:loading.attr="disabled" wire:target="toggleStatus({{ $review->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full transition-colors duration-200 {{ $review->is_published ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
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
                                <div class="flex space-x-2">
                                    @if(!$review->trashed())
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Soft Delete Review?',
                                                text: 'Review akan diarsipkan dan bisa dipulihkan.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#f59e0b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Soft Delete!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) { $wire.softDeleteReview({{ $review->id }}) }
                                            })
                                        " class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-box-archive w-4 h-4 mr-1"></i> Soft Delete
                                        </button>
                                    @else
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Pulihkan Review?',
                                                text: 'Review ini akan diaktifkan kembali.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Pulihkan!'
                                            }).then((result) => {
                                                if (result.isConfirmed) { $wire.restoreReview({{ $review->id }}) }
                                            })
                                        " class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Restore
                                        </button>

                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'HAPUS PERMANEN?',
                                                text: 'Tindakan ini tidak bisa dibatalkan!',
                                                icon: 'error',
                                                showCancelButton: true,
                                                confirmButtonColor: '#991b1b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus Permanen!'
                                            }).then((result) => {
                                                if (result.isConfirmed) { $wire.forceDeleteReview({{ $review->id }}) }
                                            })
                                        " class="text-red-700 hover:text-red-900 font-bold bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus Permanen
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-comment-slash w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada review yang terhapus.' : 'Tidak ada review ditemukan.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    </div>
</div>