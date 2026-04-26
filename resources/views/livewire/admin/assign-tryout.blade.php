<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Assign Tryout ke User</h2>
            <p class="text-gray-500">Pilih hingga 10 pengguna dan berikan banyak akses tryout sekaligus.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors flex items-center shadow-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center shadow-sm">
            <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center shadow-sm">
            <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- BAGIAN KIRI: Form Pilihan --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Target Assign</h3>
                
                <form wire:submit.prevent="assign">
                    
                    {{-- Multi Select Users --}}
                    <div class="mb-5 relative">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari & Pilih Pengguna (Maks: 10) <span class="text-red-500">*</span></label>
                        
                        {{-- List User Terpilih (Pills) --}}
                        @if(count($selectedUsers) > 0)
                            <div class="mb-3 flex flex-wrap gap-2">
                                @foreach($selectedUsers as $sUser)
                                    <div class="flex items-center bg-indigo-100 text-indigo-800 px-3 py-1.5 rounded-full text-sm font-medium border border-indigo-200 shadow-sm transition-all hover:bg-indigo-200">
                                        <i class="fa-solid fa-user text-indigo-500 mr-2 text-xs"></i>
                                        {{ $sUser['name'] }}
                                        <button type="button" wire:click="removeUser({{ $sUser['id'] }})" class="ml-2 text-indigo-400 hover:text-red-500 focus:outline-none transition-colors" title="Hapus User">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Input Search --}}
                        @if(count($selectedUsers) < 10)
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="userSearch" 
                                       class="py-3 pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       placeholder="Ketik min. 2 huruf nama/email...">
                                
                                <div wire:loading wire:target="userSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fa-solid fa-spinner fa-spin text-indigo-500"></i>
                                </div>
                            </div>

                            {{-- Dropdown Results --}}
                            @if(strlen($userSearch) >= 2)
                                <div class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    @forelse($this->dropdownUsers as $user)
                                        @php
                                            // Cek jika user sudah dipilih
                                            $isAlreadySelected = collect($selectedUsers)->contains('id', $user->id);
                                            // Mutual Disable: Cek jika user ini sudah punya salah satu tryout yang dicentang admin
                                            $alreadyHasTryout = in_array($user->id, $existingUserIds);
                                            $isDisabled = $isAlreadySelected || $alreadyHasTryout;
                                        @endphp
                                        
                                        <div @if(!$isDisabled) wire:click="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}')" @endif 
                                             class="{{ $isDisabled ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'cursor-pointer hover:bg-indigo-50' }} px-4 py-3 border-b border-gray-100 transition-colors last:border-b-0 flex justify-between items-center group">
                                            <div>
                                                <div class="font-medium {{ $isDisabled ? 'text-gray-400' : 'text-gray-900 group-hover:text-indigo-700' }}">
                                                    {{ $user->name }}
                                                    @if($isAlreadySelected)
                                                        <span class="block mt-0.5 text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-sm w-fit">
                                                            <i class="fa-solid fa-check"></i> Sedang Dipilih
                                                        </span>
                                                    @elseif($alreadyHasTryout)
                                                        <span class="block mt-0.5 text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-sm w-fit">
                                                            <i class="fa-solid fa-ban"></i> Punya Tryout Terpilih
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                            @if(!$isDisabled)
                                                <i class="fa-solid fa-plus text-indigo-200 group-hover:text-indigo-600"></i>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="px-4 py-4 text-sm text-gray-500 text-center flex flex-col items-center">
                                            <i class="fa-regular fa-face-frown text-2xl text-gray-300 mb-2"></i>
                                            User tidak ditemukan.
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        @else
                            <div class="text-sm text-red-600 flex items-center bg-red-50 p-3 rounded-lg border border-red-200 shadow-sm mt-2">
                                <i class="fa-solid fa-circle-exclamation mr-2"></i> 
                                Kuota maksimal 10 pengguna telah dipilih. Hapus pengguna lain jika ingin mengganti.
                            </div>
                        @endif
                        
                        @error('selectedUsers') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Info Tryout Terpilih --}}
                    <div class="mb-5 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 p-2 rounded-full mr-3 text-indigo-600">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-indigo-800 font-medium">Tryout Terpilih</p>
                                    <h4 class="text-2xl font-bold text-indigo-900">{{ count($selectedTryouts) }}</h4>
                                </div>
                            </div>
                            <div class="text-right border-l border-indigo-200 pl-4">
                                <p class="text-sm text-indigo-800 font-medium">Total Assign</p>
                                <h4 class="text-2xl font-bold text-indigo-900">{{ count($selectedTryouts) * count($selectedUsers) }}</h4>
                            </div>
                        </div>
                        @error('selectedTryouts') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            @if(count($selectedTryouts) === 0 || count($selectedUsers) === 0) disabled @endif>
                        <span wire:loading.remove wire:target="assign">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Assign Akses Sekarang
                        </span>
                        <span wire:loading wire:target="assign">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        {{-- BAGIAN KANAN: Tabel Tryout --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-4 border-b pb-4 gap-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Tryout Aktif</h3>
                    
                    {{-- Pencarian Tryout --}}
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchTryout" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Cari judul tryout...">
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left w-12">
                                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Tryout</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tryouts as $tryout)
                                @php
                                    // Mutual Disable: Cek apakah tryout ini sudah dimiliki oleh SALAH SATU user yang dipilih
                                    $isOwnedByAny = in_array($tryout->id, $existingTryoutIds);
                                @endphp
                                
                                <tr class="{{ $isOwnedByAny ? 'bg-gray-50' : (in_array($tryout->id, $selectedTryouts) ? 'bg-indigo-50/50' : 'hover:bg-gray-50 transition-colors') }}">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" 
                                               wire:model.live="selectedTryouts" 
                                               value="{{ $tryout->id }}" 
                                               @if($isOwnedByAny) disabled checked @endif
                                               class="rounded border-gray-300 {{ $isOwnedByAny ? 'text-gray-400 cursor-not-allowed' : 'text-indigo-600 focus:ring-indigo-500 cursor-pointer' }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium {{ $isOwnedByAny ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                            {{ $tryout->title }}
                                        </div>
                                        <div class="text-sm mt-1 flex items-center">
                                            @if($isOwnedByAny)
                                                <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-semibold">
                                                    <i class="fa-solid fa-users mr-1"></i> Dimiliki oleh user yang dipilih
                                                </span>
                                            @else
                                                @if($tryout->is_hots) <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full mr-2"><i class="fa-solid fa-fire"></i> HOTS</span> @endif
                                                <span class="text-gray-500"><i class="fa-regular fa-clock"></i> {{ $tryout->durationFormatted }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-medium {{ $isOwnedByAny ? 'text-gray-400' : 'text-gray-900' }}">
                                        Rp {{ number_format($tryout->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fa-solid fa-file-circle-xmark w-12 h-12 text-gray-300 mb-2"></i>
                                        <p>Tidak ada tryout yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tryouts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>