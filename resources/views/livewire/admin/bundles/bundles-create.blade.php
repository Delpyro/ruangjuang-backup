<div class="max-w-6xl mx-auto p-6 bg-white shadow-lg rounded-xl border border-gray-200">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Buat Bundle Baru</h2>
        <p class="text-gray-600">Bundle memungkinkan Anda menggabungkan banyak tryout dengan harga lebih hemat.</p>
    </div>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-xmark w-5 h-5 text-red-600 mr-2"></i>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="store" class="space-y-6">
        {{-- Title & Slug --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Bundle <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model.blur="title"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('title') border-red-500 @enderror"
                    placeholder="Contoh: Paket Intensif SBMPTN 2024"
                >
                @error('title')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model.blur="slug"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('slug') border-red-500 @enderror"
                >
                @error('slug')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Description (TinyMCE) --}}
        <div wire:ignore>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi Bundle
            </label>
            <textarea
                id="tinymce-description-create" {{-- ID untuk target TinyMCE --}}
                rows="6"
                wire:model="description"
                class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('description') border-red-500 @enderror"
                placeholder="Penjelasan lengkap tentang isi bundle, manfaat, dan keunggulan..."
            >{{ $description ?? '' }}</textarea>
            @error('description')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tryouts Selection dengan Search --}}
        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Pilih Tryout dalam Bundle <span class="text-red-500">*</span>
                </label>
                <span class="text-sm text-gray-500">
                    Terpilih: <span class="font-semibold">{{ $this->selectedTryoutsCount }}</span> tryout
                </span>
            </div>

            {{-- Search --}}
            <div class="mb-4">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari tryout berdasarkan judul..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- Daftar Tryout --}}
            <div class="border border-gray-300 rounded-lg bg-white max-h-96 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            {{-- Kolom Checkbox Utama --}}
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input
                                    type="checkbox"
                                    wire:model="selectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tryout</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Individual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tryouts as $tryout)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                {{-- Kolom Checkbox Pemilihan --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input
                                        type="checkbox"
                                        wire:model="selected_tryout_ids"
                                        value="{{ $tryout->id }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>
                                {{-- Kolom Nama Tryout --}}
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $tryout->title }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($tryout->price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    @if($search)
                                        Tidak ada tryout yang sesuai dengan pencarian "{{ $search }}".
                                    @else
                                        Tidak ada tryout tersedia.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('selected_tryout_ids')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Pricing Section --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Harga Bundle (Rp) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    wire:model.blur="price"
                    min="0"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('price') border-red-500 @enderror"
                    placeholder="0"
                >
                @error('price')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (Rp)</label>
                <input
                    type="number"
                    wire:model.blur="discount"
                    min="0"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('discount') border-red-500 @enderror"
                    placeholder="0"
                >
                @error('discount')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                <input
                    type="datetime-local"
                    wire:model="expired_at"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('expired_at') border-red-500 @enderror"
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                >
                @error('expired_at')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Pricing Summary --}}
        @if($this->selectedTryoutsCount > 0)
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Harga</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Harga Individual ({{ $this->selectedTryoutsCount }} tryout):</span>
                    <span class="font-medium text-gray-700">Rp {{ number_format($this->totalIndividualPrice, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Harga Bundle:</span>
                    <span class="font-medium text-gray-700">Rp {{ number_format($price, 0, ',', '.') }}</span>
                </div>
                @if($discount > 0)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Diskon:</span>
                    <span class="font-medium text-red-600">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center text-lg font-semibold border-t border-blue-200 pt-3">
                    <span class="text-gray-800">Harga Final:</span>
                    <span class="text-green-600">Rp {{ number_format($this->finalPrice, 0, ',', '.') }}</span>
                </div>
                @if($this->hasSavings)
                <div class="flex justify-between items-center pt-2">
                    <span class="text-gray-600">Total Hemat:</span>
                    <span class="font-bold text-green-600">
                        {{ $this->savingsPercentage }}%
                        (Rp {{ number_format($this->totalIndividualPrice - $this->finalPrice, 0, ',', '.') }})
                    </span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Status Settings --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="is_active"
                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                >
                <span class="text-gray-700 font-medium">Aktifkan Bundle</span>
            </label>

            <div class="text-sm text-gray-500">
                @if($is_active)
                    <span class="inline-flex items-center text-green-600">
                        <i class="fa-solid fa-check w-4 h-4 mr-1"></i>
                        Bundle akan aktif dan dapat dibeli
                    </span>
                @else
                    <span class="inline-flex items-center text-red-600">
                        <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i>
                        Bundle akan dinonaktifkan
                    </span>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            {{-- ✨ DYNAMIC ROUTE UNTUK TOMBOL BATAL ✨ --}}
            <a
                href="{{ route($this->rolePrefix . '.bundles.index') }}"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium flex items-center"
            >
                <i class="fa-solid fa-xmark w-4 h-4 mr-2"></i> Batal
            </a>
            <button
                type="submit"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                <i wire:loading.remove class="fa-solid fa-floppy-disk w-5 h-5"></i>
                <i wire:loading class="fa-solid fa-spinner w-5 h-5 animate-spin"></i>
                <span wire:loading.remove>Simpan Bundle</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // FUNGSI INIT TINYMCE (TIDAK MEMUAT SCRIPT EKSTERNAL)
    function initTinyMCE(selector, callback) {
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE belum dimuat dari layout utama.');
            return;
        }

        tinymce.init({
            selector: selector,
            plugins: 'link lists table code help wordcount',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | table | code | help',
            menubar: false,
            height: 300,
            branding: false,
            promotion: false,
            init_instance_callback: function(editor) {
                if (typeof callback === 'function') {
                    callback(editor);
                }
            }
        });
    }

    // FUNGSI CALLBACK LIVEWIRE
    const livewireTinyMCE = (editor, livewireProperty) => {
        editor.on('init', function(e) {
            editor.setContent(@this.get(livewireProperty) || '');
        });

        editor.on('change', function(e) {
            @this.set(livewireProperty, editor.getContent());
        });
        editor.on('blur', function(e) {
            @this.set(livewireProperty, editor.getContent());
        });
    };

    // Panggil inisialisasi setelah Livewire memuat DOM
    document.addEventListener('livewire:load', function () {
        initTinyMCE('textarea#tinymce-description-create', (editor) => livewireTinyMCE(editor, 'description'));
    });

    // PENTING: Menangani Livewire v3 (livewire:navigated)
    document.addEventListener('livewire:navigated', function () {
        if (typeof tinymce !== 'undefined') {
            if (tinymce.get('tinymce-description-create')) {
                tinymce.get('tinymce-description-create').destroy();
            }
        }

        setTimeout(() => {
            initTinyMCE('textarea#tinymce-description-create', (editor) => livewireTinyMCE(editor, 'description'));
        }, 100);
    });

    // PENTING: Menangani Livewire v2 atau update komponen biasa
    document.addEventListener('livewire:update', function() {
        if (typeof tinymce !== 'undefined' && !tinymce.get('tinymce-description-create')) {
            initTinyMCE('textarea#tinymce-description-create', (editor) => livewireTinyMCE(editor, 'description'));
        }
    });
</script>
@endpush