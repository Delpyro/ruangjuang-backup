<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Promo;

class Dashboard extends Component
{
    public function render()
    {
        // Ambil data promo yang sudah di-set oleh admin (Maksimal 3 sesuai limitasi)
        $promos = Promo::with('promoable')->get();

        // Format diskon untuk masing-masing item (Tryout/Bundle)
        foreach ($promos as $promo) {
            $item = $promo->promoable;
            
            if ($item && $item->discount > 0) {
                // Formatting diskon (jumlah penghematan)
                $item->formatted_discount = number_format($item->discount, 0, ',', '.');
                
                // Hitung Persentase Diskon: (discount / (price + discount)) * 100
                $originalPrice = $item->price + $item->discount;
                if ($originalPrice > 0) {
                    $item->discount_percentage = round(($item->discount / $originalPrice) * 100);
                } else {
                    $item->discount_percentage = 0;
                }
            } elseif ($item) {
                $item->formatted_discount = null;
                $item->discount_percentage = 0;
            }
        }

        // Melewatkan data ke view
        return view('livewire.customers.dashboard', [
            'isGuest' => Auth::guest(),
            'promos' => $promos, // Kirim variabel $promos, bukan lagi $tryouts
        ])->layout('layouts.app');
    }
}