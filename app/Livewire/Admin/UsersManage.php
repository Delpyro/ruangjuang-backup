<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UsersManage extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $name, $slug, $email, $phone_number, $status = 'active', $is_active = true, $image, $password;
    
    // Role di-hardcode ke 'user' karena Admin tidak boleh membuat/mengubah role jadi Admin
    public $role = 'user'; 

    public $userId;
    public $isEdit = false;
    public $showModal = false;
    
    public $currentImage;
    public $search = '';
    public $errorMessage = '';
    public $showTrashed = false;

    protected $queryString = ['search'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'status' => 'required|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:1024',
        ];

        if ($this->isEdit) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->userId;
            $rules['password'] = 'nullable|min:6';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::withTrashed()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed(); 
            }, function ($query) {
                $query->whereNull('deleted_at'); 
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.users-manage', [
            'users' => $users,
        ])->layout('layouts.admin');
    }

    public function openModal($edit = false, $id = null)
    {
        $this->resetForm();
        $this->isEdit = $edit;

        if ($edit && $id) {
            $this->edit($id);
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['image']);
    }

    public function resetForm()
    {
        $this->reset(['name', 'slug', 'email', 'phone_number', 'status', 'is_active', 'password', 'image', 'userId', 'isEdit', 'currentImage', 'errorMessage']);
        $this->role = 'user'; // Paksa role default
        $this->status = 'active';
        $this->is_active = true;
    }

    public function create()
    {
        $this->validate();

        $slug = Str::slug($this->name);

        User::create([
            'name' => $this->name,
            'slug' => $slug,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role' => 'user', // Memastikan bahwa role hanya bisa 'user'
            'status' => $this->status,
            'is_active' => $this->is_active,
            'password' => Hash::make($this->password),
            'image' => $this->image ? $this->image->store('users', 'public') : null,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'User berhasil ditambahkan.');
        return $this->redirect(request()->header('Referer')); // Reload agar SweetAlert Layout muncul
    }

    public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id); 
        $this->userId = $id;
        $this->name = $user->name;
        $this->slug = $user->slug;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        // Role TIDAK di-load karena admin tidak boleh edit role
        $this->status = $user->status;
        $this->is_active = $user->is_active;
        $this->currentImage = $user->image;
    }

    public function update()
    {
        $this->validate();

        $user = User::withTrashed()->findOrFail($this->userId);
        $slug = Str::slug($this->name);
        
        $updateData = [
            'name' => $this->name,
            'slug' => $slug,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            // Role dihilangkan dari updateData agar tidak terganti
            'status' => $this->status,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        if ($this->image) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $updateData['image'] = $this->image->store('users', 'public');
        }

        $user->update($updateData);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'User berhasil diperbarui.');
        return $this->redirect(request()->header('Referer')); // Reload agar SweetAlert Layout muncul
    }

    // --- METHOD SOFT DELETE (Return Array untuk SweetAlert) ---
    public function softDelete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete(); 
            return ['status' => 'success', 'message' => 'User berhasil di-soft delete dan dipindah ke tab Terhapus.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus user: ' . $e->getMessage()];
        }
    }

    // --- METHOD RESTORE (Return Array untuk SweetAlert) ---
    public function restore($id)
    {
        try {
            User::withTrashed()->findOrFail($id)->restore();
            return ['status' => 'success', 'message' => 'User berhasil direstore dan aktif kembali.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal merestore user: ' . $e->getMessage()];
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            
            // Hapus gambar fisik dari storage jika ada
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            
            $user->forceDelete();
            return ['status' => 'success', 'message' => 'User beserta datanya berhasil dihapus secara PERMANEN.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus permanen: ' . $e->getMessage()];
        }
    }
}