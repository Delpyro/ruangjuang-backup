<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Tryout;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class TryoutPage extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; // all, hots, regular
    // ✨ BARU: Properti untuk filter kategori (umum/khusus)
    public $category = 'all'; // all, umum, khusus
    public $sort = 'latest'; // latest, price_asc, price_desc

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'filter' => ['except' => 'all', 'as' => 'f'],
        'category' => ['except' => 'all', 'as' => 'c'], // ✨ BARU: Query string kategori
        'sort' => ['except' => 'latest', 'as' => 's'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    // ✨ BARU: Fungsi update kategori
    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function setFilter($value)
    {
        $this->filter = $value;
        $this->resetPage();
    }

    // ✨ BARU: Fungsi untuk tombol set kategori
    public function setCategory($value)
    {
        $this->category = $value;
        $this->resetPage();
    }

    public function setSort($value)
    {
        $this->sort = $value;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filter = 'all';
        $this->category = 'all'; // ✨ BARU: Reset kategori
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id();

        $tryouts = Tryout::active()
            ->withCount(['activeQuestions as active_questions_count'])
            
            // --- PERBAIKAN KRITIS: Menggunakan 'id_user' di whereDoesntHave ---
            // Hanya tampilkan tryout yang BELUM dibeli oleh user ini
            ->whereDoesntHave('purchasers', function ($query) use ($userId) {
                // Kolom ini harus 'id_user' karena relasi di Model Tryout menggunakan 'id_user'
                $query->where('id_user', $userId);
            })
            // ------------------------------------------------------------------

            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter === 'hots', function ($query) {
                $query->hots();
            })
            ->when($this->filter === 'regular', function ($query) {
                $query->where('is_hots', false);
            })
            // ✨ BARU: Query filter kategori
            ->when($this->category !== 'all', function ($query) {
                $query->where('category', $this->category);
            })
            ->when($this->sort === 'latest', function ($query) {
                $query->latest();
            })
            ->when($this->sort === 'price_asc', function ($query) {
                // Urutkan berdasarkan harga final (dengan mempertimbangkan diskon aktif)
                $query->orderByRaw('(CASE 
                    WHEN discount > 0 
                        AND (discount_start_date IS NULL OR discount_start_date <= NOW())
                        AND (discount_end_date IS NULL OR discount_end_date >= NOW())
                    THEN price - discount 
                    ELSE price 
                END) ASC');
            })
            ->when($this->sort === 'price_desc', function ($query) {
                // Urutkan berdasarkan harga final (dengan mempertimbangkan diskon aktif)
                $query->orderByRaw('(CASE 
                    WHEN discount > 0 
                        AND (discount_start_date IS NULL OR discount_start_date <= NOW())
                        AND (discount_end_date IS NULL OR discount_end_date >= NOW())
                    THEN price - discount 
                    ELSE price 
                END) DESC');
            })
            ->paginate(12);

        return view('livewire.customers.tryout-page', compact('tryouts'))
            ->layout('layouts.app');
    }
    
}