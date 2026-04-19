<?php

namespace App\Livewire\Admin\Tryouts;

use Livewire\Component;
use App\Models\Tryout;
use Illuminate\Support\Str;

class TryoutsCreate extends Component
{
    public $title, $slug, $category = 'umum', $is_hots = false, $duration, $content, $quote, $price, $discount = 0, $is_active = true;
    public $discount_start_date, $discount_end_date;

    // ✨ FITUR BARU: Dynamic Route Prefix
    public function getRolePrefixProperty()
    {
        return auth()->user()->role; // Output: 'admin' atau 'owner'
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:tryouts,slug',
        'category' => 'required|in:umum,khusus', 
        'is_hots' => 'boolean',
        'duration' => 'nullable|integer|min:1',
        'content' => 'required|string',
        'quote' => 'nullable|string',
        'price' => 'required|integer|min:0',
        'discount' => 'nullable|integer|min:0|max:' . PHP_INT_MAX, 
        'is_active' => 'boolean',
        'discount_start_date' => 'nullable|date',
        'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date', 
    ];

    protected $messages = [
        'title.required' => 'Judul tryout wajib diisi.',
        'slug.required' => 'Slug wajib diisi.',
        'slug.unique' => 'Slug sudah digunakan, coba judul lain.',
        'content.required' => 'Konten tryout wajib diisi.',
        'price.required' => 'Harga wajib diisi.',
        'discount.max' => 'Diskon tidak boleh melebihi harga tryout',
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

        try {
            Tryout::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'category' => $this->category,
                'is_hots' => $this->is_hots,
                'duration' => $this->duration,
                'content' => $this->content,
                'quote' => $this->quote,
                'price' => $this->price,
                'discount' => $this->discount ?? 0,
                'discount_start_date' => $this->discount_start_date,
                'discount_end_date' => $this->discount_end_date,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Tryout berhasil ditambahkan.');
            
            // ✨ DYNAMIC REDIRECT ✨
            return redirect()->route($this->rolePrefix . '.tryouts.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan tryout: ' . $e->getMessage());
        }
    }

    // Computed property untuk harga akhir
    public function getFinalPriceProperty()
    {
        if ($this->price && $this->discount) {
            return max(0, $this->price - $this->discount);
        }
        return $this->price ?? 0;
    }

    public function render()
    {
        return view('livewire.admin.tryouts.tryouts-create')->layout('layouts.admin');
    }
}