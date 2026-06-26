<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Halaman Beranda - Losari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-white border-b border-gray-200 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Kelola Halaman Beranda</h1>
            <span class="text-sm font-medium text-gray-600">Sesi Aktif: <strong>{{ Auth::user()->name }}</strong></span>
        </header>

        <div class="p-4 md:p-8 max-w-7xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                <h2 class="text-lg md:text-xl font-bold mb-3">Pengaturan Beranda</h2>
                <p class="text-gray-600 leading-relaxed">
                    Halaman ini disiapkan untuk pengelolaan konten beranda seperti judul utama, deskripsi,
                    dan media profil kelurahan.
                </p>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                    <h3 class="text-lg font-bold text-gray-800">Ganti Video Beranda</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        Unggah video profil baru untuk ditampilkan pada halaman beranda dengan ukuran maksimal 50 MB.
                    </p>

                    <form action="{{ route('admin.kelola-beranda.video.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label for="video" class="block text-sm font-semibold text-gray-700 mb-2">File Video</label>
                            <input
                                id="video"
                                name="video"
                                type="file"
                                accept="video/mp4,video/webm,video/ogg,video/quicktime,.mov"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-orange-500 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-orange-600"
                                required
                            >
                            @error('video')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Simpan Video Beranda
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                    <h3 class="text-lg font-bold text-gray-800">Video Saat Ini</h3>

                    @if ($videoPath)
                        <div class="mt-4 overflow-hidden rounded-2xl bg-black shadow-lg">
                            <video class="w-full aspect-video object-cover block" controls preload="metadata">
                                <source src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.media', 'public'))->url($videoPath) }}">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        </div>
                        <p class="mt-3 text-sm text-gray-500">Sumber aktif: {{ $videoPath }}</p>
                        <form action="{{ route('admin.kelola-beranda.video.destroy') }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100"
                            >
                                Hapus Video Kustom
                            </button>
                        </form>
                    @else
                        <div class="mt-4 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
                            Belum ada video beranda yang diunggah. Halaman publik masih memakai video bawaan.
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg transition">
                    ← Kembali ke Dashboard Admin
                </a>
            </div>
        </div>
    </main>
</div>

</body>
</html>