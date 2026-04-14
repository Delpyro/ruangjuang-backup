<?php

namespace App\Livewire\Owner\Bundles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bundle;

class BundlesManage extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $status = ''; 
    
    // ✨ BARU: Tambahkan properti showTrashed agar view tidak error
    public $showTrashed = false; 

    // ✨ BARU: Tambahkan showTrashed ke queryString
    protected $queryString = ['search', 'status', 'showTrashed'];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    
    // ✨ BARU: Reset page saat pindah tab Aktif / Terhapus
    public function updatedShowTrashed(): void { $this->resetPage(); }

    public function render()
    {
        $query = Bundle::query()
            ->withCount('tryouts')
            ->orderBy('created_at', 'asc');

        // ✨ BARU: Logika untuk menampilkan data terhapus atau aktif
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

        return view('livewire.owner.bundles.bundles-manage', [
            'bundles' => $query->paginate($this->perPage),
        ])->layout('layouts.owner');
    }

    public function deleteBundle($id): void
    {
        $bundle = Bundle::findOrFail($id);
        $bundle->delete(); 
        session()->flash('success', 'Bundle "' . $bundle->title . '" berhasil diarsipkan (soft deleted).');
    }

    // Fitur Restore untuk Owner
    public function restoreBundle($id): void
    {
        $bundle = Bundle::withTrashed()->findOrFail($id);
        $bundle->restore();
        session()->flash('success', 'Bundle "' . $bundle->title . '" berhasil dipulihkan.');
    }

    // Fitur Hapus Permanen untuk Owner
    public function forceDeleteBundle($id): void
    {
        $bundle = Bundle::withTrashed()->findOrFail($id);
        $bundle->forceDelete();
        session()->flash('success', 'Bundle "' . $bundle->title . '" berhasil dihapus permanen.');
    }
}