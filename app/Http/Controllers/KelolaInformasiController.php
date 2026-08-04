<?php

namespace App\Http\Controllers;

use App\Models\KelolaInformasi;
use App\Models\KelolaInformasiImage;
use App\Models\PotensiKelurahanImage;
use App\Models\PotensiKelurahanItem;
use App\Models\Umkm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class KelolaInformasiController extends Controller
{
    private const BERANDA_VIDEO_URL_KEY = '__beranda_video_url__';

    private const KONTAK_NARAHUBUNG_KEY = '__kontak_narahubung__';

    private function mediaDisk(): string
    {
        return (string) config('filesystems.media', 'public');
    }

    public function index()
    {
        [$homeVideoUrl, $homeVideoUseIframe] = $this->resolveHomeVideo();

        return view('welcome_user', [
            'homeVideoUrl' => $homeVideoUrl,
            'homeVideoUseIframe' => $homeVideoUseIframe,
        ]);
    }

    public function informasiDesa()
    {
        $sections = $this->getSections();

        return view('informasi_desa', [
            'sections' => $sections,
        ]);
    }

    public function umkm()
    {
        $umkms = collect();

        if (Schema::hasTable('umkm')) {
            $umkms = Umkm::query()
                ->orderByDesc('created_at')
                ->get();
        }

        return view('umkm', [
            'umkms' => $umkms,
        ]);
    }

    public function potensiKelurahan()
    {
        $items = PotensiKelurahanItem::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $items = $this->attachPotensiImageSources($items);

        return view('potensi_kelurahan', [
            'items' => $items,
        ]);
    }

    public function kontak()
    {
        $raw = KelolaInformasi::query()
            ->where('key', self::KONTAK_NARAHUBUNG_KEY)
            ->value('description');

        return view('kontak', [
            'kontak' => $this->normalizeKontakPayload($this->decodeKontakPayload($raw)),
        ]);
    }

    private function getSections()
    {
        $disk = Storage::disk($this->mediaDisk());

        $sections = KelolaInformasi::query()
            ->whereNotIn('key', [
                self::BERANDA_VIDEO_URL_KEY,
                self::KONTAK_NARAHUBUNG_KEY,
            ])
            ->select(['id', 'title', 'description', 'image_path', 'image_data'])
            ->orderByDesc('created_at')
            ->get();

        $imagesBySectionId = collect();
        $sectionIds = $sections->pluck('id')->all();

        if (!empty($sectionIds)) {
            try {
                $imagesBySectionId = KelolaInformasiImage::query()
                    ->select(['id', 'kelola_informasi_id', 'image_path', 'image_data', 'created_at'])
                    ->whereIn('kelola_informasi_id', $sectionIds)
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('kelola_informasi_id');
            } catch (\Throwable $exception) {
                $imagesBySectionId = collect();
            }
        }

        return $this->attachSectionImageSources($sections, $disk, $imagesBySectionId);
    }

    private function attachSectionImageSources(Collection $sections, $disk, Collection $imagesBySectionId): Collection
    {
        return $sections->map(function (KelolaInformasi $section) use ($disk, $imagesBySectionId) {
            $resolvedImages = collect($imagesBySectionId->get($section->id, []))
                ->map(function (KelolaInformasiImage $image) use ($disk) {
                    return $this->resolveImageSource($disk, $image->image_path, $image->image_data);
                })
                ->filter()
                ->values();

            if ($resolvedImages->isEmpty()) {
                $legacySource = $this->resolveImageSource($disk, $section->image_path, $section->image_data);

                if ($legacySource !== null) {
                    $resolvedImages->push($legacySource);
                }
            }

            $section->image_sources = $resolvedImages;
            $section->image_source = $resolvedImages->first();

            return $section;
        });
    }

    private function attachPotensiImageSources(Collection $items): Collection
    {
        $disk = Storage::disk($this->mediaDisk());
        $imagesByPotensiId = collect();

        if (Schema::hasTable('potensi_kelurahan_images')) {
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
                    return $this->resolveImageSource($disk, $image->image_path, $image->image_data);
                })
                ->filter()
                ->values();

            if ($resolvedImages->isEmpty()) {
                $legacySource = $this->resolveImageSource($disk, $item->image_path, $item->image_data);

                if ($legacySource !== null) {
                    $resolvedImages->push($legacySource);
                }
            }

            $item->image_sources = $resolvedImages;
            $item->image_source = $resolvedImages->first();

            return $item;
        });
    }

    private function resolveHomeVideo(): array
    {
        $videoUrlSetting = KelolaInformasi::query()
            ->where('key', self::BERANDA_VIDEO_URL_KEY)
            ->value('description');

        if (is_string($videoUrlSetting) && $videoUrlSetting !== '') {
            $embeddedUrl = $this->buildEmbeddedVideoUrl($videoUrlSetting);

            if (is_string($embeddedUrl) && $embeddedUrl !== '') {
                return [$embeddedUrl, $this->shouldUseIframePlayer($videoUrlSetting)];
            }
        }

        foreach (['mp4', 'webm', 'ogg', 'mov'] as $extension) {
            $path = 'beranda/video-profil-desa.'.$extension;

            if (Storage::disk($this->mediaDisk())->exists($path)) {
                return [Storage::disk($this->mediaDisk())->url($path), false];
            }
        }

        return [route('public.asset', ['path' => 'video_profil_desa.mp4']), false];
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

    private function extractGoogleDriveFileId(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        if (preg_match('#^https?://drive\.google\.com/file/d/([^/]+)/#i', $url, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('#^https?://drive\.google\.com/open\?id=([^&]+)#i', $url, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('#^https?://drive\.google\.com/uc\?export=download&id=([A-Za-z0-9_-]+)$#i', $url, $matches) === 1) {
            return $matches[1];
        }

        return null;
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

    private function resolveImageSource($disk, $imagePath, $imageData): ?string
    {
        $imageDataValue = is_string($imageData) ? trim($imageData) : '';
        $imagePathValue = is_string($imagePath) ? trim($imagePath) : '';

        if ($imagePathValue !== '') {
            try {
                if ($disk->exists($imagePathValue)) {
                    return $disk->url($imagePathValue);
                }
            } catch (\Throwable $exception) {
                // Fall back to inline image data when storage is unavailable on serverless.
            }
        }

        return $imageDataValue !== '' ? $imageDataValue : null;
    }

}

