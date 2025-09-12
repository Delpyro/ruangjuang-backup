<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        @vite($vite_assets ?? ['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-poppins min-h-screen flex items-center justify-center p-4 bg-gradient-animated">
        <div class="fixed -top-20 -left-20 w-60 h-60 rounded-full bg-primary-light/10 animate-pulse-slow"></div>
        <div class="fixed bottom-10 right-10 w-40 h-40 rounded-full bg-accent/10 animate-bounce-slow"></div>
        <div class="fixed top-1/3 left-1/4 w-28 h-28 rounded-full bg-primary-light/20 animate-float"></div>
        
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl flex flex-col md:flex-row w-full max-w-5xl overflow-hidden border border-white relative z-10">
            <div class="bg-gradient-to-br from-primary-dark to-primary-light text-white md:w-2/5 p-12 relative overflow-hidden">
                <div class="absolute inset-0 bg-pattern opacity-20"></div>
                
                <div class="absolute -top-20 -left-20 w-40 h-40 rounded-full bg-accent/30 animate-pulse-slow"></div>
                <div class="absolute -bottom-20 -right-20 w-40 h-40 rounded-full bg-accent/30 animate-pulse-slow animation-delay-1000"></div>
                <div class="absolute top-1/2 -right-10 w-28 h-28 rounded-full bg-white/20 animate-float"></div>
                
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo RuangJuang" {{ $attributes->merge(['class' => 'h-20 object-contain animate-float']) }}>
                    </div>
                    <p class="text-xl mb-8 text-white/90 text-center">Tempat terbaik untuk mencapai impian dan tujuan Anda</p>
                    
                    <div class="space-y-4 mt-8">
                        <div class="flex items-center space-x-2 opacity-90">
                            <div class="w-10 h-10 rounded-full bg-accent/80 flex items-center justify-center animate-pulse">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                            <p>Platform belajar terintegrasi</p>
                        </div>
                        <div class="flex items-center space-x-2 opacity-90">
                            <div class="w-10 h-10 rounded-full bg-accent/80 flex items-center justify-center animate-pulse animation-delay-500">
                                <i class="fas fa-book-open text-white"></i>
                            </div>
                            <p>Akses materi tanpa batas</p>
                        </div>
                        <div class="flex items-center space-x-2 opacity-90">
                            <div class="w-10 h-10 rounded-full bg-accent/80 flex items-center justify-center animate-pulse animation-delay-1000">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <p>Komunitas pembelajaran</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="md:w-3/5 p-12 bg-white/80">
                <div class="py-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
        
        @livewireScripts
    </body>
</html>