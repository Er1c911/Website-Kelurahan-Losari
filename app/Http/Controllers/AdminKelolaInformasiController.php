<?php

namespace App\Http\Controllers;

use App\Models\AgendaKalender;
use App\Models\KelolaInformasi;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AdminKelolaInformasiController extends Controller
{
    private const BERANDA_VIDEO_DIRECTORY = 'beranda';

    private const BERANDA_VIDEO_BASENAME = 'video-profil-desa';

    private function mediaDisk(): string
    {
        return (string) config('filesystems.media', 'public');
    }

    public function index()
    {
        return view('admin.dashboard', [
            'sections' => [],
        ]);
    }

    public function manage()
    {
        $informasi = KelolaInformasi::query()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.kelola_informasi', [
            'informasi' => $informasi,
        ]);
    }

    public function manageKalender()
    {
        $agendas = AgendaKalender::query()
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->orderBy('title')
            ->get();

        return view('admin.kelola_kalender', [
            'agendas' => $agendas,
        ]);
    }

    public function manageBeranda()
    {
        return view('admin.kelola_beranda', [
            'videoPath' => $this->getBerandaVideoPath(),
        ]);
    }

    public function manageUmkm()
    {
        $umkms = collect();

        if (Schema::hasTable('umkm')) {
            $umkms = Umkm::query()
                ->orderByDesc('created_at')
                ->get();
        }

        return view('admin.kelola_umkm', [
            'umkms' => $umkms,
        ]);
    }

    public function updateBerandaVideo(Request $request)
    {
        $validated = $request->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime', 'max:51200'],
        ], [
            'video.required' => 'Silakan pilih video terlebih dahulu.',
            'video.file' => 'File yang dipilih untuk video tidak valid.',
            'video.mimetypes' => 'File video yang diunggah tidak didukung.',
            'video.max' => 'Ukuran video maksimal 50 MB.',
        ]);

        $currentVideoPath = $this->getBerandaVideoPath();
        if ($currentVideoPath !== null) {
            Storage::disk($this->mediaDisk())->delete($currentVideoPath);
        }

        $extension = $validated['video']->getClientOriginalExtension() ?: $validated['video']->extension();
        $path = $validated['video']->storeAs(
            self::BERANDA_VIDEO_DIRECTORY,
            self::BERANDA_VIDEO_BASENAME.'.'.$extension,
            $this->mediaDisk()
        );

        if (! is_string($path) || $path === '') {
            return back()->withErrors([
                'video' => 'Gagal menyimpan video. Silakan coba lagi.',
            ]);
        }

        return back()->with([
            'status' => 'Video beranda berhasil diperbarui.',
            'videoPath' => $path,
        ]);
    }

    public function destroyBerandaVideo()
    {
        $currentVideoPath = $this->getBerandaVideoPath();

        if ($currentVideoPath === null) {
            return back()->with('status', 'Video beranda kustom tidak ditemukan.');
        }

        Storage::disk($this->mediaDisk())->delete($currentVideoPath);

        return back()->with('status', 'Video beranda berhasil dihapus. Halaman publik kembali memakai video bawaan.');
    }

    public function storeKalender(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        AgendaKalender::create($validated);

        return back()->with('status', 'Agenda kalender berhasil ditambahkan.');
    }

    public function updateKalender(Request $request, AgendaKalender $agenda)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $agenda->update($validated);

        return back()->with('status', 'Agenda kalender berhasil diperbarui.');
    }

    public function destroyKalender(AgendaKalender $agenda)
    {
        $agenda->delete();

        return back()->with('status', 'Agenda kalender berhasil dihapus.');
    }

    public function storeUmkm(Request $request)
    {
        if (! Schema::hasTable('umkm')) {
            return back()->withErrors([
                'umkm' => 'Tabel UMKM belum tersedia di server. Jalankan migrasi terlebih dahulu.',
            ])->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_name' => ['required', 'string', 'max:255'],
            'whatsapp_link' => ['required', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'contact_name' => $validated['contact_name'],
            'whatsapp_link' => $validated['whatsapp_link'],
        ];

        if ($request->hasFile('photo')) {
            $imageFile = $request->file('photo');
            $mimeType = $imageFile->getMimeType() ?: 'application/octet-stream';
            $data['photo_data'] = 'data:'.$mimeType.';base64,'.base64_encode((string) $imageFile->get());

            try {
                $storedPath = $imageFile->store('umkm', $this->mediaDisk());
                if (is_string($storedPath) && $storedPath !== '') {
                    $data['photo_path'] = $storedPath;
                }
            } catch (Throwable $exception) {
                // On serverless filesystems, local disk writes can fail. Keep photo_data as fallback.
                $data['photo_path'] = null;
            }
        }

        try {
            Umkm::create($data);
        } catch (Throwable $exception) {
            return back()->withErrors([
                'umkm' => 'Gagal menyimpan data UMKM. Pastikan database production sudah siap.',
            ])->withInput();
        }

        return back()->with('status', 'Data UMKM berhasil ditambahkan.');
    }

    public function updateUmkm(Request $request, Umkm $umkm)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_name' => ['required', 'string', 'max:255'],
            'whatsapp_link' => ['required', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $umkm->name = $validated['name'];
        $umkm->description = $validated['description'] ?? null;
        $umkm->contact_name = $validated['contact_name'];
        $umkm->whatsapp_link = $validated['whatsapp_link'];

        if ($request->hasFile('photo')) {
            if (!empty($umkm->photo_path)) {
                Storage::disk($this->mediaDisk())->delete($umkm->photo_path);
            }

            $imageFile = $request->file('photo');
            $mimeType = $imageFile->getMimeType() ?: 'application/octet-stream';
            $umkm->photo_data = 'data:'.$mimeType.';base64,'.base64_encode((string) $imageFile->get());

            $path = $imageFile->store('umkm', $this->mediaDisk());
            if (is_string($path) && $path !== '') {
                $umkm->photo_path = $path;
            }
        }

        $umkm->save();

        return back()->with('status', 'Data UMKM berhasil diperbarui.');
    }

    public function destroyUmkm(Umkm $umkm)
    {
        if (!empty($umkm->photo_path)) {
            Storage::disk($this->mediaDisk())->delete($umkm->photo_path);
        }

        $umkm->delete();

        return back()->with('status', 'Data UMKM berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data = [
            'key' => (string) Str::uuid(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $mimeType = $imageFile->getMimeType() ?: 'application/octet-stream';
            $data['image_data'] = 'data:'.$mimeType.';base64,'.base64_encode((string) $imageFile->get());

            $storedPath = $imageFile->store('kelola_informasi', $this->mediaDisk());
            if (is_string($storedPath) && $storedPath !== '') {
                $data['image_path'] = $storedPath;
            }
        }

        KelolaInformasi::create($data);

        return back()->with('status', 'Informasi berhasil ditambahkan.');
    }

    public function update(Request $request, KelolaInformasi $informasi)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $informasi->title = $validated['title'];
        $informasi->description = $validated['description'] ?? null;

        if ($request->hasFile('image')) {
            if (!empty($informasi->image_path)) {
                Storage::disk($this->mediaDisk())->delete($informasi->image_path);
            }

            $imageFile = $request->file('image');
            $mimeType = $imageFile->getMimeType() ?: 'application/octet-stream';
            $informasi->image_data = 'data:'.$mimeType.';base64,'.base64_encode((string) $imageFile->get());

            $path = $imageFile->store('kelola_informasi', $this->mediaDisk());
            if (is_string($path) && $path !== '') {
                $informasi->image_path = $path;
            }
        }

        $informasi->save();

        return back()->with('status', 'Informasi berhasil diperbarui.');
    }

    public function destroy(KelolaInformasi $informasi)
    {
        if (!empty($informasi->image_path)) {
            Storage::disk($this->mediaDisk())->delete($informasi->image_path);
        }

        $informasi->delete();

        return back()->with('status', 'Informasi berhasil dihapus.');
    }

    private function getBerandaVideoPath(): ?string
    {
        foreach (['mp4', 'webm', 'ogg', 'mov'] as $extension) {
            $path = self::BERANDA_VIDEO_DIRECTORY.'/'.self::BERANDA_VIDEO_BASENAME.'.'.$extension;

            if (Storage::disk($this->mediaDisk())->exists($path)) {
                return $path;
            }
        }

        return null;
    }
}

