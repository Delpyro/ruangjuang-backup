<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles
</head>
{{-- [!code ++] TAMBAHKAN 'h-screen overflow-hidden' DI SINI --}}
<body class="bg-gray-100 font-sans antialiased h-screen overflow-hidden">

<div class="flex h-screen">
    <aside class="w-64 bg-white shadow-lg flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">Dashboard Admin</h1>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto"> {{-- Tambahkan overflow-y-auto di sini jika menu sidebar panjang --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-home w-5 h-5 mr-3"></i> Dashboard
            </a>
            
            <a href="{{ route('admin.transactions.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-money-bill-wave w-5 h-5 mr-3"></i> Transaksi
            </a>
            
            <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.users') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-users w-5 h-5 mr-3"></i> Users
            </a>

            <a href="{{ route('admin.bundles.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.bundles.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-box w-5 h-5 mr-3"></i> Bundles
            </a>
            
            <a href="{{ route('admin.tryouts.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.tryouts') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-edit w-5 h-5 mr-3"></i> Tryouts
            </a>
            
            <a href="{{ route('admin.question-categories') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.question-categories') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-layer-group w-5 h-5 mr-3"></i> Question Categories
            </a>
            
            <a href="{{ route('admin.question-sub-categories') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.question-sub-categories') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-list w-5 h-5 mr-3"></i> Question Sub-Categories
            </a>

            <a href="{{ route('admin.reviews.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.reviews.index') ? 'bg-blue-100 text-blue-700' : '' }}">
                <i class="fas fa-star w-5 h-5 mr-3"></i> Reviews
            </a>
        </nav>
        <div class="p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-3 rounded-lg text-red-600 hover:bg-red-100 transition-colors duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white shadow flex items-center justify-between px-6 shrink-0"> {{-- Tambahkan shrink-0 --}}
            <h2 class="text-lg font-semibold text-gray-800">
                @yield('title')
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Welcome,</span>
                <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
            </div>
        </header>

        {{-- Area Konten Utama (Scrollable) --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gray-100">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle w-5 h-5 mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle w-5 h-5 mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts') 

<script src="https://cdn.tiny.cloud/1/qmkvntzionu0428xa04wcfzfwxsir87e0h0bc2vrdptzppk7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    // ?? PERBAIKAN UTAMA: Mendefinisikan URL upload gambar dari route name Laravel
    window.uploadImageUrl = "{{ route('admin.tinymce.upload.image') }}"; 

    // FUNGSI INITIATOR TINYMCE GLOBAL (TERMASUK LOGIC UPLOAD GAMBAR)
    function initTinyMCE(selector, callback) {
        tinymce.init({
            selector: selector,
            // AKTIFKAN 'image' dan 'media'
            plugins: 'anchor autolink charmap codesample emoticons image media link lists searchreplace visualblocks wordcount code fullscreen', 
            // TAMBAHKAN tombol 'image' dan 'media'
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | fullscreen | code',
            height: 300,

            // PENGATURAN UPLOAD GAMBAR
            automatic_uploads: true,
            images_reuse_filename: true,
            
            // FUNGSI HANDLER UPLOAD GAMBAR KUSTOM
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    const formData = new FormData();
                    // Mengambil URL dari variabel global yang sudah didefinisikan di atas
                    const url = window.uploadImageUrl || ''; 

                    if (!url) {
                        // Error ini seharusnya tidak lagi muncul
                        reject('URL upload gambar tidak terdefinisi. Pastikan route tinymce.upload.image sudah ada.');
                        return;
                    }

                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Mencoba membaca error dari response JSON server
                            return response.json().then(error => reject('Upload gagal: ' + (error.error || response.statusText)));
                        }
                        return response.json();
                    })
                    .then(json => {
                        if (json && json.location) {
                            resolve(json.location); // Mengembalikan URL publik
                        } else {
                            reject('Upload gagal: Respon server tidak mengandung URL.');
                        }
                    })
                    .catch(error => {
                        reject('Terjadi kesalahan jaringan: ' + error.message);
                    });
                });
            },

            // Panggil callback Livewire
            setup: function(editor) {
                callback(editor);
            }
        });
    }
</script>
</body>
</html>