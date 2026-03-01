<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 mt-20">
        <div class="container mx-auto px-4">

            <!-- Header Halaman -->
            <div class="text-center mb-10" data-aos="fade-up">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3 leading-tight">
                    Pengaturan Profil
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kelola informasi akun, kata sandi, dan pengaturan pribadi Anda.
                </p>
            </div>

            <!-- Wrapper Konten -->
            <div class="max-w-4xl mx-auto space-y-8" data-aos="fade-up" data-aos-delay="100">

                <!-- Kartu 1: Informasi Profil -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-5 md:p-8">
                        <!-- Header Kartu -->
                        <header>
                            <h2 class="text-2xl font-bold text-gray-900">
                                Informasi Profil
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Perbarui nama dan alamat email akun Anda.
                            </p>
                        </header>

                        <!-- Form (Komponen Livewire) -->
                        <div class="mt-6 max-w-xl">
                            {{-- 
                            CATATAN: Pastikan Anda telah meng-CSS tombol 'Save' 
                            di dalam komponen ini agar sesuai (misal: bg-blue-600)
                            --}}
                            <livewire:profile.update-profile-information-form />
                        </div>
                    </div>
                </div>

                <!-- Kartu 2: Ubah Kata Sandi -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-5 md:p-8">
                        <!-- Header Kartu -->
                        <header>
                            <h2 class="text-2xl font-bold text-gray-900">
                                Ubah Kata Sandi
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
                            </p>
                        </header>

                        <!-- Form (Komponen Livewire) -->
                        <div class="mt-6 max-w-xl">
                            <livewire:profile.update-password-form />
                        </div>
                    </div>
                </div>

                <!-- Kartu 3: Hapus Akun (Zona Berbahaya) -->
                <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden">
                    <!-- Header Kartu dengan aksen merah -->
                    <div class="p-5 md:p-8 bg-red-50 border-b border-red-200">
                        <header>
                            <h2 class="text-2xl font-bold text-red-900">
                                Hapus Akun
                            </h2>
                            <p class="mt-1 text-sm text-red-700">
                                Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.
                            </p>
                        </header>
                    </div>
                    
                    <!-- Tombol Hapus (Komponen Livewire) -->
                    <div class="p-5 md:p-8 pt-6">
                        <div class="max-w-xl">
                            {{-- 
                            CATATAN: Pastikan Anda telah meng-CSS tombol 'Delete Account' 
                            di dalam komponen ini agar sesuai (misal: bg-red-600)
                            --}}
                            <livewire:profile.delete-user-form />
                        </div>
                    </div>
                </div>

            </div> <!-- Akhir max-w-4xl -->

        </div> <!-- Akhir container -->
    </div> <!-- Akhir min-h-screen -->
</x-app-layout>
