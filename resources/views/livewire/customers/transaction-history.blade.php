<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 mt-20">
<div class="container mx-auto px-4">

    <!-- Header Halaman -->
    <div class="text-center mb-10" data-aos="fade-up">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3 leading-tight">
            History Transaksi Anda
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Berikut adalah riwayat semua pembelian Anda di Ruang Juang.
        </p>
    </div>

    <!-- Konten Utama: Tabel Transaksi -->
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
        
        <!-- Wrapper untuk mobile responsive -->
        <div class="overflow-x-auto">
            
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Item
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <!-- [BARU] Tambahkan kolom Aksi -->
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse ($transactions as $transaction)
                        <tr wire:key="{{ $transaction->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $transaction->order_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <!-- Menggunakan accessor 'item' dari model -->
                                {{ $transaction->item->title ?? 'Item tidak diketahui' }}
                                <span class="text-xs text-gray-500 block">
                                    ({{ $transaction->id_tryout ? 'Tryout' : 'Bundle' }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">
                                <!-- Menggunakan accessor 'formatted_amount' dari model -->
                                {{ $transaction->formatted_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <!-- Menggunakan helper 'isSuccess', 'isPending', 'isFailed' dari model -->
                                @if ($transaction->isSuccess())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1.5 mt-0.5"></i> Sukses
                                    </span>
                                @elseif ($transaction->isPending())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-hourglass-half mr-1.5 mt-0.5"></i> Pending
                                    </span>
                                @elseif ($transaction->isFailed())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1.5 mt-0.5"></i> Gagal/Batal
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $transaction->status_label }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- [BARU] Kolom Tombol Invoice -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('payment.finish', ['order_id' => $transaction->order_id]) }}" 
                                   class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                                   target="_blank"> <!-- Buka di tab baru -->
                                   <i class="fas fa-file-invoice mr-1.5 mt-0.5"></i> Invoice
                                </a> 
                            </td>
                        </tr>
                    @empty
                        <!-- Tampilan jika tidak ada transaksi -->
                        <!-- [PERBAIKAN] Colspan diubah dari 5 menjadi 6 -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box-open text-4xl mb-3 text-gray-400"></i>
                                    <p class="font-medium">Anda belum memiliki riwayat transaksi.</p>
                                    <p class="text-sm mt-1">Silakan lakukan pembelian tryout atau bundle terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        @if ($transactions->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif

    </div>

</div>
</div>