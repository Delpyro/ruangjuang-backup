<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['vite_assets' => ['resources/css/auth/register.css', 'resources/js/auth/register.js']])] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Generate slug dari name
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>
<div class="py-4">
    <h2 class="text-3xl font-bold text-primary-dark mb-1">Buat Akun</h2>
    <p class="text-sm text-gray-600 mb-6">Daftarkan diri Anda untuk memulai perjalanan belajar</p>
    
    <form wire:submit="register" class="space-y-4">
        <div class="space-y-1">
            <label class="block text-gray-700 font-medium text-sm" for="name">
                {{-- Perbaikan: Ubah 'mr-1' menjadi 'mr-2' --}}
                <i class="fas fa-user text-primary-light mr-2"></i>Nama Lengkap
            </label>
            <div class="relative">
                <input 
                    wire:model="name"
                    id="name" 
                    type="text" 
                    placeholder="Nama Lengkap Anda" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300 text-sm"
                    required
                    autofocus
                    autocomplete="name"
                >
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm" />
        </div>

        <div class="space-y-1">
            <label class="block text-gray-700 font-medium text-sm" for="email">
                {{-- Perbaikan: Ubah 'mr-1' menjadi 'mr-2' --}}
                <i class="fas fa-envelope text-primary-light mr-2"></i>Email
            </label>
            <div class="relative">
                <input 
                    wire:model="email"
                    id="email" 
                    type="email" 
                    placeholder="Alamat email Anda" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300 text-sm"
                    required
                    autocomplete="username"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
        </div>

        <div class="space-y-1">
            <label class="block text-gray-700 font-medium text-sm" for="phone_number">
                {{-- Perbaikan: Ubah 'mr-1' menjadi 'mr-2' --}}
                <i class="fas fa-phone text-primary-light mr-2"></i>Nomor HP
            </label>
            <div class="relative">
                <input 
                    wire:model="phone_number"
                    id="phone_number" 
                    type="tel" 
                    placeholder="Nomor HP Anda" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300 text-sm"
                    autocomplete="tel"
                >
            </div>
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2 text-sm" />
        </div>
        
        <div class="space-y-1">
            <label class="block text-gray-700 font-medium text-sm" for="password">
                {{-- Perbaikan: Ubah 'mr-1' menjadi 'mr-2' --}}
                <i class="fas fa-lock text-primary-light mr-2"></i>Password
            </label>
            <div class="relative">
                <input 
                    wire:model="password"
                    id="password" 
                    type="password" 
                    placeholder="Buat kata sandi" 
                    class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300 text-sm"
                    required
                    autocomplete="new-password"
                >
                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
        </div>

        <div class="space-y-1">
            <label class="block text-gray-700 font-medium text-sm" for="password_confirmation">
                {{-- Perbaikan: Ubah 'mr-1' menjadi 'mr-2' --}}
                <i class="fas fa-lock text-primary-light mr-2"></i>Konfirmasi Password
            </label>
            <div class="relative">
                <input 
                    wire:model="password_confirmation"
                    id="password_confirmation" 
                    type="password" 
                    placeholder="Ketik ulang kata sandi" 
                    class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300 text-sm"
                    required
                    autocomplete="new-password"
                >
                <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm" />
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-primary-light hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center text-base mt-6"
        >
            <i class="fas fa-user-plus mr-2"></i> Daftar
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-gray-600 text-sm">Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-primary-light hover:text-primary-dark font-medium transition duration-300" wire:navigate>
                Masuk di sini
            </a>
        </p>
    </div>
</div>