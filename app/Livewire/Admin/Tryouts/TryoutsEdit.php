<?php

namespace App\Livewire\Admin\Tryouts;

use Livewire\Component;
use App\Models\Tryout;
use Illuminate\Support\Str;

class TryoutsEdit extends Component
{
    public $tryout;
    public $title, $slug, $category, $is_hots = false, $duration, $content, $quote, $price, $discount, $is_active = true;
    public $discount_start_date, $discount_end_date;

    // ✨ FITUR BARU: Dynamic Route Prefix
    public function getRolePrefixProperty()
    {
        return auth()->user()->role; // Output: 'admin' atau 'owner'
    }

    public function mount($id)
    {
        $this->tryout = Tryout::findOrFail($id);
        
        $this->title = $this->tryout->title;
        $this->slug = $this->tryout->slug;
        $this->category = $this->tryout->category;
        $this->is_hots = $this->tryout->is_hots;
        $this->duration = $this->tryout->duration;
        $this->content = $this->tryout->content;
        $this->quote = $this->tryout->quote;
        $this->price = $this->tryout->price;
        $this->discount = $this->tryout->discount;
        $this->is_active = $this->tryout->is_active;

        $this->discount_start_date = $this->tryout->discount_start_date 
            ? $this->tryout->discount_start_date->format('Y-m-d') 
            : null;
        $this->discount_end_date = $this->tryout->discount_end_date 
            ? $this->tryout->discount_end_date->format('Y-m-d') 
            : null;
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:tryouts,slug,', // Akan di-update di method update()
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
        'discount.max' => 'Diskon tidak boleh melebihi harga tryout',
        'discount_end_date.after_or_equal' => 'Tanggal berakhir diskon harus setelah atau sama dengan tanggal mulai diskon.',
    ];

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedDiscount($value)
    {
        if ($value > $this->price) {
            $this->addError('discount', 'Diskon tidak boleh melebihi harga tryout');
        } else {
            $this->resetErrorBag('discount');
        }
    }

    public function update()
    {
        // Update unique rule dengan ID tryout yang sedang diedit
        $this->rules['slug'] = 'required|string|max:255|unique:tryouts,slug,' . $this->tryout->id;

        if ($this->discount > $this->price) {
            $this->addError('discount', 'Diskon tidak boleh melebihi harga tryout');
            return;
        }

        $this->validate();

        try {
            $this->tryout->update([
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

            session()->flash('success', 'Tryout berhasil diperbarui.');
            
            // ✨ DYNAMIC REDIRECT ✨
            return redirect()->route($this->rolePrefix . '.tryouts.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memperbarui tryout: ' . $e->getMessage());
        }
    }

    public function getFinalPriceProperty()
    {
        if ($this->price && $this->discount) {
            return max(0, $this->price - $this->discount);
        }
        return $this->price ?? 0;
    }

    public function render()
    {
        return view('livewire.admin.tryouts.tryouts-edit')->layout('layouts.admin');
    }
}