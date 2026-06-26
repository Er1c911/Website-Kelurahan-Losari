<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KelolaBerandaVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_replace_homepage_video(): void
    {
        Storage::fake('public');

        $user = User::forceCreate([
            'name' => 'Admin Losari',
            'username' => 'admin-losari',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->post(route('admin.kelola-beranda.video.update'), [
            'video' => UploadedFile::fake()->create('profil-baru.mp4', 2048, 'video/mp4'),
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('status', 'Video beranda berhasil diperbarui.');

        Storage::disk('public')->assertExists('beranda/video-profil-desa.mp4');
    }

    public function test_homepage_uses_uploaded_video_when_available(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('beranda/video-profil-desa.mp4', 'video-content');

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('/storage/beranda/video-profil-desa.mp4', false);
    }

    public function test_admin_can_remove_custom_homepage_video(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('beranda/video-profil-desa.mp4', 'video-content');

        $user = User::forceCreate([
            'name' => 'Admin Losari',
            'username' => 'admin-losari',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->delete(route('admin.kelola-beranda.video.destroy'));

        $response
            ->assertRedirect()
            ->assertSessionHas('status', 'Video beranda berhasil dihapus. Halaman publik kembali memakai video bawaan.');

        Storage::disk('public')->assertMissing('beranda/video-profil-desa.mp4');
    }
}