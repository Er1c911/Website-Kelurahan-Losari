<?php

namespace App\Http\Controllers;

use App\Models\AgendaKalender;
use App\Models\KelolaInformasi;
use App\Models\KelolaInformasiImage;
use App\Models\PotensiKelurahanImage;
use App\Models\PotensiKelurahanItem;
use App\Models\Umkm;
use App\Models\UmkmMenuImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AdminKelolaInformasiController extends Controller
{
    private const BERANDA_VIDEO_DIRECTORY = 'beranda';

    private const BERANDA_VIDEO_BASENAME = 'video-profil-desa';

    private const BERANDA_VIDEO_URL_KEY = '__beranda_video_url__';

    private const KONTAK_NARAHUBUNG_KEY = '__kontak_narahubung__';

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
            ->whereNotIn('key', [
                self::BERANDA_VIDEO_URL_KEY,
                self::KONTAK_NARAHUBUNG_KEY,
            ])
            ->select(['id', 'key', 'title', 'description', 'image_path', 'image_data', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $imagesByInformasiId = collect();
        $informasiIds = $informasi->getCollection()->pluck('id')->all();

        if (!empty($informasiIds)) {
            try {
                $imagesByInformasiId = KelolaInformasiImage::query()
                    ->select(['id', 'kelola_informasi_id', 'image_path', 'image_data', 'created_at'])
                    ->whereIn('kelola_informasi_id', $informasiIds)
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('kelola_informasi_id');
            } catch (Throwable $exception) {
                $imagesByInformasiId = collect();
            }
        }

        $informasi = $this->attachInformasiImageSources($informasi, $imagesByInformasiId);

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
        $videoUrlSetting = KelolaInformasi::query()
            ->where('key', self::BERANDA_VIDEO_URL_KEY)
            ->value('description');

        $videoUrlSetting = is_string($videoUrlSetting) ? $videoUrlSetting : null;

        return view('admin.kelola_beranda', [
            'videoPath' => $this->getBerandaVideoPath(),
            'videoUrlSetting' => $videoUrlSetting,
            'videoPreviewUrl' => $this->buildEmbeddedVideoUrl($videoUrlSetting),
            'videoPreviewUseIframe' => $this->shouldUseIframePlayer($videoUrlSetting),
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

    public function managePotensiKelurahan()
    {
        $items = PotensiKelurahanItem::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $items = $this->attachPotensiImageSources($items);

        return view('admin.kelola_potensi_kelurahan', [
            'items' => $items,
        ]);
    }

    public function manageKontak()
    {
        $raw = KelolaInformasi::query()
            ->where('key', self::KONTAK_NARAHUBUNG_KEY)
            ->value('description');

        $kontak = $this->normalizeKontakPayload($this->decodeKontakPayload($raw));

        return view('admin.kelola_kontak', [
            'kontak' => $kontak,
        ]);
    }

    public function updateKontak(Request $request)
    {
        $validated = $request->validate([
            'office_address' => ['required', 'string', 'max:255'],
            'service_hours' => ['required', 'string', 'max:255'],
            'service_info' => ['required', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_whatsapp' => ['nullable', 'string', 'max:255'],
            'whatsapp_link' => ['nullable', 'url', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $payload = $this->normalizeKontakPayload($validated);

        KelolaInformasi::query()->updateOrCreate(
            ['key' => self::KONTAK_NARAHUBUNG_KEY],
            [
                'title' => 'Kontak Narahubung',
                'description' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'image_path' => null,
                'image_data' => null,
            ]
        );

        return back()->with('status', 'Data kontak narahubung berhasil diperbarui.');
    }

    public function updateBerandaVideo(Request $request)
    {
        $validated = $request->validate([
            'video_url' => ['required', 'url', 'max:2048'],
        ], [
            'video_url.required' => 'Silakan isi URL video terlebih dahulu.',
            'video_url.url' => 'Format URL video tidak valid.',
            'video_url.max' => 'URL video terlalu panjang.',
        ]);

        $normalizedVideoUrl = $this->normalizeVideoUrl((string) $validated['video_url']);

        if (! $this->isSupportedVideoUrl($normalizedVideoUrl)) {
            return back()->withErrors([
                'video_url' => 'Gunakan URL langsung ke file video (.mp4, .webm, .ogg, atau .mov), atau link Google Drive /file/d/...',
            ])->withInput();
        }

        KelolaInformasi::query()->updateOrCreate(
            ['key' => self::BERANDA_VIDEO_URL_KEY],
            [
                'title' => 'Beranda Video URL',
                'description' => $normalizedVideoUrl,
                'image_path' => null,
                'image_data' => null,
            ]
        );

        $currentVideoPath = $this->getBerandaVideoPath();
        if ($currentVideoPath !== null) {
            Storage::disk($this->mediaDisk())->delete($currentVideoPath);
        }

        return back()->with([
            'status' => 'Video beranda berhasil diperbarui.',
        ]);
    }

    public function destroyBerandaVideo()
    {
        $deletedUrlSetting = KelolaInformasi::query()
            ->where('key', self::BERANDA_VIDEO_URL_KEY)
            ->delete();

        $currentVideoPath = $this->getBerandaVideoPath();

        if ($currentVideoPath === null) {
            if ($deletedUrlSetting > 0) {
                return back()->with('status', 'Video beranda berhasil dihapus. Halaman publik kembali memakai video bawaan.');
            }

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
            'photo_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'contact_name' => $validated['contact_name'],
            'whatsapp_link' => $validated['whatsapp_link'],
        ];

        $photoUrl = trim((string) ($validated['photo_url'] ?? ''));
        if ($photoUrl !== '') {
            $data['photo_path'] = $this->normalizeExternalImageUrl($photoUrl);
            $data['photo_data'] = null;
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
            'photo_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $umkm->name = $validated['name'];
        $umkm->description = $validated['description'] ?? null;
        $umkm->contact_name = $validated['contact_name'];
        $umkm->whatsapp_link = $validated['whatsapp_link'];

        $photoUrl = trim((string) ($validated['photo_url'] ?? ''));
        if ($photoUrl !== '') {
            if (!empty($umkm->photo_path) && ! $this->isExternalUrl($umkm->photo_path)) {
                Storage::disk($this->mediaDisk())->delete($umkm->photo_path);
            }

            $umkm->photo_path = $this->normalizeExternalImageUrl($photoUrl);
            $umkm->photo_data = null;
        }

        $umkm->save();

        return back()->with('status', 'Data UMKM berhasil diperbarui.');
    }

    public function destroyUmkm(Umkm $umkm)
    {
        if (!empty($umkm->photo_path) && ! $this->isExternalUrl($umkm->photo_path)) {
            Storage::disk($this->mediaDisk())->delete($umkm->photo_path);
        }

        if (!empty($umkm->menu_path) && ! $this->isExternalUrl($umkm->menu_path)) {
            Storage::disk($this->mediaDisk())->delete($umkm->menu_path);
        }

        $umkm->delete();

        return back()->with('status', 'Data UMKM berhasil dihapus.');
    }

    public function destroyUmkmPhoto(Umkm $umkm)
    {
        if (!empty($umkm->photo_path) && ! $this->isExternalUrl($umkm->photo_path)) {
            Storage::disk($this->mediaDisk())->delete($umkm->photo_path);
            $umkm->photo_path = null;
        } else {
            $umkm->photo_path = null;
        }

        $umkm->photo_data = null;
        $umkm->save();

        return back()->with('status', 'Foto UMKM berhasil dihapus.');
    }

    public function storeUmkmMenuImages(Request $request, Umkm $umkm)
    {
        $request->validate([
            'menu_image_urls_text' => ['required', 'string', 'max:10000'],
        ]);

        $imageUrls = $this->parseImageUrlsText($request->input('menu_image_urls_text'));

        if ($imageUrls->isEmpty()) {
            return back()->withErrors([
                'menu_image_urls_text' => 'Masukkan minimal satu URL gambar menu yang valid (http/https).',
            ])->withInput();
        }

        foreach ($imageUrls as $imageUrl) {
            UmkmMenuImage::create([
                'umkm_id' => $umkm->id,
                'image_path' => $this->normalizeExternalImageUrl((string) $imageUrl),
                'image_data' => null,
            ]);
        }

        return back()->with('status', 'Gambar menu berhasil ditambahkan.');
    }

    public function destroyUmkmMenuImage(UmkmMenuImage $menuImage)
    {
        if (!empty($menuImage->image_path) && ! $this->isExternalUrl($menuImage->image_path)) {
            Storage::disk($this->mediaDisk())->delete($menuImage->image_path);
        }

        $menuImage->delete();

        return back()->with('status', 'Gambar menu berhasil dihapus.');
    }

    public function storePotensiKelurahan(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_urls_text' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $hasImageColumns = $this->hasPotensiImageColumns();
        $hasImagesTable = $this->hasPotensiImagesTable();
        $imageUrls = $this->parseImageUrlsText($validated['image_urls_text'] ?? null);

        if ($imageUrls->isNotEmpty() && ! $hasImageColumns && ! $hasImagesTable) {
            return back()->withErrors([
                'image_urls_text' => 'Struktur gambar potensi belum tersedia di database. Jalankan migrasi terbaru terlebih dahulu.',
            ])->withInput();
        }

        if ($imageUrls->isNotEmpty() && ! $hasImagesTable) {
            return back()->withErrors([
                'image_urls_text' => 'Tabel galeri potensi belum tersedia. Jalankan migrasi terbaru terlebih dahulu.',
            ])->withInput();
        }

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($hasImageColumns) {
            $data['image_path'] = null;
            $data['image_data'] = null;
        }

        $potensi = PotensiKelurahanItem::create($data);

        if ($imageUrls->isNotEmpty()) {
            $firstStoredImage = null;

            foreach ($imageUrls as $imageUrl) {
                $storedImage = $this->storePotensiImageFromUrl($potensi, (string) $imageUrl);

                if ($firstStoredImage === null) {
                    $firstStoredImage = $storedImage;
                }
            }

            if ($hasImageColumns && is_array($firstStoredImage)) {
                $potensi->image_path = $firstStoredImage['image_path'];
                $potensi->image_data = $firstStoredImage['image_data'];
                $potensi->save();
            }
        }

        return back()->with('status', 'Potensi kelurahan berhasil ditambahkan.');
    }

    public function updatePotensiKelurahan(Request $request, PotensiKelurahanItem $potensi)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $potensi->title = $validated['title'];
        $potensi->description = $validated['description'] ?? null;
        $potensi->sort_order = $validated['sort_order'] ?? 0;

        $potensi->save();

        return back()->with('status', 'Potensi kelurahan berhasil diperbarui.');
    }

    public function storePotensiImages(Request $request, PotensiKelurahanItem $potensi)
    {
        if (! $this->hasPotensiImagesTable()) {
            return back()->withErrors([
                'image_urls_text' => 'Tabel galeri potensi belum tersedia. Jalankan migrasi terbaru terlebih dahulu.',
            ])->withInput();
        }

        $request->validate([
            'image_urls_text' => ['required', 'string', 'max:10000'],
        ]);

        $imageUrls = $this->parseImageUrlsText($request->input('image_urls_text'));

        if ($imageUrls->isEmpty()) {
            return back()->withErrors([
                'image_urls_text' => 'Masukkan minimal satu URL gambar yang valid (http/https).',
            ])->withInput();
        }

        foreach ($imageUrls as $imageUrl) {
            $this->storePotensiImageFromUrl($potensi, (string) $imageUrl);
        }

        if ($this->hasPotensiImageColumns()) {
            $latestImage = $potensi->images()->latest('id')->first();

            if ($latestImage instanceof PotensiKelurahanImage) {
                $potensi->image_path = $latestImage->image_path;
                $potensi->image_data = $latestImage->image_data;
                $potensi->save();
            }
        }

        return back()->with('status', 'Gambar potensi berhasil ditambahkan.');
    }

    public function updatePotensiImage(Request $request, PotensiKelurahanItem $potensi, PotensiKelurahanImage $image)
    {
        if (! $this->hasPotensiImagesTable()) {
            return back()->withErrors([
                'image_url' => 'Tabel galeri potensi belum tersedia. Jalankan migrasi terbaru terlebih dahulu.',
            ])->withInput();
        }

        if ($image->potensi_kelurahan_item_id !== $potensi->id) {
            abort(404);
        }

        $request->validate([
            'image_url' => ['required', 'url', 'max:2048'],
        ]);

        if (!empty($image->image_path) && ! $this->isExternalUrl($image->image_path)) {
            Storage::disk($this->mediaDisk())->delete($image->image_path);
        }

        $image->image_path = $this->normalizeExternalImageUrl(trim((string) $request->input('image_url', '')));
        $image->image_data = null;
        $image->save();

        if ($this->hasPotensiImageColumns()) {
            $latestImage = $potensi->images()->latest('id')->first();

            $potensi->image_path = $latestImage?->image_path;
            $potensi->image_data = $latestImage?->image_data;
            $potensi->save();
        }

        return back()->with('status', 'Gambar potensi berhasil diperbarui.');
    }

    public function destroyPotensiImage(PotensiKelurahanItem $potensi, PotensiKelurahanImage $image)
    {
        if (! $this->hasPotensiImagesTable()) {
            return back()->withErrors([
                'image' => 'Tabel galeri potensi belum tersedia. Jalankan migrasi terbaru terlebih dahulu.',
            ]);
        }

        if ($image->potensi_kelurahan_item_id !== $potensi->id) {
            abort(404);
        }

        if (!empty($image->image_path) && ! $this->isExternalUrl($image->image_path)) {
            Storage::disk($this->mediaDisk())->delete($image->image_path);
        }

        $image->delete();

        if ($this->hasPotensiImageColumns()) {
            $latestImage = $potensi->images()->latest('id')->first();

            $potensi->image_path = $latestImage?->image_path;
            $potensi->image_data = $latestImage?->image_data;
            $potensi->save();
        }

        return back()->with('status', 'Gambar potensi berhasil dihapus.');
    }

    public function destroyPotensiKelurahan(PotensiKelurahanItem $potensi)
    {
        if ($this->hasPotensiImagesTable()) {
            foreach ($potensi->images as $image) {
                if (!empty($image->image_path) && ! $this->isExternalUrl($image->image_path)) {
                    Storage::disk($this->mediaDisk())->delete($image->image_path);
                }
            }
        }

        if ($this->hasPotensiImageColumns() && !empty($potensi->image_path) && ! $this->isExternalUrl($potensi->image_path)) {
            Storage::disk($this->mediaDisk())->delete($potensi->image_path);
        }

        $potensi->delete();

        return back()->with('status', 'Potensi kelurahan berhasil dihapus.');
    }

    private function hasPotensiImageColumns(): bool
    {
        return Schema::hasColumn('potensi_kelurahan_items', 'image_path')
            && Schema::hasColumn('potensi_kelurahan_items', 'image_data');
    }

    private function hasPotensiImagesTable(): bool
    {
        return Schema::hasTable('potensi_kelurahan_images');
    }

    private function storePotensiImage(PotensiKelurahanItem $potensi, UploadedFile $imageFile): array
    {
        $storedPath = null;

        try {
            $storedPath = $imageFile->store('potensi_kelurahan', $this->mediaDisk());
            if (!is_string($storedPath) || $storedPath === '') {
                $storedPath = null;
            }
        } catch (Throwable $exception) {
            $storedPath = null;
        }

        $imageData = null;
        if ($storedPath === null || $this->shouldPersistInlineMediaFallback()) {
            $mimeType = $imageFile->getMimeType() ?: 'application/octet-stream';
            $imageData = 'data:'.$mimeType.';base64,'.base64_encode((string) $imageFile->get());
        }

        if ($this->hasPotensiImagesTable()) {
            PotensiKelurahanImage::create([
                'potensi_kelurahan_item_id' => $potensi->id,
                'image_path' => $storedPath,
                'image_data' => $imageData,
            ]);
        }

        return [
            'image_path' => $storedPath,
            'image_data' => $imageData,
        ];
    }

    private function storePotensiImageFromUrl(PotensiKelurahanItem $potensi, string $imageUrl): array
    {
        $normalizedUrl = $this->normalizeExternalImageUrl(trim($imageUrl));

        if ($normalizedUrl === '') {
            return [
                'image_path' => null,
                'image_data' => null,
            ];
        }

        if ($this->hasPotensiImagesTable()) {
            PotensiKelurahanImage::create([
                'potensi_kelurahan_item_id' => $potensi->id,
                'image_path' => $normalizedUrl,
                'image_data' => null,
            ]);
        }

        return [
            'image_path' => $normalizedUrl,
            'image_data' => null,
        ];
    }

    private function attachPotensiImageSources(Collection $items): Collection
    {
        $disk = Storage::disk($this->mediaDisk());
        $hasImagesTable = $this->hasPotensiImagesTable();
        $imagesByPotensiId = collect();

        if ($hasImagesTable) {
            $potensiIds = $items->pluck('id')->all();

            if (!empty($potensiIds)) {
                $imagesByPotensiId = PotensiKelurahanImage::query()
                    ->select(['id', 'potensi_kelurahan_item_id', 'image_path', 'image_data', 'created_at'])
                    ->whereIn('potensi_kelurahan_item_id', $potensiIds)
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('potensi_kelurahan_item_id');
            }
        }

        return $items->map(function (PotensiKelurahanItem $item) use ($disk, $imagesByPotensiId) {
            $resolvedImages = collect($imagesByPotensiId->get($item->id, []))
                ->map(function (PotensiKelurahanImage $image) use ($disk) {
                    $source = $this->resolveImageSource(
                        $disk,
                        $image->image_path,
                        route('potensi.image', ['image' => $image])
                    );

                    if ($source === null) {
                        return null;
                    }

                    return [
                        'id' => $image->id,
                        'source' => $source,
                    ];
                })
                ->filter()
                ->values();

            if ($resolvedImages->isEmpty()) {
                $legacySource = $this->resolveImageSource(
                    $disk,
                    $item->image_path,
                    route('potensi.legacy-image', ['potensi' => $item])
                );

                if ($legacySource !== null) {
                    $resolvedImages->push([
                        'id' => null,
                        'source' => $legacySource,
                    ]);
                }
            }

            $item->image_items = $resolvedImages;
            $item->image_source = $resolvedImages->first()['source'] ?? null;

            return $item;
        });
    }

    private function decodeKontakPayload($raw): array
    {
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeKontakPayload(array $data): array
    {
        return [
            'office_address' => $this->sanitizeKontakValue($data['office_address'] ?? 'Jl. Suropati No. 157 Singosari', 'Jl. Suropati No. 157 Singosari'),
            'service_hours' => $this->sanitizeKontakValue($data['service_hours'] ?? 'Senin - Jumat, 08.00 - 15.00 WIB', 'Senin - Jumat, 08.00 - 15.00 WIB'),
            'service_info' => $this->sanitizeKontakValue($data['service_info'] ?? 'Administrasi kependudukan, informasi kegiatan, dan pelayanan warga.', 'Administrasi kependudukan, informasi kegiatan, dan pelayanan warga.'),
            'email' => $this->sanitizeKontakValue($data['email'] ?? null, ''),
            'phone_whatsapp' => $this->sanitizeKontakValue($data['phone_whatsapp'] ?? null, ''),
            'whatsapp_link' => $this->sanitizeKontakValue($data['whatsapp_link'] ?? null, ''),
            'note' => $this->sanitizeKontakValue($data['note'] ?? 'Untuk layanan cepat, siapkan data identitas dan keperluan sebelum menghubungi petugas.', 'Untuk layanan cepat, siapkan data identitas dan keperluan sebelum menghubungi petugas.'),
        ];
    }

    private function sanitizeKontakValue($value, string $fallback): string
    {
        if (!is_string($value)) {
            return $fallback;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_urls_text' => ['nullable', 'string', 'max:10000'],
        ]);

        $data = [
            'key' => (string) Str::uuid(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => null,
            'image_data' => null,
        ];

        $informasi = KelolaInformasi::create($data);

        $imageUrls = $this->parseImageUrlsText($validated['image_urls_text'] ?? null);

        if ($imageUrls->isNotEmpty()) {
            if (! Schema::hasTable('kelola_informasi_images')) {
                return back()->withErrors([
                    'image_urls_text' => 'Tabel foto informasi belum tersedia. Jalankan migrasi terlebih dahulu.',
                ])->withInput();
            }

            foreach ($imageUrls as $imageUrl) {
                $this->storeInformasiImageFromUrl($informasi, (string) $imageUrl);
            }
        }

        return back()->with('status', 'Informasi berhasil ditambahkan.');
    }

    public function update(Request $request, KelolaInformasi $informasi)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $informasi->title = $validated['title'];
        $informasi->description = $validated['description'] ?? null;

        $informasi->save();

        return back()->with('status', 'Informasi berhasil diperbarui.');
    }

    public function storeInformasiImages(Request $request, KelolaInformasi $informasi)
    {
        if (! Schema::hasTable('kelola_informasi_images')) {
            return back()->withErrors([
                'image_urls_text' => 'Tabel foto informasi belum tersedia. Jalankan migrasi terlebih dahulu.',
            ])->withInput();
        }

        $request->validate([
            'image_urls_text' => ['required', 'string', 'max:10000'],
        ]);

        $imageUrls = $this->parseImageUrlsText($request->input('image_urls_text'));

        if ($imageUrls->isEmpty()) {
            return back()->withErrors([
                'image_urls_text' => 'Masukkan minimal satu URL gambar yang valid (http/https).',
            ])->withInput();
        }

        foreach ($imageUrls as $imageUrl) {
            $this->storeInformasiImageFromUrl($informasi, (string) $imageUrl);
        }

        return back()->with('status', 'Foto informasi berhasil ditambahkan.');
    }

    public function updateInformasiImage(Request $request, KelolaInformasi $informasi, KelolaInformasiImage $image)
    {
        if (! Schema::hasTable('kelola_informasi_images')) {
            return back()->withErrors([
                'image' => 'Tabel foto informasi belum tersedia. Jalankan migrasi terlebih dahulu.',
            ])->withInput();
        }

        if ($image->kelola_informasi_id !== $informasi->id) {
            abort(404);
        }

        $request->validate([
            'image_url' => ['required', 'url', 'max:2048'],
        ]);

        if (!empty($image->image_path) && ! $this->isExternalUrl($image->image_path)) {
            Storage::disk($this->mediaDisk())->delete($image->image_path);
        }

        $newImageUrl = $this->normalizeExternalImageUrl(trim((string) $request->input('image_url', '')));

        $image->image_path = null;
        $image->image_data = null;

        $image->image_path = $newImageUrl;

        $image->save();

        return back()->with('status', 'Foto informasi berhasil diganti.');
    }

    public function destroyInformasiImage(KelolaInformasi $informasi, KelolaInformasiImage $image)
    {
        if (! Schema::hasTable('kelola_informasi_images')) {
            return back()->withErrors([
                'image' => 'Tabel foto informasi belum tersedia. Jalankan migrasi terlebih dahulu.',
            ]);
        }

        if ($image->kelola_informasi_id !== $informasi->id) {
            abort(404);
        }

        if (!empty($image->image_path) && ! $this->isExternalUrl($image->image_path)) {
            Storage::disk($this->mediaDisk())->delete($image->image_path);
        }

        $image->delete();

        return back()->with('status', 'Foto informasi berhasil dihapus.');
    }

    public function destroy(KelolaInformasi $informasi)
    {
        if (Schema::hasTable('kelola_informasi_images')) {
            foreach ($informasi->images as $image) {
                if (!empty($image->image_path) && ! $this->isExternalUrl($image->image_path)) {
                    Storage::disk($this->mediaDisk())->delete($image->image_path);
                }
            }
        }

        if (!empty($informasi->image_path) && ! $this->isExternalUrl($informasi->image_path)) {
            Storage::disk($this->mediaDisk())->delete($informasi->image_path);
        }

        $informasi->delete();

        return back()->with('status', 'Informasi berhasil dihapus.');
    }

    private function attachInformasiImageSources($informasiPaginator, Collection $imagesByInformasiId)
    {
        $disk = Storage::disk($this->mediaDisk());

        $transformed = $informasiPaginator->getCollection()->map(function (KelolaInformasi $item) use ($disk, $imagesByInformasiId) {
            $resolvedImages = collect();

            $resolvedImages = collect($imagesByInformasiId->get($item->id, []))
                ->map(function (KelolaInformasiImage $image) use ($disk) {
                    $source = $this->resolveImageSource(
                        $disk,
                        $image->image_path,
                        route('informasi.image', ['image' => $image])
                    );

                    if ($source === null) {
                        return null;
                    }

                    return [
                        'id' => $image->id,
                        'source' => $source,
                    ];
                })
                ->filter()
                ->values();

            if ($resolvedImages->isEmpty()) {
                $legacySource = $this->resolveImageSource(
                    $disk,
                    $item->image_path,
                    route('informasi.legacy-image', ['informasi' => $item])
                );

                if ($legacySource !== null) {
                    $resolvedImages->push([
                        'id' => null,
                        'source' => $legacySource,
                    ]);
                }
            }

            $item->image_items = $resolvedImages;
            $item->image_source = $resolvedImages->first()['source'] ?? null;

            return $item;
        });

        $informasiPaginator->setCollection($transformed);

        return $informasiPaginator;
    }

    private function storeInformasiImage(KelolaInformasi $informasi, UploadedFile $imageFile): void
    {
        $storedPath = null;

        try {
            $storedPath = $imageFile->store('kelola_informasi', $this->mediaDisk());
            if (!is_string($storedPath) || $storedPath === '') {
                $storedPath = null;
            }
        } catch (Throwable $exception) {
            $storedPath = null;
        }

        $imageData = null;
        if ($storedPath === null || $this->shouldPersistInlineMediaFallback()) {
            $mimeType = $imageFile->getMimeType() ?: 'application/octet-stream';
            $imageData = 'data:'.$mimeType.';base64,'.base64_encode((string) $imageFile->get());
        }

        KelolaInformasiImage::create([
            'kelola_informasi_id' => $informasi->id,
            'image_path' => $storedPath,
            'image_data' => $imageData,
        ]);
    }

    private function storeInformasiImageFromUrl(KelolaInformasi $informasi, string $imageUrl): void
    {
        $trimmedUrl = $this->normalizeExternalImageUrl(trim($imageUrl));

        if ($trimmedUrl === '') {
            return;
        }

        KelolaInformasiImage::create([
            'kelola_informasi_id' => $informasi->id,
            'image_path' => $trimmedUrl,
            'image_data' => null,
        ]);
    }

    private function parseImageUrlsText($raw): Collection
    {
        if (!is_string($raw) || trim($raw) === '') {
            return collect();
        }

        return collect(preg_split('/\r\n|\r|\n/', $raw) ?: [])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->filter(fn ($value) => filter_var($value, FILTER_VALIDATE_URL))
            ->values();
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

    private function shouldPersistInlineMediaFallback(): bool
    {
        return (bool) env('VERCEL') && in_array($this->mediaDisk(), ['local', 'public'], true);
    }

    private function resolveImageSource($disk, $imagePath, ?string $fallbackUrl = null): ?string
    {
        $imagePathValue = is_string($imagePath) ? trim($imagePath) : '';

        if ($this->isExternalUrl($imagePathValue)) {
            $normalizedExternalUrl = $this->normalizeExternalImageUrl($imagePathValue);

            // Google Drive images can be blocked by CORP when loaded directly in <img>.
            if ($this->extractGoogleDriveFileId($normalizedExternalUrl) !== null && is_string($fallbackUrl) && trim($fallbackUrl) !== '') {
                return $fallbackUrl;
            }

            return $normalizedExternalUrl;
        }

        if ($imagePathValue !== '') {
            try {
                if ($disk->exists($imagePathValue)) {
                    return $disk->url($imagePathValue);
                }
            } catch (Throwable $exception) {
                // Fall back to inline image data when storage is unavailable on serverless.
            }
        }

        return is_string($fallbackUrl) && trim($fallbackUrl) !== '' ? $fallbackUrl : null;
    }

    private function isExternalUrl(?string $value): bool
    {
        return is_string($value) && preg_match('#^https?://#i', trim($value)) === 1;
    }

    private function normalizeExternalImageUrl(string $url): string
    {
        $fileId = $this->extractGoogleDriveFileId($url);

        if ($fileId !== null) {
            return 'https://drive.google.com/uc?export=view&id='.$fileId;
        }

        return $url;
    }

    private function extractGoogleDriveFileId(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $parts = parse_url(trim($url));

        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        if (! in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) {
            return null;
        }

        $path = trim((string) ($parts['path'] ?? ''), '/');
        $segments = $path === '' ? [] : explode('/', $path);

        if (($segments[0] ?? null) === 'file' && ($segments[1] ?? null) === 'd') {
            $fileId = trim((string) ($segments[2] ?? ''));
            if ($fileId !== '') {
                return $fileId;
            }
        }

        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);

        $fileId = isset($query['id']) ? trim((string) $query['id']) : '';
        if ($fileId !== '') {
            return $fileId;
        }

        return null;
    }

    private function normalizeVideoUrl(string $url): string
    {
        $trimmed = trim($url);

        if (preg_match('#^https?://drive\.google\.com/file/d/([^/]+)/#i', $trimmed, $matches) === 1) {
            return 'https://drive.google.com/uc?export=download&id='.$matches[1];
        }

        if (preg_match('#^https?://drive\.google\.com/open\?id=([^&]+)#i', $trimmed, $matches) === 1) {
            return 'https://drive.google.com/uc?export=download&id='.$matches[1];
        }

        return $trimmed;
    }

    private function isSupportedVideoUrl(string $url): bool
    {
        if (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $url) === 1) {
            return true;
        }

        return preg_match('#^https?://drive\.google\.com/uc\?export=download&id=[A-Za-z0-9_-]+$#i', $url) === 1;
    }

    private function shouldUseIframePlayer(?string $url): bool
    {
        return $this->extractGoogleDriveFileId($url) !== null;
    }

    private function buildEmbeddedVideoUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $fileId = $this->extractGoogleDriveFileId($url);
        if ($fileId !== null) {
            return 'https://drive.google.com/file/d/'.$fileId.'/preview';
        }

        return $url;
    }

}

