<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kontak Narahubung - Losari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-white border-b border-gray-200 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Kelola Kontak Narahubung</h1>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-600">Sesi Aktif: <strong>{{ Auth::user()->name }}</strong></span>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-4xl mx-auto">
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

            <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                <h2 class="text-lg md:text-xl font-bold mb-2">Form Kontak Narahubung</h2>
                <p class="text-sm text-gray-600 mb-6">Perubahan pada form ini akan langsung dipakai oleh halaman kontak pada website user.</p>

                <form action="{{ route('admin.kelola-kontak.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="office_address" class="block text-sm font-semibold mb-1">Alamat Kantor</label>
                        <input type="text" id="office_address" name="office_address" value="{{ old('office_address', $kontak['office_address'] ?? '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <div>
                        <label for="service_hours" class="block text-sm font-semibold mb-1">Jam Pelayanan</label>
                        <input type="text" id="service_hours" name="service_hours" value="{{ old('service_hours', $kontak['service_hours'] ?? '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <div>
                        <label for="service_info" class="block text-sm font-semibold mb-1">Deskripsi Layanan</label>
                        <textarea id="service_info" name="service_info" rows="3" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('service_info', $kontak['service_info'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold mb-1">Email Resmi (opsional)</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $kontak['email'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <div>
                        <label for="phone_whatsapp" class="block text-sm font-semibold mb-1">Telepon</label>
                        <input type="text" id="phone_whatsapp" name="phone_whatsapp" value="{{ old('phone_whatsapp', $kontak['phone_whatsapp'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <div>
                        <label for="whatsapp_link" class="block text-sm font-semibold mb-1">Link WhatsApp (opsional)</label>
                        <input type="url" id="whatsapp_link" name="whatsapp_link" value="{{ old('whatsapp_link', $kontak['whatsapp_link'] ?? '') }}" placeholder="https://wa.me/628xxxxxxxxxx"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>

                    <button type="submit"
                            class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                        Simpan Kontak Narahubung
                    </button>
                </form>
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
