<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Users</h2>

            <button
                wire:click="openModal(false)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fa-solid fa-plus w-4 h-4 mr-1"></i> Tambah User
            </button>
        </div>

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

        {{-- Flash message --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800 transition-colors duration-200">
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
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Error Message dari Livewire --}}
        @if($errorMessage)
            <div class="mb-4 p-4 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-triangle-exclamation w-5 h-5 mr-3 text-yellow-600"></i>
                    <span>{{ $errorMessage }}</span>
                </div>
                <button type="button" wire:click="$set('errorMessage', '')" class="text-yellow-600 hover:text-yellow-800 transition-colors duration-200">
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

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
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
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
                                        <button
                                            wire:click="openModal(true, {{ $user->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                        </button>

                                        <button
                                            wire:click="confirmDelete({{ $user->id }})"
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus
                                        </button>
                                    @else
                                        <button
                                            wire:click="restore({{ $user->id }})"
                                            class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Restore
                                        </button>
                                        <button
                                            wire:click="confirmForceDelete({{ $user->id }})"
                                            class="text-red-700 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-trash-can-xmark w-4 h-4 mr-1"></i> Hapus Permanen
                                        </button>
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

        {{-- Pagination (Sudah Diperbarui) --}}
        @if ($users->hasPages())
            {{-- Pagination --}}
                <div class="mt-6">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                        <div class="flex-1 flex justify-end">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
        @endif
    </div>

    {{-- Modal Overlay --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-20 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4 w-xl m-auto">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto
                                max-h-[90vh] overflow-y-auto transform transition-all">

                    {{-- Tombol close --}}
                    <button wire:click="closeModal"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200
                                bg-gray-100 hover:bg-gray-200 rounded-full p-1">
                        <i class="fa-solid fa-xmark w-5 h-5"></i>
                    </button>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-user-plus' }} w-5 h-5 mr-2"></i>
                            {{ $isEdit ? 'Edit User' : 'Tambah User' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">Isi form berikut untuk {{ $isEdit ? 'mengedit' : 'menambah' }} user</p>

                        {{-- Form --}}
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

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select wire:model="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    @error('role') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

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
                                            hover:file:bg-blue-100
                                        ">
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

                            {{-- Actions --}}
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

    {{-- Konfirmasi Delete Overlay --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="delete-confirmation-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <i class="fa-solid fa-triangle-exclamation w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-2" id="delete-confirmation-title">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Apakah kamu yakin ingin menghapus user ini? Data akan dipindahkan ke tempat sampah dan dapat dipulihkan kapan saja.</p>

                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                        </button>
                        <button wire:click="delete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Konfirmasi Force Delete Overlay --}}
    @if($confirmingForceDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="force-delete-confirmation-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <i class="fa-solid fa-octagon-exclamation w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-2" id="force-delete-confirmation-title">Hapus Permanen</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        <strong class="text-red-600">PERINGATAN:</strong> Tindakan ini akan menghapus user secara permanen.
                        Data yang dihapus tidak dapat dikembalikan. Yakin ingin melanjutkan?
                    </p>

                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelForceDelete" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                        </button>
                        <button wire:click="forceDelete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-trash-can-xmark w-4 h-4 mr-1"></i> Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>