<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Admin - Losari' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="min-h-screen">


    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-blue-900 border-b border-blue-800 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white leading-tight">{{ $header ?? 'Halaman Admin Website Kelurahan Losari' }}</h1>
            <div class="flex flex-wrap items-center gap-3">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-xs sm:text-sm font-medium text-blue-100">Sesi Aktif: <strong class="text-white">{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="sm:ml-1">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-3 py-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.5 3.75A2.25 2.25 0 0 0 5.25 6v12A2.25 2.25 0 0 0 7.5 20.25h6A2.25 2.25 0 0 0 15.75 18V15a.75.75 0 0 0-1.5 0v3a.75.75 0 0 1-.75.75h-6a.75.75 0 0 1-.75-.75V6a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 .75.75v3a.75.75 0 0 0 1.5 0V6A2.25 2.25 0 0 0 13.5 3.75h-6Zm8.03 4.72a.75.75 0 0 0 0 1.06l1.72 1.72H9a.75.75 0 0 0 0 1.5h8.25l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 0 0-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-7xl mx-auto">
            {{-- Konten dashboard --}}

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
                <h2 class="sr-only">Menu Pengelolaan Admin</h2>


                <a href="{{ route('admin.kelola-informasi.manage') }}"
                         class="inline-flex w-full items-center gap-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-base sm:text-lg px-4 sm:px-6 py-4 rounded-2xl shadow-lg hover:shadow-xl ring-1 ring-orange-300/60 transition-all duration-300 text-left">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-white">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 6.75a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-1.5 0V7.5a.75.75 0 0 1 .75-.75Zm0 4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V12a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span>Kelola Informasi Desa</span>
                </a>

                <a href="{{ route('admin.kelola-kalender.manage') }}"
                         class="inline-flex w-full items-center gap-3 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white font-bold text-base sm:text-lg px-4 sm:px-6 py-4 rounded-2xl shadow-lg hover:shadow-xl ring-1 ring-blue-300/60 transition-all duration-300 text-left">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-white">
                            <path d="M6.75 2.25A.75.75 0 0 0 6 3v1.5H4.5A2.25 2.25 0 0 0 2.25 6.75v12A2.25 2.25 0 0 0 4.5 21h15a2.25 2.25 0 0 0 2.25-2.25v-12A2.25 2.25 0 0 0 19.5 4.5H18V3a.75.75 0 0 0-1.5 0v1.5h-9V3a.75.75 0 0 0-.75-.75Z" />
                            <path fill-rule="evenodd" d="M2.25 9.75h19.5v9a.75.75 0 0 1-.75.75H3a.75.75 0 0 1-.75-.75v-9Zm4.5 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span>Kelola Kalender Kelurahan</span>
                </a>

                <a href="{{ route('admin.kelola-beranda.manage') }}"
                         class="inline-flex w-full items-center gap-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold text-base sm:text-lg px-4 sm:px-6 py-4 rounded-2xl shadow-lg hover:shadow-xl ring-1 ring-emerald-300/60 transition-all duration-300 text-left">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-white">
                            <path d="M12 3.75a.75.75 0 0 1 .53.22l8.25 8.25a.75.75 0 1 1-1.06 1.06l-.97-.97V19.5A2.25 2.25 0 0 1 16.5 21.75h-9A2.25 2.25 0 0 1 5.25 19.5v-7.19l-.97.97a.75.75 0 0 1-1.06-1.06l8.25-8.25a.75.75 0 0 1 .53-.22Z" />
                        </svg>
                    </span>
                    <span>Kelola Halaman Beranda</span>
                </a>


            </div>

            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            @php
                // Data dari controller: $sections (collection/array KelolaInformasi)
            @endphp

            {{-- Konten informasi desa sengaja dikosongkan. (frame/tulisan dihapus sesuai permintaan) --}}
            @if (!empty($sections))
                {{-- Tidak menampilkan editor karena konten memang dikosongkan. --}}
            @endif





        </div>
    </main>
</div>

</body>
</html>

