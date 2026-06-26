<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Kelurahan Losari</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: linear-gradient(180deg, #0a2344 0%, #1a4d8f 52%, #8ec5ff 100%);
            min-height: 100vh;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="text-gray-800">

    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50 border-b border-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl sm:text-2xl font-bold text-orange-600 hover:opacity-90 transition">
                <img src="{{ asset('logo_losari.png') }}" alt="Logo Losari" class="h-9 sm:h-10 w-auto object-contain">
                <span>Website Kelurahan Losari</span>
            </a>

            <details class="relative">
                <summary class="list-none cursor-pointer inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition select-none">
                    <span>Menu</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0l-4.25-4.51a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                    </svg>
                </summary>

                <div class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg p-2">
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Beranda</a>
                    <a href="{{ route('informasi-desa') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Informasi Desa</a>
                    <a href="{{ route('kalender-desa') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Kalender Kelurahan Losari</a>
                </div>
            </details>
        </div>
    </nav>

    <header id="beranda" class="relative text-white py-12 sm:py-16 md:py-20 px-4 sm:px-6 text-center">
        <div class="w-full mx-auto">
            <h1 class="text-3xl sm:text-5xl font-extrabold mb-3 leading-tight max-w-4xl mx-auto">Selamat Datang di Kelurahan Losari</h1>
            <p class="text-base sm:text-xl text-blue-200 mb-10 max-w-2xl mx-auto">Temukan informasi profil kelurahan, layanan publik, dan potensi wilayah Losari secara transparan.</p>

            <div class="w-full max-w-5xl mx-auto mb-12 px-2 sm:px-4"> 
                <div class="relative w-full overflow-hidden rounded-3xl border-4 border-white/10 shadow-2xl bg-black">
                    <video
                        class="w-full aspect-video object-cover block"
                        src="{{ $homeVideoUrl }}"
                        autoplay
                        loop
                        muted
                        playsinline
                        preload="auto"
                    ></video>
                </div>
            </div>

            <a href="{{ route('informasi-desa') }}" class="bg-white text-blue-900 px-8 py-3.5 rounded-full font-bold shadow-lg hover:bg-orange-500 hover:text-white transition-all text-base inline-block">
                Informasi Kelurahan Losari
            </a>
        </div>
    </header>

    <footer id="kontak" class="bg-gray-900 text-gray-400 py-5 text-center border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-3">
                <h2 class="text-white font-bold text-lg mb-1">Website Resmi Kelurahan Losari</h2>
                <p class="text-sm">Jl. Suropati No. 157 Singosari</p>
            </div>
            <div class="h-px w-full bg-gray-800 mb-3"></div>
            <p class="text-sm">&copy; 2026 Website Kelurahan Losari.</p>
        </div>
    </footer>

</body>
</html>