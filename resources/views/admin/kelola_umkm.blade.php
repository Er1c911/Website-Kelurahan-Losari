<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola UMKM - Losari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-white border-b border-gray-200 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Kelola UMKM</h1>
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
                <h2 class="text-lg md:text-xl font-bold mb-4">Tambah UMKM Baru</h2>

                <form action="{{ route('admin.kelola-umkm.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold mb-1">Nama UMKM</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="contact_name" class="block text-sm font-semibold mb-1">Nama Kontak</label>
                        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <div>
                        <label for="whatsapp_link" class="block text-sm font-semibold mb-1">Link WhatsApp Kontak</label>
                        <input type="url" id="whatsapp_link" name="whatsapp_link" value="{{ old('whatsapp_link') }}" placeholder="https://wa.me/628xxxxxxxxxx" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <div>
                        <label for="photo" class="block text-sm font-semibold mb-1">Foto (opsional)</label>
                        <input type="file" id="photo" name="photo" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                    </div>

                    <div>
                        <label for="menu" class="block text-sm font-semibold mb-1">Gambar Menu / Pricelist (opsional)</label>
                        <input type="file" id="menu" name="menu" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WebP. Maks 4MB.</p>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                        Simpan UMKM
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <h2 class="text-lg md:text-xl font-bold">Daftar UMKM</h2>

                @forelse ($umkms as $item)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                        <form action="{{ route('admin.kelola-umkm.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-semibold mb-1">Nama UMKM</label>
                                <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                                <textarea name="description" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description', $item->description) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Nama Kontak</label>
                                <input type="text" name="contact_name" value="{{ old('contact_name', $item->contact_name) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Link WhatsApp Kontak</label>
                                <input type="url" name="whatsapp_link" value="{{ old('whatsapp_link', $item->whatsapp_link) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>

                            @if ($item->photo_data || $item->photo_path)
                                <div>
                                    <p class="text-sm font-semibold mb-2">Foto Saat Ini</p>
                                    <img src="{{ $item->photo_data ?: \Illuminate\Support\Facades\Storage::disk(config('filesystems.media', 'public'))->url($item->photo_path) }}" alt="{{ $item->name }}" class="w-full max-w-md rounded-lg border border-gray-200 object-cover">
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-semibold mb-1">Ganti Foto (opsional)</label>
                                <input type="file" name="photo" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                            </div>

                            @if ($item->menu_data || $item->menu_path)
                                <div>
                                    <p class="text-sm font-semibold mb-2">Gambar Menu / Pricelist Saat Ini</p>
                                    <img src="{{ $item->menu_data ?: \Illuminate\Support\Facades\Storage::disk(config('filesystems.media', 'public'))->url($item->menu_path) }}" alt="Menu {{ $item->name }}" class="w-full max-w-md rounded-lg border border-gray-200 object-contain bg-gray-50">
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-semibold mb-1">{{ ($item->menu_data || $item->menu_path) ? 'Ganti Gambar Menu / Pricelist (opsional)' : 'Gambar Menu / Pricelist (opsional)' }}</label>
                                <input type="file" name="menu" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WebP. Maks 4MB.</p>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Update UMKM
                            </button>
                        </form>

                        <form action="{{ route('admin.kelola-umkm.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data UMKM ini?');" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Hapus
                            </button>
                        </form>

                        @if ($item->photo_data || $item->photo_path)
                            <form action="{{ route('admin.kelola-umkm.photo.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus foto UMKM ini?');" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-xl p-6 text-gray-600">
                        Belum ada data UMKM. Tambahkan data UMKM pertama Anda.
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
