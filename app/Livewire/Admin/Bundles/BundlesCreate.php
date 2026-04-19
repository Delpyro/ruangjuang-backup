<?php

namespace App\Livewire\Admin\Bundles;

use Livewire\Component;
use App\Models\Bundle;
use App\Models\Tryout;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BundlesCreate extends Component
{
    public $title;
    public $slug;
    public $description;
    public $price = 0;
    public $discount = 0;
    public $is_active = true;
    public $expired_at;
    
    public $selected_tryout_ids = []; 
    public $search = '';
    public $selectAll = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:bundles,slug',
        'description' => 'nullable|string',
        'price' => 'required|integer|min:0',
        'discount' => 'nullable|integer|min:0|lte:price', 
        'is_active' => 'boolean',
        'expired_at' => 'nullable|date|after:today',
        'selected_tryout_ids' => 'required|array|min:1',
        'selected_tryout_ids.*' => 'exists:tryouts,id',
    ];

    protected $messages = [
        'title.required' => 'Judul bundle wajib diisi.',
        'slug.required' => 'Slug wajib diisi.',
        'slug.unique' => 'Slug sudah digunakan, coba judul lain.',
        'price.required' => 'Harga bundle wajib diisi.',
        'price.min' => 'Harga tidak boleh negatif.',
        'selected_tryout_ids.required' => 'Bundle harus menyertakan minimal satu Tryout.',
        'selected_tryout_ids.min' => 'Bundle harus menyertakan minimal satu Tryout.',
        'discount.lte' => 'Diskon tidak boleh melebihi harga Bundle.',
        'expired_at.after' => 'Tanggal kedaluwarsa harus setelah hari ini.',
    ];

    // ✨ FITUR BARU: Dynamic Route Prefix
    public function getRolePrefixProperty()
    {
        return auth()->user()->role; // Output: 'admin' atau 'owner'
    }

    public function loadTryouts()
    {
        $query = Tryout::active();
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
        return $query->select('id', 'title', 'price')->get();
    }

    public function updatedSearch() {}

    public function updatedTitle($value)
    {
        if (!empty($value)) {
            $this->slug = Str::slug($value);
        }
    }
    
    public function updatedExpiredAt()
    {
        $this->validateOnly('expired_at');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_tryout_ids = $this->loadTryouts()->pluck('id')->toArray();
        } else {
            $this->selected_tryout_ids = [];
        }
    }

    public function updatedSelectedTryoutIds()
    {
        $allTryoutsCount = $this->loadTryouts()->count();
        $selectedCount = count($this->selected_tryout_ids);
        $this->selectAll = $selectedCount === $allTryoutsCount && $allTryoutsCount > 0;
    }

    public function updatedDiscount()
    {
        $this->validateOnly('discount');
    }

    public function updatedPrice()
    {
        $this->validateOnly('price');
        $this->validateOnly('discount');
    }

    public function store()
    {
        $this->validate();

        try {
            $bundle = Bundle::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'price' => $this->price,
                'discount' => $this->discount ?? 0, 
                'is_active' => $this->is_active,
                'expired_at' => $this->expired_at ? Carbon::parse($this->expired_at) : null,
            ]);
            
            $bundle->tryouts()->attach($this->selected_tryout_ids);

            session()->flash('success', 'Bundle baru berhasil dibuat!');
            
            // ✨ DYNAMIC REDIRECT ✨
            return redirect()->route($this->rolePrefix . '.bundles.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan bundle: ' . $e->getMessage());
        }
    }

    public function getFinalPriceProperty() { return max(0, $this->price - $this->discount); }
    public function getTotalIndividualPriceProperty() { return empty($this->selected_tryout_ids) ? 0 : Tryout::whereIn('id', $this->selected_tryout_ids)->sum('price'); }
    public function getSavingsPercentageProperty() {
        $totalIndividual = $this->totalIndividualPrice; 
        return ($totalIndividual > 0 && $this->finalPrice > 0) ? round((($totalIndividual - $this->finalPrice) / $totalIndividual) * 100, 2) : 0;
    }
    public function getSelectedTryoutsCountProperty() { return count($this->selected_tryout_ids); }
    public function getHasSavingsProperty() { return $this->totalIndividualPrice > $this->finalPrice; }
    public function getFormattedExpiredAtProperty() { return $this->expired_at ? Carbon::parse($this->expired_at)->format('Y-m-d\TH:i') : null; }

    public function render()
    {
        $tryouts = $this->loadTryouts();
        return view('livewire.admin.bundles.bundles-create', [
            'tryouts' => $tryouts,
        ])->layout('layouts.admin');
    }
}