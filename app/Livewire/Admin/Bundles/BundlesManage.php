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
    public function updatedPerPage(): void { $this->resetPage(); }

    // ✨ FITUR BARU: Dynamic Route Prefix
    public function getRolePrefixProperty()
    {
        return auth()->user()->role; // Output: 'admin' atau 'owner'
    }

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

    // --- METHOD SOFT DELETE (Return Array) ---
    public function softDeleteBundle($id)
    {
        try {
            $bundle = Bundle::findOrFail($id);
            $bundle->delete(); 
            return ['status' => 'success', 'message' => 'Bundle "' . $bundle->title . '" berhasil di-soft delete.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus bundle: ' . $e->getMessage()];
        }
    }

    // --- METHOD RESTORE (Return Array) ---
    public function restoreBundle($id)
    {
        try {
            $bundle = Bundle::withTrashed()->findOrFail($id);
            $bundle->restore();
            return ['status' => 'success', 'message' => 'Bundle "' . $bundle->title . '" berhasil direstore dan aktif kembali.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal merestore bundle: ' . $e->getMessage()];
        }
    } 

    // --- METHOD FORCE DELETE (Return Array & Proteksi Backend) ---
    public function forceDeleteBundle($id)
    {
        if (auth()->user()->role !== 'owner') {
            return ['status' => 'error', 'message' => 'Akses ditolak. Hanya owner yang dapat menghapus permanen.'];
        }

        try {
            $bundle = Bundle::withTrashed()->findOrFail($id);
            $bundle->forceDelete();
            return ['status' => 'success', 'message' => 'Bundle "' . $bundle->title . '" berhasil dihapus permanen.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus permanen: ' . $e->getMessage()];
        }
    }
}