<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
    
    {{-- TARUH DI SINI: Tanpa @tailwind directives --}}
    <style>
        /* =========================================
        GLOBAL TINYMCE FIX (Mencegah reset Tailwind)
        ========================================= */
        .tinymce-content p { margin: 0 !important; }

        .tinymce-content ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5em !important;
            margin-bottom: 0.5em !important;
        }

        .tinymce-content ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5em !important;
            margin-bottom: 0.5em !important;
        }

        .tinymce-content ul ul { list-style-type: circle !important; margin: 0 !important; }
        .tinymce-content ul ul ul { list-style-type: square !important; }
        .tinymce-content li { margin-bottom: 0.25em !important; display: list-item !important; }

        /* FIX TABLE */
        .tinymce-content table {
            border-collapse: collapse !important;
            width: 100% !important;
            margin-top: 1em !important;
            margin-bottom: 1em !important;
        }
        .tinymce-content table, .tinymce-content th, .tinymce-content td {
            border: 1px solid #d1d5db !important;
        }
        .tinymce-content th, .tinymce-content td {
            padding: 0.5rem 0.75rem !important;
        }
        .tinymce-content th {
            background-color: #f3f4f6 !important;
            font-weight: 600 !important;
        }

        /* FIX HEADINGS, BLOCKQUOTE, LINKS */
        .tinymce-content h1 { font-size: 2em !important; font-weight: bold !important; margin-bottom: 0.5em !important; }
        .tinymce-content h2 { font-size: 1.5em !important; font-weight: bold !important; margin-bottom: 0.5em !important; }
        .tinymce-content blockquote { border-left: 4px solid #e5e7eb !important; padding-left: 1rem !important; font-style: italic !important; }
        .tinymce-content a { color: #2563eb !important; text-decoration: underline !important; }
    </style>

    {{-- Stack dipindah ke bawah style global --}}
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased h-full overflow-hidden">

    {{ $slot }} 
    
    @livewireScripts
    @stack('scripts')
</body>
</html>