<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Reviews</h2>
        </div>

        {{-- Flash message Success/Error (Alpine.js) --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-green-600 hover:text-green-800">
                    <i class="fa-solid fa-xmark w-5 h-5"></i>
                </button>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button wire:click="$set('showTrashed', false)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-comment w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button wire:click="$set('showTrashed', true)" class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        {{-- Search & Filter --}}
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-2/5">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ulasan, user, atau tryout..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" />
            </div>
            
            <div class="w-full md:w-1/4">
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <option value="all">Semua Status</option>
                    <option value="published">Published</option>
                    <option value="hidden">Hidden</option>
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <select wire:model.live="perPage" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                </select>
            </div>
        </div>

        {{-- Table Reviews --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tryout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ulasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visibilitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $review->trashed() ? 'bg-red-50' : '' }}" wire:key="review-{{ $review->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $review->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Illuminate\Support\Str::limit($review->tryout->title ?? 'Tryout Dihapus', 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-semibold">
                                {{ $review->rating }} <i class="fa-solid fa-star text-xs"></i>
                            </td>
                            <td class="px-6 py-4 text-sm {{ $review->trashed() ? 'text-gray-400' : 'text-gray-700' }}" style="min-width: 250px; white-space: normal;">
                                {{ $review->review_text ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    wire:click="toggleStatus({{ $review->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full transition-colors duration-200
                                    {{ $review->is_published ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                    <i class="fa-solid {{ $review->is_published ? 'fa-eye' : 'fa-eye-slash' }} w-4 h-4 mr-1.5"></i>
                                    {{ $review->is_published ? 'Published' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$review->trashed())
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Soft Delete Review?',
                                                text: 'Review akan diarsipkan ke tab Terhapus.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#f59e0b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Soft Delete!'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) {
                                                    const res = await $wire.softDeleteReview({{ $review->id }});
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
                                                title: 'Pulihkan Review?',
                                                text: 'Review ini akan aktif kembali.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Restore!'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) {
                                                    const res = await $wire.restoreReview({{ $review->id }});
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
                                                        const res = await $wire.forceDeleteReview({{ $review->id }});
                                                        if (res.status === 'success') {
                                                            Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
                                                        } else {
                                                            Swal.fire('Oops...', res.message, 'error');
                                                        }
                                                    }
                                                })
                                            " class="text-red-700 hover:text-red-900 font-bold bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                                <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Permanen
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
                                    <i class="fa-solid fa-comment-slash w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada review yang terhapus.' : 'Tidak ada ulasan ditemukan.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div class="mt-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                    <div class="flex-1 flex justify-end">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
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