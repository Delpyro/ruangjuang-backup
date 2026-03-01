<?php

namespace App\Livewire\Admin;

use App\Models\Bundle;
use App\Models\Promo;
use App\Models\Tryout;
use Livewire\Component;
use Livewire\WithPagination;

class PromoManage extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $type = ''; // 'tryout' atau 'bundle'
    public $itemId = ''; // ID dari tryout/bundle yang dipilih
    public $availableItems = []; // List opsi dropdown

    // Ketika tipe diubah, update list opsi item
    public function updatedType($value)
    {
        $this->itemId = ''; // Reset pilihan

        if ($value === 'tryout') {
            // Ambil tryout yang aktif dan belum ada di tabel promo
            $this->availableItems = Tryout::where('is_active', 1)
                ->whereDoesntHave('promo')
                ->get();
        } elseif ($value === 'bundle') {
            // Ambil bundle yang aktif dan belum ada di tabel promo
            $this->availableItems = Bundle::where('is_active', 1)
                ->whereDoesntHave('promo')
                ->get();
        } else {
            $this->availableItems = [];
        }
    }

    public function addToPromo()
    {
        // 1. Cek jumlah maksimal Promo
        $totalPromos = Promo::count();
        
        if ($totalPromos >= 3) {
            // Tampilkan error jika sudah 3
            $this->addError('general', 'Maksimal hanya 3 item yang dapat ditampilkan di Promo Terlaris.');
            return; // Hentikan eksekusi
        }

        // 2. Validasi input
        $this->validate([
            'type' => 'required|in:tryout,bundle',
            'itemId' => 'required|integer',
        ]);

        // 3. Proses Simpan
        $modelClass = $this->type === 'tryout' ? Tryout::class : Bundle::class;

        Promo::create([
            'promoable_type' => $modelClass,
            'promoable_id' => $this->itemId,
        ]);

        session()->flash('success', 'Berhasil ditambahkan ke daftar Promo Terlaris!');
        
        // Refresh form
        $this->reset(['type', 'itemId', 'availableItems']);
    }

    public function removeFromPromo($promoId)
    {
        Promo::findOrFail($promoId)->delete();
        session()->flash('success', 'Item dihapus dari daftar Promo.');
        
        // Opsional: Bersihkan error limit jika sebelumnya muncul
        $this->resetErrorBag('general');
    }

    public function render()
    {
        // Ambil data promo beserta relasinya (tryout/bundle)
        $promos = Promo::with('promoable')->latest()->paginate(10);
        $totalPromos = Promo::count(); // Ambil total untuk dipakai di view

        return view('livewire.admin.promo-manage', [
            'promos' => $promos,
            'totalPromos' => $totalPromos,
        ])->layout('layouts.admin');
    }
}