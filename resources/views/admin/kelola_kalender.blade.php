<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kalender Kelurahan - Losari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-gray-50">
        <header class="bg-white border-b border-gray-200 py-4 px-4 md:px-8 flex flex-col md:flex-row md:items-center gap-3 md:gap-0 md:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Kelola Kalender Kelurahan</h1>
            <span class="text-sm font-medium text-gray-600">Sesi Aktif: <strong>{{ Auth::user()->name }}</strong></span>
        </header>

        <div class="p-4 md:p-8 max-w-7xl mx-auto">
            @php
                $timeOptions = [];
                for ($hour = 0; $hour < 24; $hour++) {
                    for ($minute = 0; $minute < 60; $minute++) {
                        $timeOptions[] = sprintf('%02d:%02d', $hour, $minute);
                    }
                }
            @endphp

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
                <h2 class="text-lg md:text-xl font-bold mb-4">Tambah Agenda Kalender</h2>

                <form action="{{ route('admin.kelola-kalender.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-semibold mb-1">Judul Kegiatan</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label for="event_date" class="block text-sm font-semibold mb-1">Tanggal Kegiatan</label>
                        <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}" required
                               class="w-full md:w-80 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-semibold mb-1">Jam Mulai (opsional)</label>
                            <select id="start_time" name="start_time"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                                <option value="">Pilih Jam Mulai</option>
                                @foreach ($timeOptions as $timeOption)
                                    <option value="{{ $timeOption }}" {{ old('start_time') === $timeOption ? 'selected' : '' }}>
                                        {{ $timeOption }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Format 24 jam: 00:00 sampai 23:59</p>
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-semibold mb-1">Jam Selesai (opsional)</label>
                            <select id="end_time" name="end_time"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                                <option value="">Pilih Jam Selesai</option>
                                @foreach ($timeOptions as $timeOption)
                                    <option value="{{ $timeOption }}" {{ old('end_time') === $timeOption ? 'selected' : '' }}>
                                        {{ $timeOption }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Format 24 jam: 00:00 sampai 23:59</p>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold mb-1">Deskripsi (opsional)</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                        Simpan Agenda
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <h2 class="text-lg md:text-xl font-bold">Daftar Agenda Kalender</h2>

                @forelse ($agendas as $agenda)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
                        <form action="{{ route('admin.kelola-kalender.update', $agenda) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-semibold mb-1">Judul Kegiatan</label>
                                <input type="text" name="title" value="{{ old('title', $agenda->title) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Tanggal Kegiatan</label>
                                <input type="date" name="event_date" value="{{ old('event_date', $agenda->event_date) }}" required
                                       class="w-full md:w-80 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Jam Mulai (opsional)</label>
                                    @php
                                        $selectedStartTime = old('start_time', $agenda->start_time ? substr($agenda->start_time, 0, 5) : '');
                                    @endphp
                                    <select name="start_time"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                                        <option value="">Pilih Jam Mulai</option>
                                        @foreach ($timeOptions as $timeOption)
                                            <option value="{{ $timeOption }}" {{ $selectedStartTime === $timeOption ? 'selected' : '' }}>
                                                {{ $timeOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                     <p class="mt-1 text-xs text-gray-500">Format 24 jam: 00:00 sampai 23:59</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Jam Selesai (opsional)</label>
                                    @php
                                        $selectedEndTime = old('end_time', $agenda->end_time ? substr($agenda->end_time, 0, 5) : '');
                                    @endphp
                                    <select name="end_time"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                                        <option value="">Pilih Jam Selesai</option>
                                        @foreach ($timeOptions as $timeOption)
                                            <option value="{{ $timeOption }}" {{ $selectedEndTime === $timeOption ? 'selected' : '' }}>
                                                {{ $timeOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                     <p class="mt-1 text-xs text-gray-500">Format 24 jam: 00:00 sampai 23:59</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-1">Deskripsi (opsional)</label>
                                <textarea name="description" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $agenda->description) }}</textarea>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Update Agenda
                            </button>
                        </form>

                        <form action="{{ route('admin.kelola-kalender.destroy', $agenda) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?');" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                Hapus Agenda
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-xl p-6 text-gray-600">
                        Belum ada agenda kalender. Tambahkan agenda pertama Anda.
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
