<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\AgendaKalender;

// Halaman Publik (User biasa tanpa login)
Route::get('/', [\App\Http\Controllers\KelolaInformasiController::class, 'index'])->name('home');
Route::get('/informasi-desa', [\App\Http\Controllers\KelolaInformasiController::class, 'informasiDesa'])
    ->name('informasi-desa');
Route::view('/kalender-desa', 'kalender_desa')->name('kalender-desa');
Route::get('/api/kalender-desa', function (\Illuminate\Http\Request $request) {
    $now = now(config('app.timezone'));

    $year = (int) $request->query('year', $now->year);
    $month = (int) $request->query('month', $now->month);

    if ($month < 1 || $month > 12 || $year < 1970 || $year > 2100) {
        return response()->json([
            'message' => 'Parameter tahun atau bulan tidak valid.',
        ], 422);
    }

    $firstDay = \Carbon\CarbonImmutable::create($year, $month, 1, 0, 0, 0, config('app.timezone'));
    $daysInMonth = $firstDay->daysInMonth;
    $today = now(config('app.timezone'));

    $eventsByDate = [];
    $agendaBulanan = AgendaKalender::query()
        ->whereYear('event_date', $year)
        ->whereMonth('event_date', $month)
        ->orderBy('event_date')
        ->orderBy('start_time')
        ->orderBy('title')
        ->get();

    foreach ($agendaBulanan as $agenda) {
        $eventsByDate[$agenda->event_date][] = [
            'title' => $agenda->title,
            'start_time' => $agenda->start_time,
            'end_time' => $agenda->end_time,
            'description' => $agenda->description,
            'category' => 'agenda',
        ];
    }

    $days = [];
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = $firstDay->setDay($day);
        $days[] = [
            'date' => $date->toDateString(),
            'day' => $day,
            'is_today' => $date->isSameDay($today),
            'day_of_week_iso' => $date->dayOfWeekIso,
            'events' => $eventsByDate[$date->toDateString()] ?? [],
        ];
    }

    return response()->json([
        'year' => $year,
        'month' => $month,
        'month_label' => $firstDay->locale('id')->translatedFormat('F Y'),
        'first_day_of_week_iso' => $firstDay->dayOfWeekIso,
        'event_categories' => [
            'agenda' => 'Agenda Kelurahan',
        ],
        'days' => $days,
    ]);
})->name('api.kalender-desa');

// Proses Otentikasi Admin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Area Dashboard Admin (Diproteksi Auth Middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'index'])
        ->name('dashboard');

    // Halaman kelola informasi desa
    Route::get('/admin/kelola-informasi', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'manage'])
        ->name('admin.kelola-informasi.manage');

    // Halaman kelola kalender kelurahan
    Route::get('/admin/kelola-kalender', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'manageKalender'])
        ->name('admin.kelola-kalender.manage');

    // Halaman kelola beranda
    Route::get('/admin/kelola-beranda', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'manageBeranda'])
        ->name('admin.kelola-beranda.manage');
    Route::post('/admin/kelola-beranda/video', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'updateBerandaVideo'])
        ->name('admin.kelola-beranda.video.update');
    Route::delete('/admin/kelola-beranda/video', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'destroyBerandaVideo'])
        ->name('admin.kelola-beranda.video.destroy');

    // Endpoint CRUD pengelolaan kalender kelurahan
    Route::post('/admin/kelola-kalender', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'storeKalender'])
        ->name('admin.kelola-kalender.store');
    Route::put('/admin/kelola-kalender/{agenda}', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'updateKalender'])
        ->name('admin.kelola-kalender.update');
    Route::delete('/admin/kelola-kalender/{agenda}', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'destroyKalender'])
        ->name('admin.kelola-kalender.destroy');

    // Endpoint CRUD pengelolaan informasi desa
    Route::post('/admin/kelola-informasi', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'store'])
        ->name('admin.kelola-informasi.store');
    Route::put('/admin/kelola-informasi/{informasi}', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'update'])
        ->name('admin.kelola-informasi.update');
    Route::delete('/admin/kelola-informasi/{informasi}', [\App\Http\Controllers\AdminKelolaInformasiController::class, 'destroy'])
        ->name('admin.kelola-informasi.destroy');
});