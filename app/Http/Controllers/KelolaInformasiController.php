<?php

namespace App\Http\Controllers;

use App\Models\KelolaInformasi;
use Illuminate\Support\Facades\Storage;

class KelolaInformasiController extends Controller
{
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

    private function getSections()
    {
        return KelolaInformasi::query()
            ->orderByDesc('created_at')
            ->get();
    }

    private function getHomeVideoUrl(): string
    {
        foreach (['mp4', 'webm', 'ogg', 'mov'] as $extension) {
            $path = 'beranda/video-profil-desa.'.$extension;

            if (Storage::disk($this->mediaDisk())->exists($path)) {
                return Storage::disk($this->mediaDisk())->url($path);
            }
        }

        return asset('video_profil_desa.mp4');
    }

}

