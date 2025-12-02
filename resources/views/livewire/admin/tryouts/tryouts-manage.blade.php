<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Tryout</h2>

            <a href="{{ route('admin.tryouts.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                {{-- Font Awesome: fa-plus --}}
                <i class="fa-solid fa-plus w-4 h-4 mr-1"></i> Tambah Tryout
            </a>

        </div>

        {{-- Tabs untuk filter status --}}
        <div class="mb-4 flex border-b border-gray-200">
            <button
                wire:click="$set('showTrashed', false)"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ !$showTrashed ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{-- Font Awesome: fa-list --}}
                <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Aktif
            </button>
            <button
                wire:click="$set('showTrashed', true)"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $showTrashed ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{-- Font Awesome: fa-trash-can --}}
                <i class="fa-solid fa-trash-can w-4 h-4 mr-1 inline"></i> Terhapus
            </button>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari judul tryout..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            />
        </div>

        {{-- Flash message --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    {{-- Font Awesome: fa-circle-check --}}
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800 transition-colors duration-200">
                    {{-- Font Awesome: fa-xmark --}}
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center">
                    {{-- Font Awesome: fa-circle-exclamation --}}
                    <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                    {{-- Font Awesome: fa-xmark --}}
                    <i class="fa-solid fa-xmark w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Table Tryouts --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Tryout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">question</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($tryouts as $index => $tryout)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $tryout->trashed() ? 'bg-red-50' : '' }}">
                            {{-- PENOMORAN DEFAULT LARAVEL PAGINATION --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ($tryouts->currentPage() - 1) * $tryouts->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    {{-- BLOK ICON BUKU DIHILANGKAN --}}
                                    <span class="font-medium text-sm {{ $tryout->trashed() ? 'text-gray-400' : 'text-gray-900' }}">{{ $tryout->title }}</span>
                                    @if($tryout->trashed())
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Terhapus</span>
                                    @endif
                                    @if($tryout->is_hots)
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">HOTS</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{-- asumsi duration_formatted sudah didefinisikan di model atau accessor --}}
                                {{ $tryout->duration ?? '-' }} menit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    @if($tryout->discount)
                                        <span class="text-sm text-gray-400 line-through">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                        <span class="text-green-600 font-medium text-sm">Rp {{ number_format($tryout->price - $tryout->discount, 0, ',', '.') }}</span>
                                        <span class="text-xs text-red-600">Diskon Rp {{ number_format($tryout->discount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-600 text-sm">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tryout->trashed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Terhapus
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tryout->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $tryout->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$tryout->trashed())
                                        <a href="{{ route('admin.tryouts.edit', $tryout->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            <i class="fa-solid fa-pen-to-square w-4 h-4 mr-1"></i> Edit
                                        </a>

                                        <button
                                            wire:click="toggleStatus({{ $tryout->id }})"
                                            class="text-{{ $tryout->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $tryout->is_active ? 'yellow' : 'green' }}-900 bg-{{ $tryout->is_active ? 'yellow' : 'green' }}-50 hover:bg-{{ $tryout->is_active ? 'yellow' : 'green' }}-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            {{-- Font Awesome: fa-pause/fa-play --}}
                                            <i class="fa-solid {{ $tryout->is_active ? 'fa-pause' : 'fa-play' }} w-4 h-4 mr-1"></i> {{ $tryout->is_active ? 'Draft' : 'Public' }}
                                        </button>

                                        <button
                                            wire:click="toggleHots({{ $tryout->id }})"
                                            class="text-{{ $tryout->is_hots ? 'gray' : 'red' }}-600 hover:text-{{ $tryout->is_hots ? 'gray' : 'red' }}-900 bg-{{ $tryout->is_hots ? 'gray' : 'red' }}-50 hover:bg-{{ $tryout->is_hots ? 'gray' : 'red' }}-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            {{-- Font Awesome: fa-star --}}
                                            <i class="fa-solid fa-star w-4 h-4 mr-1"></i> {{ $tryout->is_hots ? 'Unmark HOTS' : 'Mark HOTS' }}
                                        </button>

                                        <button
                                            wire:click="confirmDelete({{ $tryout->id }})"
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            {{-- Font Awesome: fa-trash-can --}}
                                            <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus
                                        </button>
                                    @else
                                        <button
                                            wire:click="restore({{ $tryout->id }})"
                                            class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            {{-- Font Awesome: fa-rotate-left --}}
                                            <i class="fa-solid fa-rotate-left w-4 h-4 mr-1"></i> Restore
                                        </button>
                                        <button
                                            wire:click="forceDelete({{ $tryout->id }})"
                                            onclick="return confirm('Yakin ingin menghapus permanen? Data tidak dapat dikembalikan.')"
                                            class="text-red-700 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                            {{-- Font Awesome: fa-trash-can-xmark --}}
                                            <i class="fa-solid fa-trash-can-xmark w-4 h-4 mr-1"></i> Hapus Permanen
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{-- Link ke halaman pertanyaan tryout --}}
                                <a href="{{ route('admin.tryouts.questions', $tryout->id) }}"
                                    class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                    {{-- Font Awesome: fa-square-plus --}}
                                    <i class="fa-solid fa-square-plus w-4 h-4 mr-1"></i> Soal
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    {{-- Font Awesome: fa-book --}}
                                    <i class="fa-solid fa-book w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>{{ $showTrashed ? 'Tidak ada data tryout yang terhapus.' : 'Tidak ada data tryout.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $tryouts->links() }}
        </div>
    </div>

    {{-- Modal Overlay --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-20 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4 w-xl m-auto">
                <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full mx-auto
                                     max-h-[90vh] overflow-y-auto transform transition-all">

                    {{-- Tombol close --}}
                    <button wire:click="closeModal"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200
                                     bg-gray-100 hover:bg-gray-200 rounded-full p-1">
                        {{-- Font Awesome: fa-xmark --}}
                        <i class="fa-solid fa-xmark w-5 h-5"></i>
                    </button>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            {{-- Font Awesome: fa-pen-to-square / fa-plus --}}
                            <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-plus' }} w-5 h-5 mr-2"></i>
                            {{ $isEdit ? 'Edit Tryout' : 'Tambah Tryout' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">Isi form berikut untuk {{ $isEdit ? 'mengedit' : 'menambah' }} tryout</p>

                        {{-- Form (Disederhanakan untuk tampilan modal) --}}
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tryout</label>
                                        <input type="text" wire:model="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        @error('title') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                        <input type="text" wire:model="slug" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        @error('slug') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit)</label>
                                        <input type="number" wire:model="duration" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        @error('duration') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                        <input type="number" wire:model="price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        @error('price') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Diskon (Rp)</label>
                                        <input type="number" wire:model="discount" min="0" :max="$price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        @error('discount') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quote</label>
                                        <textarea wire:model="quote" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                                        @error('quote') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                                        <textarea wire:model="content" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                                        @error('content') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="flex space-x-4">
                                        <div class="flex items-center">
                                            <input wire:model="is_hots" id="is_hots" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="is_hots" class="ml-2 block text-sm text-gray-900">HOTS</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input wire:model="is_active" id="is_active" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                                        </div>
                                    </div>
                                    @error('is_hots') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                    @error('is_active') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                                    {{-- Feather: x -> Font Awesome: fa-xmark --}}
                                    <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                                </button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center">
                                    {{-- Feather: check / save -> Font Awesome: fa-check / fa-floppy-disk --}}
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
                        {{-- Feather: alert-triangle -> Font Awesome: fa-triangle-exclamation --}}
                        <i class="fa-solid fa-triangle-exclamation w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-2" id="delete-confirmation-title">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Apakah kamu yakin ingin menghapus tryout ini? Data akan dipindahkan ke tempat sampah dan dapat dipulihkan kapan saja.</p>

                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                            {{-- Feather: x -> Font Awesome: fa-xmark --}}
                            <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Batal
                        </button>
                        <button wire:click="delete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center">
                            {{-- Feather: trash -> Font Awesome: fa-trash-can --}}
                            <i class="fa-solid fa-trash-can w-4 h-4 mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>