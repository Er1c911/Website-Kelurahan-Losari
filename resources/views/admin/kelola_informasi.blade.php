<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Informasi Kelurahan - Losari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-white border-b border-gray-200 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Kelola Informasi Kelurahan</h1>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-600">Sesi Aktif: <strong>{{ Auth::user()->name }}</strong></span>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-7xl mx-auto">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8 mb-8">
                <h2 class="text-lg md:text-xl font-bold mb-4">Tambah Informasi Baru</h2>

                <form action="{{ route('admin.kelola-informasi.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-semibold mb-1">Judul Informasi</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="image_urls_text" class="block text-sm font-semibold mb-1">URL Foto Informasi (opsional, bisa lebih dari 1)</label>
                        <textarea id="image_urls_text" name="image_urls_text" rows="4" placeholder="Satu URL per baris\nhttps://example.com/gambar-1.jpg\nhttps://example.com/gambar-2.jpg"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('image_urls_text') }}</textarea>
                    </div>

                    <button type="submit"
                            data-submit-label="Simpan Informasi"
                            class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                        Simpan Informasi
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <h2 class="text-lg md:text-xl font-bold">Daftar Informasi Kelurahan</h2>

                @forelse ($informasi as $item)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                        <form action="{{ route('admin.kelola-informasi.update', $item) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-semibold mb-1">Judul Informasi</label>
                                <input type="text" name="title" value="{{ old('title', $item->title) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                                <textarea name="description" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('description', $item->description) }}</textarea>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Update
                            </button>
                        </form>

                        <div class="mt-5 border-t border-gray-200 pt-5">
                            <h3 class="text-sm font-bold text-gray-700 mb-3">Foto Informasi</h3>

                            @if ($item->image_items->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    @foreach ($item->image_items as $image)
                                        <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                            <img src="{{ $image['source'] }}" alt="Foto {{ $item->title }}" class="w-full h-48 object-cover rounded-lg border border-gray-200">

                                            @if (!empty($image['id']))
                                                <form action="{{ route('admin.kelola-informasi.images.update', ['informasi' => $item, 'image' => $image['id']]) }}" method="POST" class="mt-3 space-y-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <label class="block text-xs font-semibold text-gray-600">Ganti Foto Ini (URL)</label>
                                                    <input type="url" name="image_url" required placeholder="https://example.com/gambar-baru.jpg" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-sm">
                                                    <button type="submit" class="inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white font-semibold px-3 py-2 rounded-lg text-sm transition">
                                                        Ganti Foto
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.kelola-informasi.images.destroy', ['informasi' => $item, 'image' => $image['id']]) }}" method="POST" onsubmit="return confirm('Hapus foto ini?');" class="mt-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-2 rounded-lg text-sm transition">
                                                        Hapus Foto
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 mb-4">Belum ada foto untuk informasi ini.</p>
                            @endif

                            <form action="{{ route('admin.kelola-informasi.images.store', $item) }}" method="POST" class="space-y-2">
                                @csrf
                                <label class="block text-sm font-semibold text-gray-700">Tambah Foto Baru (URL)</label>
                                <textarea name="image_urls_text" rows="3" placeholder="Satu URL per baris\nhttps://example.com/gambar-1.jpg\nhttps://example.com/gambar-2.jpg" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">{{ old('image_urls_text') }}</textarea>
                                <button type="submit" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                    Tambah Foto
                                </button>
                            </form>
                        </div>

                        <form action="{{ route('admin.kelola-informasi.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus informasi ini?');" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-xl p-6 text-gray-600">
                        Belum ada informasi kelurahan. Tambahkan informasi pertama Anda.
                    </div>
                @endforelse

                @if (method_exists($informasi, 'links'))
                    <div class="pt-2">
                        {{ $informasi->links() }}
                    </div>
                @endif
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

