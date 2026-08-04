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

                <form action="{{ route('admin.kelola-umkm.store') }}" method="POST" class="space-y-4">
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
                        <form action="{{ route('admin.kelola-umkm.update', $item) }}" method="POST" class="space-y-4">
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

                            <button type="submit"
                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Update UMKM
                            </button>
                        </form>

                        {{-- Menu Images Gallery --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-bold mb-4">Gambar Menu / Pricelist</h3>

                            @if ($item->menuImages->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-6">
                                    @foreach ($item->menuImages as $menuImage)
                                        <div class="relative group">
                                            <img src="{{ route('umkm.menu-image', ['menuImage' => $menuImage]) }}" alt="Menu" class="w-full aspect-square object-cover rounded-lg border border-gray-200">
                                            <form action="{{ route('admin.kelola-umkm.menu-images.destroy', $menuImage) }}" method="POST" onsubmit="return confirm('Hapus gambar menu ini?');" class="absolute inset-0 rounded-lg bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-2 rounded-lg transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 mb-4">Belum ada gambar menu yang diunggah.</p>
                            @endif

                            <form action="{{ route('admin.kelola-umkm.menu-images.store', $item) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-semibold mb-2">URL Gambar Menu / Pricelist (bisa multiple)</label>
                                    <textarea name="menu_image_urls_text" rows="3" placeholder="Satu URL per baris\nhttps://example.com/menu-1.jpg\nhttps://example.com/menu-2.jpg"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">{{ old('menu_image_urls_text') }}</textarea>
                                </div>
                                <button type="submit" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                    Tambah Gambar Menu
                                </button>
                            </form>
                        </div>

                        <form action="{{ route('admin.kelola-umkm.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data UMKM ini?');" class="mt-3">
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
