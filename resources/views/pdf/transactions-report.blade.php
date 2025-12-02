<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Ruang Juang</title>
    <style>
        /* A. Pengaturan Dasar */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif; /* Font modern sans-serif */
            font-size: 10px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* B. Header Megah (Corporate Look) */
        .header {
            padding: 10px 0;
            margin-bottom: 25px;
        }
        .header-top {
            display: block; 
            border-bottom: 3px solid #0056b3; 
            padding-bottom: 5px;
        }
        .header h1 {
            font-size: 22px;
            margin: 0;
            color: #0056b3; 
            text-transform: uppercase;
            font-weight: 800;
        }
        .header h3 {
            font-size: 12px;
            margin: 5px 0 0 0;
            color: #555;
            font-weight: 300;
        }

        /* C. Metadata Laporan & Filter */
        .metadata-section {
            margin-bottom: 25px;
            padding: 10px;
            background-color: #f7f7f7; 
            border-left: 5px solid #007bff; 
        }
        .metadata-section table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .metadata-section td {
            padding: 2px 0;
            border: none;
            width: 50%;
        }
        .metadata-section strong {
            font-weight: bold;
            color: #333;
            width: 150px;
            display: inline-block;
        }
        .metadata-title {
            font-size: 12px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 5px;
        }

        /* D. Tabel Data Utama (Tabel Bersih Modern) */
        .table-container {
            margin-top: 15px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            border: 1px solid #ddd;
        }
        .data-table th, .data-table td {
            border: 1px solid #e9e9e9;
            padding: 10px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #0056b3; 
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9.5px;
        }
        .data-table tr:nth-child(even) { 
            background-color: #fcfcfc; 
        } 
        .amount-col {
            font-weight: 700;
            text-align: right;
            color: #000;
        }

        /* E. Badge Status */
        .status-badge {
            font-weight: bold;
            padding: 4px 7px;
            border-radius: 15px; 
            font-size: 8px;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }
        .status-settlement { color: #ffffff; background-color: #28a745; } /* Menggunakan status model: settlement */
        .status-pending { color: #ffffff; background-color: #ffc107; } /* pending */
        .status-expire, .status-deny, .status-cancel { color: #ffffff; background-color: #dc3545; } /* expired, deny, cancel */
        .status-other { color: #ffffff; background-color: #6c757d; } /* Default/lainnya */

    </style>
</head>
<body>

    <div class="header">
        <div class="header-top">
            <h1>RUANG JUANG</h1>
        </div>
        <h3>LAPORAN REKAPITULASI KEUANGAN TRANSAKSI</h3>
    </div>

    <div class="metadata-section">
        <div class="metadata-title">DETAIL LAPORAN</div>
        <table>
            <tr>
                <td><strong>Item Dilaporkan</strong></td>
                <td>: 
                    @if ($itemTitle ?? false) 
                        {{ strtoupper($itemTitle) }} 
                        ({{ ucfirst($itemType ?? '-') }})
                    @else
                        SEMUA ITEM
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Status Transaksi</strong></td>
                <td>: 
                    @if ($filterStatus == 'all') SEMUA STATUS @else {{ strtoupper($filterStatus) }} @endif
                </td>
            </tr>
            <tr>
                <td><strong>Periode Data</strong></td>
                <td>: 
                    @if ($filterMonth == 'all') Seluruh Periode Tersedia @else {{ \Carbon\Carbon::createFromFormat('Y-m', $filterMonth)->isoFormat('MMMM YYYY') }} @endif
                </td>
            </tr>
            <tr>
                <td><strong>Total Record</strong></td>
                <td>: {{ $transactions->count() }} Data Transaksi</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: Administrator Sistem</td>
            </tr>
            <tr>
                <td><strong>Tanggal Dokumen</strong></td>
                <td>: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</td>
            </tr>
        </table>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Order ID</th>
                    <th width="15%">Nama Pembeli</th>
                    <th width="25%">Produk Tryout / Bundle</th>
                    <th width="15%" class="amount-col">Total Bayar</th>
                    <th width="10%">Status</th>
                    <th width="15%">Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $index => $transaction)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            {{ $transaction->order_id }} 
                            <br><small style="color: #6c757d;">({{ $transaction->created_at->isoFormat('DD-MM-YY') }})</small>
                        </td>
                        <td>{{ $transaction->user->name ?? 'User Dihapus' }}</td>
                        <td>
                            {{-- Menggunakan accessor item->title dari model --}}
                            {{ $transaction->item->title ?? 'N/A' }} 
                            <br><small style="color: #007bff; font-weight: 500;">[{{ $transaction->tryout_id ? 'Tryout' : 'Bundle' }}]</small>
                        </td>
                        <td class="amount-col">{{ $transaction->formatted_amount }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($transaction->status) }}">
                                {{ strtoupper($transaction->status) }}
                            </span>
                        </td>
                        <td>{{ $transaction->payment_method ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 25px; color: #777; font-style: italic; background-color: #fff;">
                            *** Tidak ada data transaksi yang sesuai kriteria laporan ini. ***
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
</body>
</html>