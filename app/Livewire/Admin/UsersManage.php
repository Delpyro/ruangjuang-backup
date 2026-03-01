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

    // Menentukan tema pagination menjadi Tailwind CSS
    protected $paginationTheme = 'tailwind';

    public $name, $slug, $email, $phone_number, $role = 'user', $status = 'active', $is_active = true, $image, $password;
    public $userId;
    public $isEdit = false;
    public $showModal = false;
    public $confirmingDeletion = false;
    public $userToDelete;
    
    // Tambahkan untuk konfirmasi delete permanen
    public $confirmingForceDelete = false;
    public $userToForceDelete;
    public $currentImage;

    // Untuk search
    public $search = '';
    
    // Property untuk error message
    public $errorMessage = '';

    // Tambahkan property untuk showTrashed
    public $showTrashed = false;

    protected $queryString = ['search'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user',
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
            // Filter sesuai tab yang dipilih
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed(); // Hanya tampilkan yang terhapus (tab Terhapus)
            }, function ($query) {
                $query->whereNull('deleted_at'); // Hanya tampilkan yang TIDAK terhapus (tab Aktif)
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
        $this->reset(['name', 'slug', 'email', 'phone_number', 'role', 'status', 'is_active', 'password', 'image', 'userId', 'isEdit', 'currentImage', 'errorMessage']);
        $this->role = 'user';
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
            'role' => $this->role,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'password' => Hash::make($this->password),
            'image' => $this->image ? $this->image->store('users', 'public') : null,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Menggunakan withTrashed() agar bisa mengedit user yang soft delete
        $user = User::withTrashed()->findOrFail($id); 
        $this->userId = $id;
        $this->name = $user->name;
        $this->slug = $user->slug;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->role = $user->role;
        $this->status = $user->status;
        $this->is_active = $user->is_active;
        $this->currentImage = $user->image;
    }

    public function update()
    {
        $this->validate();

        // Menggunakan withTrashed() agar bisa mengupdate user yang soft delete
        $user = User::withTrashed()->findOrFail($this->userId);

        $slug = Str::slug($this->name);
        
        $updateData = [
            'name' => $this->name,
            'slug' => $slug,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role' => $this->role,
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
    }

    public function confirmDelete($id)
    {
        $this->errorMessage = '';
        $this->confirmingDeletion = true;
        $this->userToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->userToDelete = null;
        $this->errorMessage = '';
    }

    public function delete()
    {
        try {
            $user = User::findOrFail($this->userToDelete);
            $user->delete();
            
            $this->confirmingDeletion = false;
            $this->userToDelete = null;
            session()->flash('success', 'User berhasil dihapus (soft delete).');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // Method untuk konfirmasi force delete
    public function confirmForceDelete($id)
    {
        $this->errorMessage = '';
        $this->confirmingForceDelete = true;
        $this->userToForceDelete = $id;
    }

    public function cancelForceDelete()
    {
        $this->confirmingForceDelete = false;
        $this->userToForceDelete = null;
        $this->errorMessage = '';
    }

    public function forceDelete()
    {
        try {
            $user = User::withTrashed()->findOrFail($this->userToForceDelete);
            
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            
            $user->forceDelete();
            
            $this->confirmingForceDelete = false;
            $this->userToForceDelete = null;
            session()->flash('success', 'User berhasil dihapus permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            User::withTrashed()->findOrFail($id)->restore();
            session()->flash('success', 'User berhasil direstore.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal merestore user: ' . $e->getMessage());
        }
    }
}