<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Desa - Kelurahan Losari</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root {
            --losari-blue: #0f2f5f;
            --losari-blue-soft: #dbeafe;
            --losari-orange: #f97316;
            --losari-sand: #fff7ed;
            --losari-bg: #f5f8fc;
        }

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

        .card-reveal {
            animation: revealUp 0.45s ease-out;
        }

        .title-strong {
            color: #0b1f3a;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="text-gray-800">

    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50 border-b border-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl sm:text-2xl font-bold text-orange-600 hover:opacity-90 transition">
                <img src="{{ route('public.asset', ['path' => 'logo_losari.png']) }}" alt="Logo Losari" class="h-9 sm:h-10 w-auto object-contain">
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
                    <a href="#informasi" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Informasi Desa</a>
                    <a href="{{ route('kalender-desa') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Kalender Kelurahan Losari</a>
                </div>
            </details>
        </div>
    </nav>

    <header class="relative text-white py-14 sm:py-16 px-4 sm:px-6 text-center">
        <div class="relative max-w-4xl mx-auto">
            <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight text-white">Informasi Kelurahan Losari</h1>
            <p class="text-blue-100 mt-4 text-sm sm:text-base">
                Ringkasan profil, layanan, kegiatan, dan pembaruan wilayah yang disusun agar mudah dipahami seluruh warga.
            </p>
        </div>
    </header>

    <main id="informasi" class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <div class="grid grid-cols-1 gap-8 sm:gap-10">
            @forelse ($sections as $section)
                <article class="card-reveal group relative bg-slate-50 rounded-3xl shadow-[0_12px_40px_rgba(15,47,95,0.12)] hover:shadow-[0_20px_50px_rgba(15,47,95,0.18)] transition-all duration-300 overflow-hidden border border-slate-200/70">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-orange-400 via-amber-400 to-orange-500"></div>
                    <div class="flex flex-col lg:flex-row min-h-[320px]">
                        @if (!empty($section->image_data) || !empty($section->image_path))
                            <div class="w-full lg:w-2/5 min-h-[240px] lg:min-h-[320px] overflow-hidden bg-slate-100">
                                <img src="{{ $section->image_data ?: \Illuminate\Support\Facades\Storage::disk(config('filesystems.media', 'public'))->url($section->image_path) }}" alt="{{ $section->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @endif

                        <div class="p-7 sm:p-9 lg:w-3/5 flex flex-col justify-start">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-700/80 mb-3">Informasi Resmi</p>
                            <h2 class="title-strong text-2xl sm:text-3xl font-extrabold mb-4 leading-tight">{{ $section->title }}</h2>
                            <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                                {{ $section->description }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="bg-white border border-orange-100 rounded-2xl p-8 text-center shadow-sm">
                    <h2 class="title-strong text-xl font-bold mb-2">Informasi Belum Tersedia</h2>
                    <p class="text-gray-600">Data informasi desa akan ditampilkan setelah admin melakukan pembaruan konten.</p>
                </div>
            @endforelse
        </div>
    </main>

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
