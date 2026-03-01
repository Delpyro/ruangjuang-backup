<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        {{-- Header Baru --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('admin.transactions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center mb-2">
                    <i class="fa-solid fa-arrow-left w-3 h-3 mr-1"></i> Kembali ke Ringkasan
                </a>
                <h2 class="text-2xl font-bold text-gray-800">
                    Detail Transaksi: {{ $itemTitle }} 
                    <span class="text-sm font-semibold text-gray-500">({{ ucfirst($itemType) }})</span>
                </h2>
            </div>
            
            {{-- Tombol Export PDF --}}
            <button
                wire:click="exportToPdf"
                wire:loading.attr="disabled"
                wire:target="exportToPdf"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center shadow-md">
                <i class="fa-solid fa-file-pdf w-4 h-4 mr-1" wire:loading.class="animate-spin" wire:target="exportToPdf"></i> 
                <span wire:loading.remove wire:target="exportToPdf">Export PDF</span>
                <span wire:loading wire:target="exportToPdf">Membuat PDF...</span>
            </button>
        </div>

        {{-- Tabs untuk filter status --}}
        <div class="mb-4 flex flex-wrap border-b border-gray-200 gap-y-2">
            <button
                wire:click="$set('filterStatus', 'all')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-list w-4 h-4 mr-1 inline"></i> Semua
            </button>
            <button
                wire:click="$set('filterStatus', 'success')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'success' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-check-circle w-4 h-4 mr-1 inline"></i> Success
            </button>
            <button
                wire:click="$set('filterStatus', 'pending')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-hourglass-half w-4 h-4 mr-1 inline"></i> Pending
            </button>
            <button
                wire:click="$set('filterStatus', 'failed')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'failed' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-times-circle w-4 h-4 mr-1 inline"></i> Failed
            </button>
            
            {{-- Tab Tambahan: Berbayar dan Gratis --}}
            <div class="w-px h-6 bg-gray-300 mx-2 self-center hidden sm:block"></div>
            
            <button
                wire:click="$set('filterStatus', 'berbayar')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'berbayar' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-money-bill-wave w-4 h-4 mr-1 inline"></i> Berbayar
            </button>
            <button
                wire:click="$set('filterStatus', 'gratis')"
                class="py-2 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $filterStatus == 'gratis' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fa-solid fa-gift w-4 h-4 mr-1 inline"></i> Gratis
            </button>
        </div>

        {{-- Search & Filter Bulan --}}
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari Order ID, Nama User, atau Payment Method..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                />
            </div>
            
            <div class="flex-shrink-0">
                <select 
                    wire:model.live="filterMonth" 
                    id="filterMonth"
                    class="w-full md:w-auto border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                    <option value="all">Semua Bulan</option>
                    @foreach ($months as $month)
                        <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Akhir Search & Filter Bulan --}}

        {{-- Flash messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 flex items-center justify-between shadow-sm"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check w-5 h-5 mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 flex items-center justify-between shadow-sm"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3 text-red-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
        
        {{-- Table Transaksi --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID & Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Bayar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" wire:key="trx-{{ $transaction->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-sm text-gray-900">{{ $transaction->order_id }}</span>
                                <span class="block text-xs text-gray-500">{{ $transaction->created_at->isoFormat('D MMM YYYY, HH:mm') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $transaction->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $transaction->item->title ?? 'N/A' }}
                                <span class="text-xs text-gray-500 block">
                                    ({{ $transaction->tryout_id ? 'Tryout' : 'Bundle' }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                {{ $transaction->formatted_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-medium text-gray-800">{{ $transaction->payment_method ?? '-' }}</span>
                                <span class="block text-xs text-gray-500">({{ $transaction->payment_type ?? 'N/A' }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($transaction->isSuccess())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Success
                                    </span>
                                @elseif ($transaction->isPending())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif ($transaction->isFailed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button
                                        wire:click="openModal({{ $transaction->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                        <i class="fa-solid fa-eye w-4 h-4 mr-1"></i> Detail
                                    </button>
                                    <button
                                        wire:click="syncStatus('{{ $transaction->order_id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="syncStatus('{{ $transaction->order_id }}')"
                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                        <i class="fa-solid fa-sync w-4 h-4 mr-1" wire:loading.class="animate-spin" wire:target="syncStatus('{{ $transaction->order_id }}')"></i> Sync
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-file-invoice-dollar w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>Tidak ada transaksi ditemukan untuk item ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- Modal Detail Transaksi --}}
    @if($showModal && $selectedTransaction)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
             <div class="fixed inset-0 bg-gray-500 bg-opacity-20 transition-opacity backdrop-blur-sm" aria-hidden="true" wire:click="closeModal"></div>

             <div class="flex items-center justify-center min-h-screen p-4 w-xl m-auto">
                 <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full mx-auto
                              max-h-[90vh] transform transition-all"
                              @click.away="closeModal">

                     {{-- Tombol close --}}
                     <button wire:click="closeModal"
                         class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200
                                 bg-gray-100 hover:bg-gray-200 rounded-full p-1">
                         <i class="fa-solid fa-xmark w-5 h-5"></i>
                     </button>

                     <div class="p-6">
                         <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                             <i class="fa-solid fa-file-invoice-dollar w-5 h-5 mr-2"></i>
                             Detail Transaksi
                         </h3>
                         <p class="text-sm text-gray-500 mb-6">Order ID: {{ $selectedTransaction->order_id }}</p>

                         {{-- Form --}}
                         <div class="space-y-4 overflow-y-auto max-h-[70vh] pr-2">
                             
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Status</span>
                                 <span class="text-sm font-semibold col-span-2">
                                     @if ($selectedTransaction->isSuccess())
                                         <span class="text-green-600">Success (Settlement)</span>
                                     @elseif ($selectedTransaction->isPending())
                                         <span class="text-yellow-600">Pending</span>
                                     @elseif ($selectedTransaction->isFailed())
                                         <span class="text-red-600">{{ ucfirst($selectedTransaction->status) }}</span>
                                     @else
                                         <span class="text-gray-700">{{ ucfirst($selectedTransaction->status) }}</span>
                                     @endif
                                 </span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">User</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->user->name ?? 'N/A' }}</span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Item</span>
                                 <span class="text-sm text-gray-900 col-span-2">
                                     {{ $selectedTransaction->item->title ?? 'N/A' }}
                                     ({{ $selectedTransaction->tryout_id ? 'Tryout' : 'Bundle' }})
                                 </span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Total Bayar</span>
                                 <span class="text-sm text-gray-900 col-span-2 font-bold">{{ $selectedTransaction->formatted_amount }}</span>
                             </div>

                             <hr class="my-4">

                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Payment Method</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->payment_method ?? '-' }}</span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Payment Type</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->payment_type ?? '-' }}</span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Midtrans ID</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->transaction_id ?? '-' }}</span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Fraud Status</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->fraud_status ?? '-' }}</span>
                             </div>

                             <hr class="my-4">

                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Waktu Transaksi</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->transaction_time ? $selectedTransaction->transaction_time->isoFormat('D MMM YYYY, HH:mm:ss') : '-' }}</span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Waktu Selesai</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->settlement_time ? $selectedTransaction->settlement_time->isoFormat('D MMM YYYY, HH:mm:ss') : '-' }}</span>
                             </div>
                             <div class="grid grid-cols-3 gap-x-4">
                                 <span class="text-sm font-medium text-gray-500 col-span-1">Waktu Expired</span>
                                 <span class="text-sm text-gray-900 col-span-2">{{ $selectedTransaction->expired_at ? $selectedTransaction->expired_at->isoFormat('D MMM YYYY, HH:mm:ss') : '-' }}</span>
                             </div>
                             
                             {{-- Metadata --}}
                             <div class="mt-4">
                                 <label class="text-sm font-medium text-gray-500 mb-1">Metadata (JSON)</label>
                                 <pre class="bg-gray-100 p-3 rounded-lg text-xs text-gray-700 overflow-auto max-h-40">@json($selectedTransaction->metadata, JSON_PRETTY_PRINT)</pre>
                             </div>

                         </div>

                         {{-- Actions --}}
                         <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
                             <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                                 <i class="fa-solid fa-xmark w-4 h-4 mr-1"></i> Tutup
                             </button>
                         </div>
                     </div>
                 </div>
             </div>
        </div>
    @endif
</div>