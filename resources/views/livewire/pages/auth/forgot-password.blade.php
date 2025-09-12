<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['vite_assets' => ['resources/css/auth/reset-password.css', 'resources/js/auth/reset-password.js']])] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class=" p-12 bg-white/80">
    <div class="py-4">
        <h2 class="text-4xl font-bold text-primary-dark mb-2">Lupa Password</h2>
        <p class="text-gray-600 mb-10">Masukkan alamat email Anda untuk menerima link reset password.</p>
        
        <x-auth-session-status class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200" :status="session('status')" />
        
        <form wire:submit="sendPasswordResetLink" class="space-y-6">
            <div class="space-y-2">
                <label class="block text-gray-700 font-medium" for="email">
                    <i class="fas fa-envelope text-primary-light mr-2"></i>Email
                </label>
                <div class="relative">
                    <input 
                        wire:model="email"
                        id="email" 
                        type="email" 
                        placeholder="Masukkan alamat email" 
                        {{-- Hapus padding kiri (pl-10) --}}
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                        required
                        autofocus
                    >
                    {{-- Hapus elemen <i> ikon --}}
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-primary-light hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center"
            >
                <i class="fas fa-paper-plane mr-2"></i> Kirim Link Reset Password
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <p class="text-gray-600">
                <a href="{{ route('login') }}" class="text-primary-light hover:text-primary-dark font-medium transition duration-300" wire:navigate><i class="fas fa-arrow-left mr-2"></i> Kembali ke halaman Login</a>
            </p>
        </div>
    </div>
</div>