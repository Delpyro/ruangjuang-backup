<?php

namespace App\Livewire\Admin\Bundles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bundle;

class BundlesManage extends Component
{
    use WithPagination;

    // Properti untuk Pencarian dan Filter
    public $search = '';
    public $perPage = 10;
    public $status = ''; // Filter berdasarkan status aktif/nonaktif

    // Reset halaman ketika ada perubahan pada properti pencarian
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Hapus Bundle
     */
    public function deleteBundle($id): void
    {
        $bundle = Bundle::findOrFail($id);
        
        // Sebelum menghapus bundle, relasi pivot akan otomatis terhapus
        // karena kita menggunakan onDelete('cascade') pada migrasi bundle_tryout.
        $bundle->delete(); 
        
        session()->flash('success', 'Bundle "' . $bundle->title . '" berhasil dihapus (soft deleted).');
    }

    public function render()
    {
        $query = Bundle::query()
            ->withCount('tryouts') // Hitung jumlah tryout di setiap bundle
            // PERUBAHAN: Menggunakan asc (ascending) agar data terlama/tertua menjadi Nomor 1.
            ->orderBy('created_at', 'asc');

        // Logic Pencarian
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%');
        }

        // Logic Filter Status
        if ($this->status !== '') {
            $query->where('is_active', (bool)$this->status);
        }

        $bundles = $query->paginate($this->perPage);

        return view('livewire.admin.bundles.bundles-manage', [
            'bundles' => $bundles,
        ])->layout('layouts.admin');
    }
}