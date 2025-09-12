<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['vite_assets' => ['resources/css/auth/new-password.css', 'resources/js/auth/new-password.js']])] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="py-4">
    <h2 class="text-4xl font-bold text-primary-dark mb-2">Reset Password</h2>
    <p class="text-gray-600 mb-10">Masukkan kata sandi baru untuk akun Anda.</p>

    <x-auth-session-status class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200" :status="session('status')" />

    <form wire:submit="resetPassword" class="space-y-6">
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
                    autocomplete="username"
                />
                {{-- Hapus elemen <i> ikon --}}
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
        </div>

        <div class="space-y-2">
            <label class="block text-gray-700 font-medium" for="password">
                <i class="fas fa-lock text-primary-light mr-2"></i>Password
            </label>
            <div class="relative">
                <input 
                    wire:model="password"
                    id="password" 
                    type="password" 
                    placeholder="Buat kata sandi baru"
                    {{-- Hapus padding kiri (pl-10) --}}
                    class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                    required
                    autocomplete="new-password" 
                />
                {{-- Hapus elemen <i> ikon --}}
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
        </div>

        <div class="space-y-2">
            <label class="block text-gray-700 font-medium" for="password_confirmation">
                <i class="fas fa-lock text-primary-light mr-2"></i>Konfirmasi Password
            </label>
            <div class="relative">
                <input 
                    wire:model="password_confirmation"
                    id="password_confirmation" 
                    type="password" 
                    placeholder="Ketik ulang kata sandi baru" 
                    {{-- Hapus padding kiri (pl-10) --}}
                    class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                    required
                    autocomplete="new-password"
                />
                {{-- Hapus elemen <i> ikon --}}
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm" />
        </div>

        <button 
            type="submit" 
            class="w-full bg-primary-light hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center mt-6"
        >
            <i class="fas fa-key mr-2"></i> Reset Password
        </button>
    </form>
</div>