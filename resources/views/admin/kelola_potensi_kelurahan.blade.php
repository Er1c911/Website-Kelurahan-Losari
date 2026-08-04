<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Potensi Kelurahan - Losari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-white border-b border-gray-200 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Kelola Potensi Kelurahan</h1>
            <span class="text-sm font-medium text-gray-600">Sesi Aktif: <strong>{{ Auth::user()->name }}</strong></span>
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
                <h2 class="text-lg md:text-xl font-bold mb-4">Tambah Potensi Baru</h2>

                <form action="{{ route('admin.kelola-potensi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-semibold mb-1">Judul Potensi</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="images" class="block text-sm font-semibold mb-1">Gambar Potensi (opsional, bisa lebih dari 1)</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-semibold mb-1">Urutan Tampil (Opsional) </label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <button type="submit"
                            class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                        Simpan Potensi
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <h2 class="text-lg md:text-xl font-bold">Daftar Potensi Kelurahan</h2>

                @forelse ($items as $item)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                        <form action="{{ route('admin.kelola-potensi.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-semibold mb-1">Judul Potensi</label>
                                <input type="text" name="title" value="{{ old('title', $item->title) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                                <textarea name="description" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('description', $item->description) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Tambah Gambar Baru (opsional, bisa lebih dari 1)</label>
                                <input type="file" name="images[]" accept="image/*" multiple
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Urutan Tampil (Opsional) </label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Update
                            </button>
                        </form>

                        @if (($item->image_items ?? collect())->isNotEmpty())
                            <div class="mt-5 border-t border-gray-200 pt-5">
                                <p class="text-sm font-semibold mb-2">Gambar Saat Ini</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($item->image_items as $image)
                                        <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                            <img src="{{ $image['source'] }}" alt="{{ $item->title }}" class="w-full h-40 rounded-lg border border-gray-200 object-cover">

                                            @if (!empty($image['id']))
                                                <form action="{{ route('admin.kelola-potensi.images.destroy', ['potensi' => $item, 'image' => $image['id']]) }}" method="POST" onsubmit="return confirm('Hapus gambar ini?');" class="mt-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-2 rounded-lg text-sm transition">
                                                        Hapus Gambar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('admin.kelola-potensi.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus potensi ini?');" class="mt-3">
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
                        Belum ada potensi kelurahan. Tambahkan potensi pertama Anda.
                    </div>
                @endforelse
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
