<?php

namespace App\Http\Controllers;

use App\Models\KelolaInformasi;
use App\Models\KelolaInformasiImage;
use App\Models\PotensiKelurahanImage;
use App\Models\PotensiKelurahanItem;
use App\Models\Umkm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
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

    public function informasiImage(KelolaInformasiImage $image)
    {
        return $this->imageResponse($image->image_path, $image->image_data);
    }

    public function informasiLegacyImage(KelolaInformasi $informasi)
    {
        return $this->imageResponse($informasi->image_path, $informasi->image_data);
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

    public function potensiImage(PotensiKelurahanImage $image)
    {
        return $this->imageResponse($image->image_path, $image->image_data);
    }

    public function potensiLegacyImage(PotensiKelurahanItem $potensi)
    {
        return $this->imageResponse($potensi->image_path, $potensi->image_data);
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
                    return $this->resolveImageSource(
                        $disk,
                        $image->image_path,
                        route('informasi.image', ['image' => $image])
                    );
                })
                ->filter()
                ->values();

            if ($resolvedImages->isEmpty()) {
                $legacySource = $this->resolveImageSource(
                    $disk,
                    $section->image_path,
                    route('informasi.legacy-image', ['informasi' => $section])
                );

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
                    return $this->resolveImageSource(
                        $disk,
                        $image->image_path,
                        route('potensi.image', ['image' => $image])
                    );
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

    private function resolveImageSource($disk, $imagePath, ?string $fallbackUrl = null): ?string
    {
        $imagePathValue = is_string($imagePath) ? trim($imagePath) : '';

        if ($this->isExternalUrl($imagePathValue)) {
            $normalizedExternalUrl = $this->normalizeExternalImageUrl($imagePathValue);

            // Google Drive images are often blocked by CORP in direct <img> usage, so proxy via our app when possible.
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
            } catch (\Throwable $exception) {
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

    private function imageResponse($imagePath, $imageData)
    {
        $disk = Storage::disk($this->mediaDisk());
        $imagePathValue = is_string($imagePath) ? trim($imagePath) : '';

        if ($this->isExternalUrl($imagePathValue)) {
            $externalUrl = $this->normalizeExternalImageUrl($imagePathValue);

            try {
                $externalResponse = Http::timeout(20)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                    ])
                    ->get($externalUrl);

                if ($externalResponse->successful()) {
                    $contentType = (string) ($externalResponse->header('Content-Type') ?? 'application/octet-stream');

                    return response($externalResponse->body(), 200, [
                        'Content-Type' => $contentType,
                        'Cache-Control' => 'public, max-age=86400',
                    ]);
                }
            } catch (\Throwable $exception) {
                // Continue to local-storage / inline fallback below.
            }
        }

        if ($imagePathValue !== '') {
            try {
                if ($disk->exists($imagePathValue)) {
                    return $disk->response($imagePathValue);
                }
            } catch (\Throwable $exception) {
                // Continue to image_data fallback.
            }
        }

        $imageDataValue = is_string($imageData) ? trim($imageData) : '';

        if ($imageDataValue === '' || preg_match('/^data:([^;]+);base64,(.+)$/', $imageDataValue, $matches) !== 1) {
            abort(404);
        }

        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            abort(404);
        }

        return response($binary, 200, [
            'Content-Type' => $matches[1],
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

}

