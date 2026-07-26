<?php

namespace App\Http\Controllers;

use App\Models\KelolaInformasi;
use App\Models\Umkm;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class KelolaInformasiController extends Controller
{
    private const BERANDA_VIDEO_URL_KEY = '__beranda_video_url__';

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

    private function getSections()
    {
        return KelolaInformasi::query()
            ->where('key', '!=', self::BERANDA_VIDEO_URL_KEY)
            ->orderByDesc('created_at')
            ->get();
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

}

