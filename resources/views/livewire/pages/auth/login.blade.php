<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['vite_assets' => ['resources/css/auth/login.css', 'resources/js/auth/login.js']])] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="py-4">
    <h2 class="text-4xl font-bold text-primary-dark mb-2">Selamat Datang</h2>
    <p class="text-gray-600 mb-10">Silakan masuk ke akun RuangJuang Anda</p>
    
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <form wire:submit="login" class="space-y-6">
        <div class="space-y-2">
            <label class="block text-gray-700 font-medium" for="email">
                <i class="fas fa-envelope text-primary-light mr-2"></i>Email
            </label>
            <div class="relative">
                <input 
                    wire:model="form.email"
                    id="email" 
                    type="email" 
                    placeholder="Masukkan alamat email" 
                    {{-- Hapus padding kiri (pl-10) --}}
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                    required
                    autofocus
                    autocomplete="username"
                >
                {{-- Hapus elemen <i> ikon --}}
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>
        
        <div class="space-y-2">
            <div class="flex justify-between">
                <label class="block text-gray-700 font-medium" for="password">
                    <i class="fas fa-lock text-primary-light mr-2"></i>Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-primary-light hover:text-primary-dark transition duration-300" wire:navigate>
                        Lupa password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input 
                    wire:model="form.password"
                    id="password" 
                    type="password" 
                    placeholder="Masukkan kata sandi" 
                    {{-- Hapus padding kiri (pl-10) --}}
                    class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                    required
                    autocomplete="current-password"
                >
                {{-- Hapus elemen <i> ikon --}}
                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>
        
        <div class="flex items-center">
            <input 
                wire:model="form.remember"
                id="remember" 
                type="checkbox" 
                class="h-4 w-4 text-primary-light focus:ring-primary-light border-gray-300 rounded"
                name="remember"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-primary-light hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center"
        >
            <i class="fas fa-sign-in-alt mr-2"></i> Masuk
        </button>
    </form>
    
    <div class="mt-8 text-center">
        <p class="text-gray-600">Belum punya akun? 
            <a href="{{ route('register') }}" class="text-primary-light hover:text-primary-dark font-medium transition duration-300" wire:navigate>
                Daftar sekarang
            </a>
        </p>
    </div>
</div>