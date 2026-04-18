<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-3">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-indigo-600 transition-colors duration-200">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                <a href="{{ route('owner.users') }}" class="hover:text-indigo-600 transition-colors duration-200">Pengguna</a>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                <span class="text-slate-800 font-medium">{{ $user->name }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('owner.users') }}" 
                       class="group w-10 h-10 rounded-xl bg-white/90 backdrop-blur-sm border border-slate-200/80 flex items-center justify-center shadow-sm hover:shadow-md hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-300">
                        <i class="fa-solid fa-arrow-left text-slate-500 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-slate-800 via-indigo-800 to-slate-800 bg-clip-text text-transparent">
                            Detail Pengguna
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">Informasi profil dan riwayat transaksi lengkap</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/80 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="relative h-36 bg-gradient-to-br from-slate-700 via-indigo-800 to-slate-800">
                        @if($user->trashed())
                            <div class="absolute top-4 right-4 bg-red-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 z-10">
                                <i class="fa-solid fa-trash-can"></i> Terhapus
                            </div>
                        @endif
                        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>
                    
                    <div class="relative px-6">
                        <div class="flex justify-center -mt-14 mb-4">
                            <div class="relative">
                                @if($user->image)
                                    <img src="{{ Storage::url($user->image) }}" 
                                         class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md bg-white">
                                @else
                                    <div class="w-28 h-28 rounded-full border-4 border-white shadow-md bg-gradient-to-br from-indigo-50 to-slate-100 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-4xl text-slate-400"></i>
                                    </div>
                                @endif
                                <div class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 border-3 border-white shadow-sm"></div>
                            </div>
                        </div>

                        <div class="text-center mb-5">
                            <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                            <p class="text-slate-500 text-sm mt-1">{{ $user->email }}</p>
                            
                            <div class="flex flex-wrap items-center justify-center gap-2 mt-3">
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-gradient-to-r from-indigo-50 to-indigo-100 text-indigo-700 border border-indigo-200 shadow-sm">
                                    <i class="fa-solid fa-shield-halved mr-1.5"></i> {{ ucfirst($user->role) }}
                                </span>
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-gradient-to-r from-emerald-50 to-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-gradient-to-r from-amber-50 to-amber-100 text-amber-700 border border-amber-200' }} shadow-sm">
                                    <i class="fa-solid {{ $user->is_active ? 'fa-circle-check' : 'fa-circle-exclamation' }} mr-1.5"></i> 
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200/60 px-6 py-5 bg-slate-50/50">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">
                            <i class="fa-solid fa-circle-info mr-2 text-indigo-500"></i> Informasi Kontak
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 group">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-500 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 group-hover:scale-105 transition-all duration-200">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Nomor Telepon</p>
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-700">{{ $user->phone_number ?? '—' }}</p>
                                        @if($user->phone_number)
                                            @php
                                                $phoneNumber = preg_replace('/[^0-9]/', '', $user->phone_number);
                                                if (str_starts_with($phoneNumber, '0')) {
                                                    $phoneNumber = '62' . substr($phoneNumber, 1);
                                                } elseif (!str_starts_with($phoneNumber, '62')) {
                                                    $phoneNumber = '62' . $phoneNumber;
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $phoneNumber }}" 
                                               target="_blank"
                                               class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-green-50 text-green-600 border border-green-200 hover:bg-green-500 hover:text-white transition-all duration-200"
                                               title="Hubungi via WhatsApp">
                                                <i class="fa-brands fa-whatsapp text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 group">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0 group-hover:bg-teal-100 group-hover:scale-105 transition-all duration-200">
                                    <i class="fa-regular fa-calendar"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Bergabung Sejak</p>
                                    <p class="font-semibold text-slate-700">{{ $user->created_at->format('d F Y') }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <i class="fa-solid fa-receipt text-blue-500 text-xl"></i>
                            <span class="text-[10px] font-bold text-blue-700 bg-white/60 border border-blue-200/50 px-2.5 py-1 rounded-full uppercase tracking-wider">Total</span>
                        </div>
                        <p class="text-3xl font-bold text-blue-800">{{ $totalTransactions }}</p>
                        <p class="text-sm text-blue-600 mt-1 font-medium">Transaksi</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-5 border border-emerald-200 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                            <span class="text-[10px] font-bold text-emerald-700 bg-white/60 border border-emerald-200/50 px-2.5 py-1 rounded-full uppercase tracking-wider">Sukses</span>
                        </div>
                        <p class="text-3xl font-bold text-emerald-800">{{ $successTransactions }}</p>
                        <p class="text-sm text-emerald-600 mt-1 font-medium">Berhasil</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300 lg:col-span-2">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 rounded-full bg-white/60 flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-purple-600"></i>
                            </div>
                            <span class="text-[10px] font-bold text-purple-700 bg-white/60 border border-purple-200/50 px-2.5 py-1 rounded-full uppercase tracking-wider">Total Pembelanjaan</span>
                        </div>
                        <p class="text-2xl sm:text-3xl font-bold text-purple-800">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-purple-600 mt-1 font-medium">Total Akumulasi Nominal</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/80 overflow-hidden hover:shadow-xl transition-all duration-300">
                    
                    <div class="p-6 border-b border-slate-200/60 bg-slate-50/30">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">
                                    <i class="fa-solid fa-history mr-2 text-indigo-500"></i> Riwayat Transaksi
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">Daftar semua pembelian tryout dan bundle</p>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-3">
                                <select wire:model.live="statusFilter" 
                                        class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Settlement">✅ Success</option>
                                    <option value="Settlement">✅ Settlement</option>
                                    <option value="pending">⏳ Pending</option>
                                    <option value="failed">❌ Failed</option>
                                    <option value="expire">⌛ Expired</option>
                                </select>
                                
                                <select wire:model.live="perPage" 
                                        class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm">
                                    <option value="10">10 per halaman</option>
                                    <option value="25">25 per halaman</option>
                                    <option value="50">50 per halaman</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" 
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Cari berdasarkan invoice, status, atau nama item..." 
                                       class="w-full pl-12 pr-12 py-3 border border-slate-200 rounded-xl bg-white shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @if($search)
                                    <button wire:click="$set('search', '')" 
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200/80">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Invoice & Waktu</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($transactions as $transaction)
                                    <tr class="hover:bg-slate-50 transition-all duration-200 group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center group-hover:bg-indigo-50 transition-all duration-300">
                                                    <i class="fa-solid fa-receipt text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800 text-sm">#{{ $transaction->order_id ?? $transaction->id }}</p>
                                                    <p class="text-xs text-slate-400 mt-0.5">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($transaction->id_bundle && $transaction->bundle)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase mb-1.5 border border-indigo-100">
                                                    <i class="fa-solid fa-layer-group"></i> Bundle
                                                </span>
                                                <p class="text-sm font-medium text-slate-700 mt-1">{{ $transaction->bundle->title }}</p>
                                            @elseif($transaction->id_tryout && $transaction->tryout)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold uppercase mb-1.5 border border-teal-100">
                                                    <i class="fa-solid fa-file-alt"></i> Tryout
                                                </span>
                                                <p class="text-sm font-medium text-slate-700 mt-1">{{ $transaction->tryout->title }}</p>
                                            @else
                                                <p class="text-sm text-slate-400 italic">Item tidak tersedia</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="font-bold text-slate-800">Rp {{ number_format($transaction->amount ?? 0, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusMap = [
                                                    'success' => ['bg-emerald-50', 'text-emerald-700', 'fa-circle-check', 'border-emerald-200'],
                                                    'settlement' => ['bg-emerald-50', 'text-emerald-700', 'fa-circle-check', 'border-emerald-200'],
                                                    'pending' => ['bg-amber-50', 'text-amber-700', 'fa-hourglass-half', 'border-amber-200'],
                                                    'failed' => ['bg-rose-50', 'text-rose-700', 'fa-circle-xmark', 'border-rose-200'],
                                                    'expire' => ['bg-slate-50', 'text-slate-600', 'fa-clock', 'border-slate-200'],
                                                ];
                                                $status = strtolower($transaction->status);
                                                $colors = $statusMap[$status] ?? ['bg-slate-50', 'text-slate-600', 'fa-question', 'border-slate-200'];
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $colors[0] }} {{ $colors[1] }} {{ $colors[3] }}">
                                                <i class="fa-solid {{ $colors[2] }}"></i> {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button wire:click="showDetail('{{ $transaction->id }}')" 
                                                    class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all duration-200">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                            <a href="{{ route('payment.finish', ['order_id' => $transaction->order_id]) }}" 
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                                                target="_blank">
                                                <i class="fas fa-file-invoice mr-1.5 mt-0.5"></i> Invoice
                                            </a> 
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center bg-slate-50/50">
                                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-50 mb-4 border border-indigo-100">
                                                <i class="fa-solid fa-inbox text-3xl text-indigo-300"></i>
                                            </div>
                                            <h4 class="text-lg font-semibold text-slate-700 mb-1">Belum Ada Transaksi</h4>
                                            <p class="text-sm text-slate-400">Pengguna ini belum melakukan pembelian apapun</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($transactions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-50">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($showModal && $selectedTransaction)
        <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-2xl p-4 mx-auto animate-in fade-in zoom-in duration-200">
                
                <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden">
                    
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            Detail Transaksi
                        </h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-rose-500 bg-slate-100 hover:bg-rose-50 w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Order ID</p>
                                <p class="font-bold text-slate-800 text-lg">#{{ $selectedTransaction->order_id ?? $selectedTransaction->id }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Pembayaran</p>
                                @php
                                    $status = strtolower($selectedTransaction->status);
                                    $statusMap = [
                                        'success' => ['bg-emerald-100', 'text-emerald-700', 'fa-circle-check'],
                                        'settlement' => ['bg-emerald-100', 'text-emerald-700', 'fa-circle-check'],
                                        'pending' => ['bg-amber-100', 'text-amber-700', 'fa-hourglass-half'],
                                        'failed' => ['bg-rose-100', 'text-rose-700', 'fa-circle-xmark'],
                                        'expire' => ['bg-slate-200', 'text-slate-600', 'fa-clock'],
                                    ];
                                    $colors = $statusMap[$status] ?? ['bg-slate-100', 'text-slate-600', 'fa-question'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-bold {{ $colors[0] }} {{ $colors[1] }}">
                                    <i class="fa-solid {{ $colors[2] }}"></i> {{ ucfirst($status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Waktu Transaksi</p>
                                    <p class="font-medium text-slate-700">
                                        <i class="fa-regular fa-calendar-alt text-slate-400 mr-1.5"></i>
                                        {{ $selectedTransaction->created_at->format('d F Y, H:i:s') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Metode Pembayaran</p>
                                    <p class="font-medium text-slate-700 uppercase">
                                        <i class="fa-regular fa-credit-card text-slate-400 mr-1.5"></i>
                                        {{ str_replace('_', ' ', $selectedTransaction->payment_type ?? 'Belum dipilih') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Item yang Dibeli</p>
                                    @if($selectedTransaction->id_bundle && $selectedTransaction->bundle)
                                        <div class="flex items-start gap-2">
                                            <span class="mt-0.5 px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase border border-indigo-100"><i class="fa-solid fa-layer-group"></i> Bundle</span>
                                            <p class="font-medium text-slate-700">{{ $selectedTransaction->bundle->title }}</p>
                                        </div>
                                    @elseif($selectedTransaction->id_tryout && $selectedTransaction->tryout)
                                        <div class="flex items-start gap-2">
                                            <span class="mt-0.5 px-2 py-0.5 rounded bg-teal-50 text-teal-700 text-[10px] font-bold uppercase border border-teal-100"><i class="fa-solid fa-file-alt"></i> Tryout</span>
                                            <p class="font-medium text-slate-700">{{ $selectedTransaction->tryout->title }}</p>
                                        </div>
                                    @else
                                        <p class="font-medium text-slate-400 italic">Item tidak tersedia</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Nominal</p>
                                    <p class="font-bold text-indigo-600 text-xl">
                                        Rp {{ number_format($selectedTransaction->amount ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                        <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-colors focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            Tutup Detail
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>