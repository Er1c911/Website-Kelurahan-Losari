<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM - Kelurahan Losari</title>
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

        .card-show {
            animation: cardShow 0.45s ease-out;
        }

        @keyframes cardShow {
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
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-700' }}">Beranda</a>
                    <a href="{{ route('informasi-desa') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('informasi-desa') ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-700' }}">Informasi Kelurahan Losari</a>
                    <a href="{{ route('umkm') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('umkm') ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-700' }}">UMKM</a>
                    <a href="{{ route('potensi-kelurahan') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('potensi-kelurahan') ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-700' }}">Potensi Kelurahan</a>
                    <a href="{{ route('kalender-desa') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('kalender-desa') ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-700' }}">Kalender Kelurahan Losari</a>
                    <a href="{{ route('kontak') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('kontak') ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-700' }}">Kontak (Narahubung)</a>
                </div>
            </details>
        </div>
    </nav>

    <header class="relative text-white py-14 sm:py-16 px-4 sm:px-6 text-center">
        <div class="relative max-w-4xl mx-auto">
            <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight text-white">UMKM Kelurahan Losari</h1>
            <p class="text-blue-100 mt-4 text-sm sm:text-base">Informasi peluang usaha warga, produk unggulan, dan sektor ekonomi kreatif di Kelurahan Losari.</p>
        </div>
    </header>

    <main id="umkm" class="max-w-7xl mx-auto px-4 sm:px-6 pb-16">
        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
            @forelse ($umkms as $item)
                <article class="card-show bg-white rounded-2xl border border-slate-200 p-6 shadow-[0_12px_30px_rgba(15,47,95,0.12)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(15,47,95,0.22)]">
                    @php
                        $photoSource = ($item->photo_data || $item->photo_path)
                            ? route('umkm.image', ['umkm' => $item])
                            : null;

                        $menuImageSources = $item->menuImages
                            ->map(fn($m) => route('umkm.menu-image', ['menuImage' => $m]))
                            ->filter()
                            ->values();

                        $legacyMenuSource = ($item->menu_data || $item->menu_path)
                            ? route('umkm.legacy-menu-image', ['umkm' => $item])
                            : null;
                        if ($legacyMenuSource) {
                            $menuImageSources->prepend($legacyMenuSource);
                        }

                        // Backward-compatibility: when older data stores pricelist in photo field,
                        // move it to popup source and keep card clean.
                        if ($menuImageSources->isEmpty() && $photoSource) {
                            $menuImageSources = collect([$photoSource]);
                            $photoSource = null;
                        }
                    @endphp

                    @if ($photoSource)
                        <img src="{{ $photoSource }}" alt="{{ $item->name }}" class="w-full h-44 object-cover rounded-xl mb-4 border border-slate-200">
                    @endif

                    <h2 class="text-xl font-extrabold text-blue-900 mb-2">{{ $item->name }}</h2>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $item->description ?: 'Deskripsi belum tersedia.' }}</p>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ $item->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-3 py-2 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3 18.7-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                            </svg>
                            {{ $item->contact_name }}
                        </a>

                        @if ($menuImageSources->count() > 0)
                            <button
                                type="button"
                                onclick="openMenuGallery({{ json_encode($menuImageSources->toArray()) }}, '{{ e($item->name) }}')"
                                class="inline-flex items-center gap-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-3 py-2 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M1.5 6a3 3 0 0 1 3-3h16a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H4.5a3 3 0 0 1-3-3V6Zm9 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM16 8.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" clip-rule="evenodd" />
                                </svg>
                                Menu / Pricelist
                            </button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 bg-white/95 rounded-2xl border border-slate-200 p-6 text-center text-slate-600">
                    Data UMKM belum tersedia. Silakan cek kembali nanti.
                </div>
            @endforelse
        </section>
    </main>

    {{-- Modal Menu Gallery --}}
    <div
        id="menu-gallery-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="menu-gallery-title"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        onclick="if(event.target===this) closeMenuGallery()"
    >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 shrink-0">
                <h3 id="menu-gallery-title" class="text-base font-bold text-blue-900">Menu / Pricelist</h3>
                <button
                    type="button"
                    onclick="closeMenuGallery()"
                    class="text-gray-400 hover:text-gray-600 transition rounded-lg p-1 -mr-1"
                    aria-label="Tutup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 flex-1 flex items-center justify-center w-full">
                <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto justify-items-center">
                    <!-- Gambar akan ditambahkan oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let galleryImages = [];

        function openMenuGallery(images, name) {
            galleryImages = images;
            const modal = document.getElementById('menu-gallery-modal');
            document.getElementById('menu-gallery-title').textContent = 'Menu / Pricelist – ' + name;
            displayGallery();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeMenuGallery() {
            const modal = document.getElementById('menu-gallery-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function displayGallery() {
            const galleryGrid = document.getElementById('gallery-grid');
            galleryGrid.innerHTML = '';
            
            galleryImages.forEach((imgSrc) => {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center min-h-[250px]';
                
                const img = document.createElement('img');
                img.src = imgSrc;
                img.alt = 'Menu image';
                img.className = 'max-w-full max-h-full object-contain hover:scale-105 transition-transform duration-300 cursor-zoom-in';
                
                imgContainer.appendChild(img);
                galleryGrid.appendChild(imgContainer);
            });
        }

        document.addEventListener('keydown', function (e) {
            if (document.getElementById('menu-gallery-modal').classList.contains('hidden')) return;
            if (e.key === 'Escape') closeMenuGallery();
        });
    </script>

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
