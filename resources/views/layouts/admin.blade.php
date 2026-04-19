<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- Pengecekan Title Berdasarkan Role --}}
    <title>
        @if(auth()->check() && auth()->user()->role === 'admin')
            Admin
        @elseif(auth()->check() && auth()->user()->role === 'owner')
            Owner
        @else
            Panel
        @endif 
        - {{ config('app.name', 'Laravel') }}
    </title>

    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased h-screen overflow-hidden">

<div class="flex h-screen">
    <aside class="w-64 bg-white shadow-lg flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-gray-200">
            {{-- Pengecekan Judul Sidebar Berdasarkan Role --}}
            <h1 class="text-xl font-bold text-gray-800">
                @if(auth()->check() && auth()->user()->role === 'admin')
                    Dashboard Admin
                @elseif(auth()->check() && auth()->user()->role === 'owner')
                    Dashboard Owner
                @endif
            </h1>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            
            {{-- ========================================== --}}
            {{-- NAVIGASI UNTUK ADMIN                       --}}
            {{-- ========================================== --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-home w-5 h-5 mr-3"></i> Dashboard
                </a>
                
                <a href="{{ route('admin.transactions.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-money-bill-wave w-5 h-5 mr-3"></i> Transaksi
                </a>
                
                <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.users') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-users w-5 h-5 mr-3"></i> Users
                </a>

                <a href="{{ route('admin.assign-tryout') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.assign-tryout') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-user-plus w-5 h-5 mr-3"></i> Assign Tryout
                </a>
                
                <a href="{{ route('admin.bundles.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.bundles.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-box w-5 h-5 mr-3"></i> Bundles
                </a>
                
                <a href="{{ route('admin.tryouts.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.tryouts.*') ? 'bg-blue-100 text-blue-700' : '' }}">
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

                <a href="{{ route('admin.promo.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.promo.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-tags w-5 h-5 mr-3"></i> Promo Terlaris
                </a>

            {{-- ========================================== --}}
            {{-- NAVIGASI UNTUK OWNER                       --}}
            {{-- ========================================== --}}
            @elseif(auth()->check() && auth()->user()->role === 'owner')
                <a href="{{ route('owner.dashboard') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-home w-5 h-5 mr-3"></i> Dashboard
                </a>
                
                <a href="{{ route('owner.transactions.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.transactions.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-money-bill-wave w-5 h-5 mr-3"></i> Transaksi
                </a>
                
                <a href="{{ route('owner.users') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.users') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-users w-5 h-5 mr-3"></i> Users
                </a>

                <a href="{{ route('owner.assign-tryout') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.assign-tryout') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-user-plus w-5 h-5 mr-3"></i> Assign Tryout
                </a>
                
                <a href="{{ route('owner.bundles.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.bundles.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-box w-5 h-5 mr-3"></i> Bundles
                </a>
                
                <a href="{{ route('owner.tryouts.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.tryouts.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-edit w-5 h-5 mr-3"></i> Tryouts
                </a>
                
                <a href="{{ route('owner.question-categories') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.question-categories') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-layer-group w-5 h-5 mr-3"></i> Question Categories
                </a>
                
                <a href="{{ route('owner.question-sub-categories') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.question-sub-categories') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-list w-5 h-5 mr-3"></i> Question Sub-Categories
                </a>

                <a href="{{ route('owner.reviews.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.reviews.index') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-star w-5 h-5 mr-3"></i> Reviews
                </a>

                <a href="{{ route('owner.promo.index') }}" class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('owner.promo.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i class="fas fa-tags w-5 h-5 mr-3"></i> Promo Terlaris
                </a>
            @endif

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
        <header class="h-16 bg-white shadow flex items-center justify-between px-6 shrink-0">
            <h2 class="text-lg font-semibold text-gray-800">
                @yield('title')
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Welcome,</span>
                <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gray-100">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts') 

<script src="https://cdn.tiny.cloud/1/upiv2b2lhxi2vmy9xrik2ul1sabxcosdq2ixjyaij7g8dc5q/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

{{-- SWEETALERT2 SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{!! session('success') !!}',
                showConfirmButton: false,
                timer: 2500,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{!! session('error') !!}',
            });
        @endif
    });
</script>

<script>
    // Pengecekan URL Upload Gambar Berdasarkan Role
    @if(auth()->check() && auth()->user()->role === 'admin')
        window.uploadImageUrl = "{{ route('admin.tinymce.upload.image') }}"; 
    @elseif(auth()->check() && auth()->user()->role === 'owner')
        window.uploadImageUrl = "{{ route('owner.tinymce.upload.image') }}";
    @else
        window.uploadImageUrl = "";
    @endif

    function initTinyMCE(selector, callback) {
        tinymce.init({
            selector: selector,
            plugins: 'anchor autolink charmap codesample emoticons image media link lists searchreplace visualblocks wordcount code fullscreen', 
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | fullscreen | code',
            height: 300,
            automatic_uploads: true,
            images_reuse_filename: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    const formData = new FormData();
                    const url = window.uploadImageUrl || ''; 

                    if (!url) {
                        reject('URL upload gambar tidak terdefinisi atau Anda tidak memiliki akses.');
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
                            return response.json().then(error => reject('Upload gagal: ' + (error.error || response.statusText)));
                        }
                        return response.json();
                    })
                    .then(json => {
                        if (json && json.location) {
                            resolve(json.location); 
                        } else {
                            reject('Upload gagal: Respon server tidak mengandung URL.');
                        }
                    })
                    .catch(error => {
                        reject('Terjadi kesalahan jaringan: ' + error.message);
                    });
                });
            },
            setup: function(editor) {
                callback(editor);
            }
        });
    }
</script>
</body>
</html>