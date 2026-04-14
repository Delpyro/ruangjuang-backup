<?php

namespace App\Livewire\Admin\Bundles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bundle;

class BundlesManage extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $status = ''; 
    
    public $showTrashed = false; 

    protected $queryString = ['search', 'status', 'showTrashed'];

    // Reset pagination saat ada perubahan pada filter
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedShowTrashed(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); } // ✨ BARU: Mencegah bug dropdown perPage

    public function render()
    {
        $query = Bundle::query()
            ->withCount('tryouts')
            ->orderBy('created_at', 'asc');

        // Logika untuk menampilkan data terhapus atau aktif
        if ($this->showTrashed) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status !== '') {
            $query->where('is_active', (bool)$this->status);
        }

        return view('livewire.admin.bundles.bundles-manage', [
            'bundles' => $query->paginate($this->perPage),
        ])->layout('layouts.admin');
    }

    // ✨ PERBAIKAN: Ubah nama jadi softDeleteBundle agar konsisten dengan Tryout
    public function softDeleteBundle($id): void
    {
        $bundle = Bundle::findOrFail($id);
        $bundle->delete(); 
        session()->flash('success', 'Bundle "' . $bundle->title . '" berhasil di-Soft Delete.');
    }

    // Fitur Restore (Admin diizinkan me-restore data)
    public function restoreBundle($id): void
    {
        $bundle = Bundle::withTrashed()->findOrFail($id);
        $bundle->restore();
        session()->flash('success', 'Bundle "' . $bundle->title . '" berhasil dipulihkan.');
    }
}