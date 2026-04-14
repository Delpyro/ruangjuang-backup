<?php

namespace App\Livewire\Owner\Tryouts; // Namespace Owner

use Livewire\Component;
use App\Models\Tryout;
use Illuminate\Support\Str;

class TryoutsCreate extends Component
{
    public $title, $slug, $category = 'umum', $is_hots = false, $duration, $content, $quote, $price, $discount, $is_active = true;
    public $discount_start_date, $discount_end_date;

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
        'discount.max' => 'Diskon tidak boleh melebihi harga tryout',
        'discount_end_date.after_or_equal' => 'Tanggal berakhir diskon harus setelah atau sama dengan tanggal mulai diskon.',
    ];

    public function updatedTitle($value) { $this->slug = Str::slug($value); }

    public function updatedDiscount($value) {
        if ($value > $this->price) { $this->addError('discount', 'Diskon tidak boleh melebihi harga tryout'); } 
        else { $this->resetErrorBag('discount'); }
    }

    public function save() {
        if ($this->discount > $this->price) {
            $this->addError('discount', 'Diskon tidak boleh melebihi harga tryout');
            return;
        }

        $this->validate();

        Tryout::create([
            'title' => $this->title, 'slug' => $this->slug, 'category' => $this->category, 
            'is_hots' => $this->is_hots, 'duration' => $this->duration, 'content' => $this->content, 
            'quote' => $this->quote, 'price' => $this->price, 'discount' => $this->discount, 
            'discount_start_date' => $this->discount_start_date, 'discount_end_date' => $this->discount_end_date,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Tryout berhasil ditambahkan.');
        return redirect()->route('owner.tryouts.index'); // REDIRECT OWNER
    }

    public function getFinalPriceProperty() {
        return ($this->price && $this->discount) ? max(0, $this->price - $this->discount) : $this->price;
    }

    public function render() {
        // LAYOUT DAN VIEW OWNER
        return view('livewire.owner.tryouts.tryouts-create')->layout('layouts.owner');
    }
}