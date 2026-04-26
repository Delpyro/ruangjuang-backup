<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Users</h2>

            <div class="flex items-center gap-3">
                {{-- Tombol Tambah User --}}
                <button
                    wire:click="openModal(false)"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                    <i class="fa-solid fa-plus w-4 h-4 mr-1"></i> Tambah User
                </button>
            </div>
        </div>

        {{-- Error Message dari Livewire (Optional) --}}
        @if($errorMessage)
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)" 
                 x-transition.duration.500ms
                 class="mb-4 p-4 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-triangle-exclamation w-5 h-5 mr-3 text-yellow-600"></i>
                    <span>{{ $errorMessage }}</span>
                </div>
                <button type="button" @click="show = false; $wire.set('errorMessage', '')" class="text-yellow-600 hover:text-yellow-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Tabs untuk filter status --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button
                wire:click="$set('showTrashed', false)"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-users w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button
                wire:click="$set('showTrashed', true)"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama, email, atau nomor telepon..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            />
        </div>

        {{-- Table Users --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $user->trashed() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->image)
                                        <img src="{{ Storage::url($user->image) }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-user w-4 h-4 text-gray-500"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-medium text-sm block {{ $user->trashed() ? 'text-gray-400' : 'text-gray-900' }}">{{ $user->name }}</span>
                                        @if($user->trashed())
                                            <span class="text-xs text-red-600">Terhapus</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->phone_number ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'owner' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->trashed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Terhapus
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$user->trashed())
                                        {{-- DYNAMIC ROUTE UNTUK AKSES --}}
                                        <a href="{{ url('/' . $this->rolePrefix . '/user/akses/' . $user->id) }}"
                                            class="text-cyan-600 hover:text-cyan-900 bg-cyan-50 hover:bg-cyan-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm" title="Atur Akses User">
                                            <i class="fa-solid fa-lock w-4 h-4 mr-1"></i> Akses
                                        </a>

                                        {{-- TOMBOL DETAIL HANYA UNTUK OWNER --}}
                                        @if(auth()->check() && auth()->user()->role === 'owner')
                                        <a href="{{ url('/owner/user/detail/' . $user->id) }}"
                                            class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm" title="Detail User">
                                            <i class="fa-solid fa-eye w-4 h-4 mr-1"></i> Detail
                                        </a>
                                        @endif

                                        {{-- Tombol Edit --}}
                                        <button
                                            wire:click="openModal(true, {{ $user->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                        </button>

                                        {{-- Tombol Soft Delete --}}
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Soft Delete User?',
                                                text: 'User akan diarsipkan ke tab Terhapus.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#f59e0b',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Soft Delete!',
                                                cancelButtonText: 'Batal'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) { 
                                                    const res = await $wire.softDelete({{ $user->id }});
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
                                        {{-- Tombol Restore --}}
                                        <button type="button" x-data x-on:click="
                                            Swal.fire({
                                                title: 'Restore User?',
                                                text: 'User ini akan kembali aktif.',
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonColor: '#10b981',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Restore!',
                                                cancelButtonText: 'Batal'
                                            }).then(async (result) => {
                                                if (result.isConfirmed) { 
                                                    const res = await $wire.restore({{ $user->id }});
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
                                            {{-- Tombol Hapus Permanen KHUSUS OWNER --}}
                                            <button type="button" x-data x-on:click="
                                                Swal.fire({
                                                    title: 'Hapus Permanen?',
                                                    text: 'PERINGATAN: Tindakan ini akan menghapus user secara permanen. Data yang dihapus tidak dapat dikembalikan!',
                                                    icon: 'error',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#ef4444',
                                                    cancelButtonColor: '#6b7280',
                                                    confirmButtonText: 'Ya, Hapus Permanen!',
                                                    cancelButtonText: 'Batal'
                                                }).then(async (result) => {
                                                    if (result.isConfirmed) { 
                                                        const res = await $wire.forceDelete({{ $user->id }});
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
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-users w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada data user yang terhapus.' : 'Tidak ada data user.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="mt-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                    <div class="flex-1 flex justify-end">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Overlay Form Tambah/Edit --}}
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
                            <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-user-plus' }} w-5 h-5 mr-2"></i>
                            {{ $isEdit ? 'Edit User' : 'Tambah User' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">Isi form berikut untuk {{ $isEdit ? 'mengedit' : 'menambah' }} user</p>

                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                    <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    @error('email') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                                    <input type="text" wire:model="phone_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    @error('phone_number') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    @if($isEdit)
                                        <span class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin ubah password.</span>
                                    @endif
                                    @error('password') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- ✨ Hanya tampilkan pilihan Role jika user yang login adalah Owner ✨ --}}
                                @if(auth()->check() && auth()->user()->role === 'owner')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select wire:model="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    @error('role') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('status') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <input wire:model="is_active" id="is_active" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                                    </div>
                                    @error('is_active') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                                    <div class="mt-1 flex items-center">
                                        <input type="file" wire:model="image" class="w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100">
                                    </div>
                                    @if ($currentImage)
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 mb-1">Foto saat ini:</p>
                                            <img src="{{ Storage::url($currentImage) }}" class="w-16 h-16 rounded-full object-cover border">
                                        </div>
                                    @endif
                                    @error('image') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                                    <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                                </button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center">
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