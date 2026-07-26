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
        return view('welcome_user', [
            'homeVideoUrl' => $this->getHomeVideoUrl(),
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

    private function getHomeVideoUrl(): string
    {
        $videoUrlSetting = KelolaInformasi::query()
            ->where('key', self::BERANDA_VIDEO_URL_KEY)
            ->value('description');

        if (is_string($videoUrlSetting) && $videoUrlSetting !== '') {
            return $videoUrlSetting;
        }

        foreach (['mp4', 'webm', 'ogg', 'mov'] as $extension) {
            $path = 'beranda/video-profil-desa.'.$extension;

            if (Storage::disk($this->mediaDisk())->exists($path)) {
                return Storage::disk($this->mediaDisk())->url($path);
            }
        }

        return route('public.asset', ['path' => 'video_profil_desa.mp4']);
    }

}

