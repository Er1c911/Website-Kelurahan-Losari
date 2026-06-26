<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Kelurahan Losari</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: linear-gradient(180deg, #0a2344 0%, #1a4d8f 52%, #8ec5ff 100%);
            min-height: 100vh;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }

        .fade-in-up {
            animation: fadeInUp 0.45s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-left {
            animation: slideInLeft 0.28s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 0.28s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(16px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-16px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="text-gray-800">
    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50 border-b border-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl sm:text-2xl font-bold text-orange-600 hover:opacity-90 transition">
                <img src="{{ asset('logo_losari.png') }}" alt="Logo Losari" class="h-9 sm:h-10 w-auto object-contain">
                <span>Website Kelurahan Losari</span>
            </a>

            <details class="relative">
                <summary class="list-none cursor-pointer inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition select-none">
                    <span>Menu</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0l-4.25-4.51a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                    </svg>
                </summary>

                <div class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg p-2">
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Beranda</a>
                    <a href="{{ route('informasi-desa') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Informasi Desa</a>
                    <a href="#kalender" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">Kalender Kelurahan Losari</a>
                </div>
            </details>
        </div>
    </nav>

    <header class="relative text-white py-14 sm:py-16 px-4 sm:px-6 text-center">
        <div class="relative max-w-4xl mx-auto">
            <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight text-white">Kalender Kelurahan Losari</h1>
            <p class="text-blue-100 mt-4 text-sm sm:text-base">Agenda kegiatan resmi Kelurahan Losari untuk memudahkan warga memantau jadwal pelayanan dan kegiatan lingkungan.</p>
        </div>
    </header>

    <main id="kalender" class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <section class="relative rounded-3xl border border-slate-200/80 bg-white shadow-[0_12px_40px_rgba(15,47,95,0.12)] p-4 sm:p-6 lg:p-8 overflow-hidden">
            <div class="absolute -top-12 right-8 h-28 w-28 rounded-full bg-sky-100 blur-2xl"></div>
            <div class="absolute -bottom-14 left-10 h-28 w-28 rounded-full bg-orange-100 blur-2xl"></div>

            <div class="relative flex flex-col gap-3 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <button id="prevMonthBtn" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600 active:scale-[0.98] transition shadow-sm">
                        <span aria-hidden="true">&larr;</span>
                        Bulan Sebelumnya
                    </button>

                    <h2 id="monthLabel" class="text-lg sm:text-2xl font-extrabold text-blue-900 text-center px-4 py-2 rounded-xl bg-blue-50 border border-blue-100"></h2>

                    <button id="nextMonthBtn" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600 active:scale-[0.98] transition shadow-sm">
                        Bulan Berikutnya
                        <span aria-hidden="true">&rarr;</span>
                    </button>
                </div>

                <div class="flex justify-center">
                    <button id="todayBtn" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-blue-200 bg-white text-blue-700 font-semibold hover:bg-blue-50 transition">
                        Kembali ke Bulan Ini
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-2 mb-3 text-center text-xs sm:text-sm font-semibold text-gray-700">
                <div class="rounded-lg py-2 bg-gray-100">Senin</div>
                <div class="rounded-lg py-2 bg-gray-100">Selasa</div>
                <div class="rounded-lg py-2 bg-gray-100">Rabu</div>
                <div class="rounded-lg py-2 bg-gray-100">Kamis</div>
                <div class="rounded-lg py-2 bg-gray-100">Jumat</div>
                <div class="rounded-lg py-2 bg-gray-100">Sabtu</div>
                <div class="rounded-lg py-2 bg-gray-100">Minggu</div>
            </div>

            <div id="calendarGridWrapper" class="slide-in-left">
                <div id="calendarGrid" class="grid grid-cols-7 gap-2 sm:gap-3"></div>
            </div>
            <p id="calendarError" class="hidden mt-4 text-center text-sm text-red-600"></p>
            <p id="agendaInstruction" class="mt-4 text-center text-sm text-blue-800 font-medium">Klik tanggal pada kalender untuk melihat detail kegiatan.</p>

            <section id="agendaDetailPanel" class="hidden fixed inset-0 z-[70] items-center justify-center p-4 sm:p-6">
                <div id="agendaDetailBackdrop" class="absolute inset-0 bg-slate-900/55 backdrop-blur-sm"></div>

                <div class="relative w-full max-w-2xl rounded-2xl border border-blue-100 bg-white shadow-2xl max-h-[85vh] overflow-y-auto">
                    <div class="sticky top-0 z-10 bg-white border-b border-blue-100 px-4 sm:px-6 py-3 flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-blue-900">Detail Kegiatan</h3>
                            <p id="agendaDetailDate" class="text-sm text-blue-700 mt-1"></p>
                        </div>
                        <button id="agendaDetailCloseBtn" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" aria-label="Tutup detail kegiatan">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div id="agendaDetailList" class="p-4 sm:p-6 space-y-3"></div>
                </div>
            </section>
        </section>
    </main>

    <footer id="kontak" class="bg-gray-900 text-gray-400 py-5 text-center border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-3">
                <h2 class="text-white font-bold text-lg mb-1">Website Resmi Kelurahan Losari</h2>
                <p class="text-sm">Jl. Suropati No. 157 Singosari</p>
            </div>
            <div class="h-px w-full bg-gray-800 mb-3"></div>
            <p class="text-sm">&copy; 2026 Website Kelurahan Losari.</p>
        </div>
    </footer>

    <script>
        (() => {
            const apiUrl = "{{ route('api.kalender-desa') }}";
            const monthLabelEl = document.getElementById('monthLabel');
            const calendarGridEl = document.getElementById('calendarGrid');
            const errorEl = document.getElementById('calendarError');
            const prevBtn = document.getElementById('prevMonthBtn');
            const nextBtn = document.getElementById('nextMonthBtn');
            const todayBtn = document.getElementById('todayBtn');
            const calendarGridWrapperEl = document.getElementById('calendarGridWrapper');
            const agendaInstructionEl = document.getElementById('agendaInstruction');
            const agendaDetailPanelEl = document.getElementById('agendaDetailPanel');
            const agendaDetailBackdropEl = document.getElementById('agendaDetailBackdrop');
            const agendaDetailCloseBtnEl = document.getElementById('agendaDetailCloseBtn');
            const agendaDetailDateEl = document.getElementById('agendaDetailDate');
            const agendaDetailListEl = document.getElementById('agendaDetailList');

            const today = new Date();
            let viewYear = today.getFullYear();
            let viewMonth = today.getMonth() + 1;
            let latestSystemMonthKey = `${today.getFullYear()}-${today.getMonth() + 1}`;
            let dayDataByDate = new Map();
            let selectedDateKey = null;
            let touchStartX = 0;
            let touchStartY = 0;
            let pointerStartX = 0;
            let pointerStartY = 0;
            let pointerTracking = false;
            let wheelAccumulatedX = 0;
            let lastWheelTriggerAt = 0;

            const swipeMinDistance = 50;
            const wheelMinDistance = 70;
            const wheelCooldownMs = 400;

            function buildApiUrl(year, month) {
                const url = new URL(apiUrl, window.location.origin);
                url.searchParams.set('year', year);
                url.searchParams.set('month', month);
                return url.toString();
            }

            function animateGrid(direction) {
                calendarGridWrapperEl.classList.remove('slide-in-left', 'slide-in-right');
                void calendarGridWrapperEl.offsetWidth;
                calendarGridWrapperEl.classList.add(direction === 'prev' ? 'slide-in-right' : 'slide-in-left');
            }

            function renderLoading() {
                monthLabelEl.textContent = 'Memuat kalender...';
                calendarGridEl.innerHTML = '';
                errorEl.classList.add('hidden');
                closeAgendaDetail(false);
                agendaInstructionEl.classList.remove('hidden');
                selectedDateKey = null;
            }

            function renderError(message) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
                closeAgendaDetail(false);
            }

            function closeAgendaDetail(showInstruction = true) {
                agendaDetailPanelEl.classList.remove('flex');
                agendaDetailPanelEl.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                if (showInstruction) {
                    agendaInstructionEl.classList.remove('hidden');
                }
            }

            function formatDateLabel(dateString) {
                return new Intl.DateTimeFormat('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }).format(new Date(`${dateString}T00:00:00`));
            }

            function formatTimeLabel(timeString) {
                if (!timeString || typeof timeString !== 'string') {
                    return null;
                }

                const parts = timeString.split(':');
                if (parts.length < 2) {
                    return null;
                }

                return `${parts[0].padStart(2, '0')}.${parts[1].padStart(2, '0')} WIB`;
            }

            function updateSelectedCellHighlight() {
                calendarGridEl.querySelectorAll('[data-date]').forEach((cellEl) => {
                    const isSelected = cellEl.dataset.date === selectedDateKey;
                    cellEl.classList.toggle('ring-2', isSelected);
                    cellEl.classList.toggle('ring-blue-500', isSelected);
                });
            }

            function showAgendaDetail(dateKey) {
                const dayItem = dayDataByDate.get(dateKey);
                if (!dayItem) {
                    return;
                }

                selectedDateKey = dateKey;
                updateSelectedCellHighlight();

                agendaDetailDateEl.textContent = formatDateLabel(dayItem.date);
                agendaDetailListEl.innerHTML = '';

                if (!dayItem.events || dayItem.events.length === 0) {
                    const emptyEl = document.createElement('p');
                    emptyEl.className = 'text-sm text-gray-600';
                    emptyEl.textContent = 'Belum ada agenda pada tanggal ini.';
                    agendaDetailListEl.appendChild(emptyEl);
                } else {
                    dayItem.events.forEach((event, index) => {
                        const itemEl = document.createElement('article');
                        itemEl.className = 'rounded-xl border border-blue-100 bg-white p-3 sm:p-4';

                        const titleEl = document.createElement('h4');
                        titleEl.className = 'font-bold text-blue-900';
                        titleEl.textContent = `${index + 1}. ${event.title}`;
                        itemEl.appendChild(titleEl);

                        const startTimeLabel = formatTimeLabel(event.start_time);
                        const endTimeLabel = formatTimeLabel(event.end_time);

                        if (startTimeLabel || endTimeLabel) {
                            const timeEl = document.createElement('p');
                            timeEl.className = 'text-xs sm:text-sm text-blue-700 mt-1 font-semibold';

                            if (startTimeLabel && endTimeLabel) {
                                timeEl.textContent = `Waktu: ${startTimeLabel} - ${endTimeLabel}`;
                            } else if (startTimeLabel) {
                                timeEl.textContent = `Mulai: ${startTimeLabel}`;
                            } else {
                                timeEl.textContent = `Selesai: ${endTimeLabel}`;
                            }

                            itemEl.appendChild(timeEl);
                        }

                        if (event.description) {
                            const descEl = document.createElement('p');
                            descEl.className = 'text-sm text-gray-600 mt-1 leading-relaxed';
                            descEl.textContent = event.description;
                            itemEl.appendChild(descEl);
                        }

                        agendaDetailListEl.appendChild(itemEl);
                    });
                }

                agendaDetailPanelEl.classList.remove('hidden');
                agendaDetailPanelEl.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                agendaInstructionEl.classList.add('hidden');
            }

            function createDayCell(dayItem) {
                const { day, is_today: isToday, events, date } = dayItem;
                const cell = document.createElement('div');
                const baseClass = 'aspect-square rounded-xl border flex flex-col items-center justify-center text-sm sm:text-base font-semibold transition-all duration-200 fade-in-up relative cursor-pointer';
                const todayClass = 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-200/80 ring-2 ring-orange-200';
                const normalClass = 'bg-white text-gray-700 border-gray-200 hover:bg-orange-50 hover:border-orange-200';

                cell.className = `${baseClass} ${isToday ? todayClass : normalClass}`;
                cell.dataset.date = date;
                cell.setAttribute('role', 'button');
                cell.setAttribute('tabindex', '0');
                cell.setAttribute('aria-label', `Lihat detail agenda tanggal ${formatDateLabel(date)}`);

                if (events.length > 0) {
                    cell.title = events.map((event) => event.title).join(' | ');
                }

                const dayNumberEl = document.createElement('span');
                dayNumberEl.textContent = day;
                cell.appendChild(dayNumberEl);

                if (events.length > 0) {
                    const badgeEl = document.createElement('span');
                    badgeEl.className = `absolute bottom-1 right-1 text-[10px] sm:text-xs rounded-full px-1.5 py-0.5 font-bold ${isToday ? 'bg-white text-orange-600' : 'bg-blue-600 text-white'}`;
                    badgeEl.textContent = events.length;
                    badgeEl.setAttribute('aria-label', `${events.length} agenda`);
                    cell.appendChild(badgeEl);
                }

                cell.addEventListener('click', () => showAgendaDetail(date));
                cell.addEventListener('keydown', (event) => {
                    if (event.key !== 'Enter' && event.key !== ' ') {
                        return;
                    }

                    event.preventDefault();
                    showAgendaDetail(date);
                });

                return cell;
            }

            function createEmptyCell() {
                const cell = document.createElement('div');
                cell.className = 'aspect-square rounded-xl border border-transparent';
                return cell;
            }

            function renderCalendar(data, direction = 'next') {
                monthLabelEl.textContent = data.month_label;
                errorEl.classList.add('hidden');
                calendarGridEl.innerHTML = '';
                dayDataByDate = new Map(data.days.map((dayItem) => [dayItem.date, dayItem]));
                animateGrid(direction);

                const leadingEmptyDays = Math.max(0, data.first_day_of_week_iso - 1);
                for (let i = 0; i < leadingEmptyDays; i++) {
                    calendarGridEl.appendChild(createEmptyCell());
                }

                data.days.forEach((dayItem) => {
                    calendarGridEl.appendChild(createDayCell(dayItem));
                });

                closeAgendaDetail(false);
                agendaInstructionEl.classList.remove('hidden');
                selectedDateKey = null;
                updateSelectedCellHighlight();
            }

            async function loadCalendar(year, month, direction = 'next') {
                renderLoading();
                try {
                    const response = await fetch(buildApiUrl(year, month), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data kalender.');
                    }

                    const data = await response.json();
                    renderCalendar(data, direction);
                } catch (error) {
                    monthLabelEl.textContent = 'Kalender tidak tersedia';
                    calendarGridEl.innerHTML = '';
                    renderError(error.message);
                }
            }

            function shiftMonth(offset) {
                viewMonth += offset;

                if (viewMonth > 12) {
                    viewMonth = 1;
                    viewYear += 1;
                } else if (viewMonth < 1) {
                    viewMonth = 12;
                    viewYear -= 1;
                }

                loadCalendar(viewYear, viewMonth, offset < 0 ? 'prev' : 'next');
            }

            function handleSwipe() {
                calendarGridWrapperEl.style.touchAction = 'pan-y';

                calendarGridWrapperEl.addEventListener('touchstart', (event) => {
                    if (!event.changedTouches || event.changedTouches.length === 0) {
                        return;
                    }

                    touchStartX = event.changedTouches[0].clientX;
                    touchStartY = event.changedTouches[0].clientY;
                }, { passive: true });

                calendarGridWrapperEl.addEventListener('touchend', (event) => {
                    if (!event.changedTouches || event.changedTouches.length === 0) {
                        return;
                    }

                    const touchEndX = event.changedTouches[0].clientX;
                    const touchEndY = event.changedTouches[0].clientY;
                    const deltaX = touchEndX - touchStartX;
                    const deltaY = touchEndY - touchStartY;

                    if (Math.abs(deltaX) < swipeMinDistance || Math.abs(deltaX) <= Math.abs(deltaY)) {
                        return;
                    }

                    if (deltaX < 0) {
                        shiftMonth(1);
                    } else {
                        shiftMonth(-1);
                    }
                }, { passive: true });

                // Touchpad swipe on laptop usually appears as horizontal wheel deltas.
                calendarGridWrapperEl.addEventListener('wheel', (event) => {
                    if (Math.abs(event.deltaX) <= Math.abs(event.deltaY)) {
                        return;
                    }

                    wheelAccumulatedX += event.deltaX;

                    const nowMs = Date.now();
                    if (Math.abs(wheelAccumulatedX) < wheelMinDistance || (nowMs - lastWheelTriggerAt) < wheelCooldownMs) {
                        return;
                    }

                    if (wheelAccumulatedX > 0) {
                        shiftMonth(1);
                    } else {
                        shiftMonth(-1);
                    }

                    wheelAccumulatedX = 0;
                    lastWheelTriggerAt = nowMs;
                }, { passive: true });

                // Fallback gesture for pointer drag (mouse/pen/touch).
                calendarGridWrapperEl.addEventListener('pointerdown', (event) => {
                    pointerTracking = true;
                    pointerStartX = event.clientX;
                    pointerStartY = event.clientY;
                });

                calendarGridWrapperEl.addEventListener('pointerup', (event) => {
                    if (!pointerTracking) {
                        return;
                    }

                    pointerTracking = false;

                    const deltaX = event.clientX - pointerStartX;
                    const deltaY = event.clientY - pointerStartY;

                    if (Math.abs(deltaX) < swipeMinDistance || Math.abs(deltaX) <= Math.abs(deltaY)) {
                        return;
                    }

                    if (deltaX < 0) {
                        shiftMonth(1);
                    } else {
                        shiftMonth(-1);
                    }
                });

                calendarGridWrapperEl.addEventListener('pointercancel', () => {
                    pointerTracking = false;
                });
            }

            prevBtn.addEventListener('click', () => shiftMonth(-1));
            nextBtn.addEventListener('click', () => shiftMonth(1));

            agendaDetailCloseBtnEl.addEventListener('click', () => closeAgendaDetail(true));
            agendaDetailBackdropEl.addEventListener('click', () => closeAgendaDetail(true));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !agendaDetailPanelEl.classList.contains('hidden')) {
                    closeAgendaDetail(true);
                }
            });

            todayBtn.addEventListener('click', () => {
                const now = new Date();
                const oldYear = viewYear;
                const oldMonth = viewMonth;

                viewYear = now.getFullYear();
                viewMonth = now.getMonth() + 1;

                const oldValue = oldYear * 12 + oldMonth;
                const newValue = viewYear * 12 + viewMonth;
                const direction = newValue < oldValue ? 'prev' : 'next';

                loadCalendar(viewYear, viewMonth, direction);
            });

            setInterval(() => {
                const now = new Date();
                const monthKey = `${now.getFullYear()}-${now.getMonth() + 1}`;

                if (monthKey !== latestSystemMonthKey) {
                    latestSystemMonthKey = monthKey;
                    viewYear = now.getFullYear();
                    viewMonth = now.getMonth() + 1;
                    loadCalendar(viewYear, viewMonth, 'next');
                }
            }, 60000);

            handleSwipe();
            loadCalendar(viewYear, viewMonth, 'next');
        })();
    </script>
</body>
</html>
