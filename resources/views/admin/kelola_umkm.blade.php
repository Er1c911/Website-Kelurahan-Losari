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
