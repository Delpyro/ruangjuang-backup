<?php

namespace App\Livewire\Admin\Tryouts;

use Livewire\Component;
use App\Models\Tryout;
use Illuminate\Support\Str;

class TryoutsCreate extends Component
{
    // ✨ BARU: Tambahkan $category dengan default 'umum'
    public $title, $slug, $category = 'umum', $is_hots = false, $duration, $content, $quote, $price, $discount, $is_active = true;
    
    // ✨ BARU: Properti untuk tanggal diskon
    public $discount_start_date, $discount_end_date;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:tryouts,slug',
        'category' => 'required|in:umum,khusus', // ✨ BARU: Validasi kategori
        'is_hots' => 'boolean',
        'duration' => 'nullable|integer|min:1',
        'content' => 'required|string',
        'quote' => 'nullable|string',
        'price' => 'required|integer|min:0',
        'discount' => 'nullable|integer|min:0|max:' . PHP_INT_MAX, 
        'is_active' => 'boolean',
        // ✨ BARU: Rules validasi untuk tanggal
        'discount_start_date' => 'nullable|date',
        // end_date harus setelah atau sama dengan start_date (jika keduanya diisi)
        'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date', 
    ];

    protected $messages = [
        'discount.max' => 'Diskon tidak boleh melebihi harga tryout',
        // ✨ BARU: Pesan validasi untuk tanggal
        'discount_end_date.after_or_equal' => 'Tanggal berakhir diskon harus setelah atau sama dengan tanggal mulai diskon.',
    ];

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedDiscount($value)
    {
        // Validasi agar diskon tidak melebihi harga
        if ($value > $this->price) {
            $this->addError('discount', 'Diskon tidak boleh melebihi harga tryout');
        } else {
            $this->resetErrorBag('discount');
        }
    }

    public function save()
    {
        // Validasi tambahan untuk diskon
        if ($this->discount > $this->price) {
            $this->addError('discount', 'Diskon tidak boleh melebihi harga tryout');
            return;
        }

        $this->validate();

        Tryout::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category, // ✨ BARU: Simpan kategori
            'is_hots' => $this->is_hots,
            'duration' => $this->duration,
            'content' => $this->content,
            'quote' => $this->quote,
            'price' => $this->price,
            'discount' => $this->discount,
            // ✨ BARU: Simpan tanggal diskon
            'discount_start_date' => $this->discount_start_date,
            'discount_end_date' => $this->discount_end_date,
            // End BARU
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Tryout berhasil ditambahkan.');
        return redirect()->route('admin.tryouts.index');
    }

    // Computed property untuk harga akhir
    public function getFinalPriceProperty()
    {
        if ($this->price && $this->discount) {
            return max(0, $this->price - $this->discount);
        }
        return $this->price;
    }

    public function render()
    {
        return view('livewire.admin.tryouts.tryouts-create')->layout('layouts.admin');
    }
}