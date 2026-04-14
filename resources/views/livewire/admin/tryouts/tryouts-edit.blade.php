<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-xl border border-gray-200">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Tryout</h2>
        <p class="text-gray-600">Edit informasi tryout berikut</p>
    </div>

    {{-- Blok Notifikasi HTML lama dihapus, ditangani global oleh SweetAlert di Layout --}}

    <form wire:submit.prevent="update" class="space-y-6">
        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tryout <span class="text-red-500">*</span></label>
            <input type="text" wire:model.blur="title" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Masukkan judul tryout">
            @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Slug --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-red-500">*</span></label>
            <input type="text" wire:model.blur="slug" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Slug akan otomatis terisi">
            @error('slug') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Category Dropdown --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Tryout <span class="text-red-500">*</span></label>
            <select wire:model.blur="category" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                <option value="umum">Umum</option>
                <option value="khusus">Khusus</option>
            </select>
            @error('category') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Checkboxes --}}
        <div class="flex gap-6">
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="is_hots" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <span class="text-gray-700">Tryout Hot</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <span class="text-gray-700">Aktif</span>
            </label>
        </div>

        {{-- Duration --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit) <span class="text-red-500">*</span></label>
            <input type="number" wire:model.blur="duration" min="1" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Contoh: 120">
            @error('duration') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Content - TinyMCE Target --}}
        <div wire:ignore>
            <label class="block text-sm font-medium text-gray-700 mb-2">Konten Tryout <span class="text-red-500">*</span></label>
            <textarea id="tinymce-content-edit" rows="6" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Deskripsi lengkap tentang tryout...">{!! $content !!}</textarea>
            @error('content') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Quote - TinyMCE Target --}}
        <div wire:ignore>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quote (Opsional)</label>
            <textarea id="tinymce-quote-edit" rows="3" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Kutipan motivasi atau informasi tambahan...">{!! $quote !!}</textarea>
            @error('quote') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Price & Discount --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" wire:model.blur="price" min="0" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="0">
                @error('price') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (Rp)</label>
                <input type="number" wire:model.blur="discount" min="0" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="0">
                @error('discount') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Diskon (Opsional)</label>
                <input type="date" wire:model.blur="discount_start_date" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                @error('discount_start_date') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir Diskon (Opsional)</label>
                <input type="date" wire:model.blur="discount_end_date" class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                @error('discount_end_date') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Harga Setelah Diskon --}}
        @if($discount > 0 && $price > 0)
        <div class="p-4 bg-blue-50 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-gray-700">Harga Normal:</span>
                <span class="text-gray-900 font-medium">Rp {{ number_format($price, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-gray-700">Diskon:</span>
                <span class="text-red-600 font-medium">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-blue-200">
                <span class="text-gray-700 font-semibold">Harga Setelah Diskon:</span>
                <span class="text-green-600 font-bold">Rp {{ number_format($this->finalPrice, 0, ',', '.') }}</span>
            </div>
        </div>
        @elseif($price > 0)
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-semibold">Harga:</span>
                <span class="text-gray-900 font-bold">Rp {{ number_format($price, 0, ',', '.') }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-2">Tidak ada diskon yang diterapkan</p>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.tryouts.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium flex items-center">
                <i class="fa-solid fa-xmark w-4 h-4 mr-2"></i> Batal
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium shadow-sm flex items-center">
                <i class="fa-solid fa-check w-4 h-4 mr-2"></i> Perbarui Tryout
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    window.uploadImageUrl = "{{ route('admin.tinymce.upload.image') }}";
    const livewireTinyMCE = (editor, livewireProperty) => {
        editor.on('change', function(e) { @this.set(livewireProperty, editor.getContent()); });
        editor.on('blur', function(e) { @this.set(livewireProperty, editor.getContent()); });
    };
    document.addEventListener('livewire:load', function () {
        initTinyMCE('textarea#tinymce-content-edit', (editor) => livewireTinyMCE(editor, 'content'));
        initTinyMCE('textarea#tinymce-quote-edit', (editor) => livewireTinyMCE(editor, 'quote'));
    });
    document.addEventListener('livewire:navigated', function () {
        if (tinymce.get('tinymce-content-edit')) { tinymce.get('tinymce-content-edit').destroy(); }
        if (tinymce.get('tinymce-quote-edit')) { tinymce.get('tinymce-quote-edit').destroy(); }
        setTimeout(() => {
            initTinyMCE('textarea#tinymce-content-edit', (editor) => livewireTinyMCE(editor, 'content'));
            initTinyMCE('textarea#tinymce-quote-edit', (editor) => livewireTinyMCE(editor, 'quote'));
        }, 100);
    });
    document.addEventListener('livewire:update', function() {
        if (!tinymce.get('tinymce-content-edit')) { initTinyMCE('textarea#tinymce-content-edit', (editor) => livewireTinyMCE(editor, 'content')); }
        if (!tinymce.get('tinymce-quote-edit')) { initTinyMCE('textarea#tinymce-quote-edit', (editor) => livewireTinyMCE(editor, 'quote')); }
    });
</script>
@endpush