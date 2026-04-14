<?php

namespace App\Livewire\Owner;

use App\Models\Bundle;
use App\Models\Promo;
use App\Models\Tryout;
use Livewire\Component;
use Livewire\WithPagination;

class PromoManage extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $type = ''; 
    public $itemId = ''; 
    public $availableItems = []; 

    public function updatedType($value)
    {
        $this->itemId = ''; 
        if ($value === 'tryout') {
            $this->availableItems = Tryout::where('is_active', 1)->whereDoesntHave('promo')->get();
        } elseif ($value === 'bundle') {
            $this->availableItems = Bundle::where('is_active', 1)->whereDoesntHave('promo')->get();
        } else {
            $this->availableItems = [];
        }
    }

    public function addToPromo()
    {
        if (Promo::count() >= 3) {
            $this->addError('general', 'Maksimal hanya 3 item yang dapat ditampilkan di Promo Terlaris.');
            return;
        }

        $this->validate([
            'type' => 'required|in:tryout,bundle',
            'itemId' => 'required|integer',
        ]);

        $modelClass = $this->type === 'tryout' ? Tryout::class : Bundle::class;

        Promo::create([
            'promoable_type' => $modelClass,
            'promoable_id' => $this->itemId,
        ]);

        session()->flash('success', 'Berhasil ditambahkan ke daftar Promo Terlaris!');
        $this->reset(['type', 'itemId', 'availableItems']);
    }

    public function removeFromPromo($promoId)
    {
        Promo::findOrFail($promoId)->delete();
        return ['status' => 'success', 'message' => 'Item berhasil dihapus dari daftar Promo.'];
    }

    public function render()
    {
        return view('livewire.owner.promo-manage', [
            'promos' => Promo::with('promoable')->latest()->paginate(10),
            'totalPromos' => Promo::count(),
        ])->layout('layouts.owner'); // ✨ Layout Owner
    }
}