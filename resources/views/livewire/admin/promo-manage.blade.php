<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8 border border-gray-100">
        {{-- Header Form --}}
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Promo Terlaris</h2>
                <p class="text-sm text-gray-500 mt-1">Pilih Try Out atau Bundle untuk ditampilkan di halaman depan section Promo.</p>
            </div>
            {{-- Indikator Kuota --}}
            <div class="bg-gray-100 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium">
                <span class="text-gray-600">Kuota:</span> 
                <span class="{{ $totalPromos >= 3 ? 'text-red-600' : 'text-blue-600' }}">{{ $totalPromos }}/3</span>
            </div>
        </div>

        {{-- Flash Message Success --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center shadow-sm">
                <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Flash Message Error Kuota (PENTING) --}}
        @error('general')
            <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center shadow-sm">
                <i class="fa-solid fa-triangle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                <span>{{ $message }}</span>
            </div>
        @enderror

        {{-- Form Tambah Promo (Disable form jika kuota penuh) --}}
        <form wire:submit.prevent="addToPromo" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end bg-gray-50 p-5 rounded-xl border border-gray-200 {{ $totalPromos >= 3 ? 'opacity-50 pointer-events-none' : '' }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jenis</label>
                <select wire:model.live="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" {{ $totalPromos >= 3 ? 'disabled' : '' }}>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="tryout">Try Out</option>
                    <option value="bundle">Bundle</option>
                </select>
                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Item</label>
                <select wire:model="itemId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" {{ empty($type) || $totalPromos >= 3 ? 'disabled' : '' }}>
                    <option value="">-- Pilih Item --</option>
                    @foreach($availableItems as $item)
                        <option value="{{ $item->id }}">{{ $item->title }} (Rp {{ number_format($item->price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                @error('itemId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center disabled:bg-blue-300" {{ $totalPromos >= 3 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-plus mr-2"></i> Tambahkan ke Promo
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Promo Aktif --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Promo Aktif</h3>
        
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Asli</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($promos as $index => $promo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ ($promos->currentPage() - 1) * $promos->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($promo->promoable_type === 'App\Models\Tryout')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-file-alt mr-1 mt-0.5"></i> Try Out
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-layer-group mr-1 mt-0.5"></i> Bundle
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $promo->promoable->title ?? 'Item Dihapus' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Rp {{ number_format($promo->promoable->price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="removeFromPromo({{ $promo->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-md transition-colors inline-flex items-center shadow-sm">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-tags w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>Belum ada item yang ditambahkan ke Promo Terlaris.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $promos->links('') }}
        </div>
    </div>
</div>