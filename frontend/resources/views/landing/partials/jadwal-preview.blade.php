@extends('landing.index')

@section('section-top')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <h1>Preview Jadwal</h1>
                    <ul>
                        <li><a href="{{ route('landing') }}">Home</a></li>
                        <li> / Jadwal</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <!-- START FILTER -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="filter-tabs-wrapper">
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="full">
                                <i class="fa-solid fa-table"></i> Full Jadwal
                            </button>
                            <button class="filter-btn" data-filter="kelas">
                                <i class="fa-solid fa-building"></i> Berdasarkan Kelas
                            </button>
                            <button class="filter-btn" data-filter="guru">
                                <i class="fa-solid fa-chalkboard-user"></i> Berdasarkan Guru
                            </button>
                        </div>

                        <!-- Filter Full (tidak ada filter) -->
                        <div class="filter-panel active" id="filter-full">
                            <div class="text-center text-muted">
                                <i class="fa-solid fa-info-circle"></i> Menampilkan jadwal lengkap semua kelas
                            </div>
                        </div>

                        <!-- Filter Kelas -->
                        <div class="filter-panel" id="filter-kelas">
                            <div class="row">
                                <div class="col-lg-4 offset-lg-4">
                                    <div class="form-group">
                                        <label>Pilih Kelas</label>
                                        <select id="kelasSelect" class="form-control">
                                            @foreach ($kelasList as $kelas)
                                                <option value="{{ $kelas->id_kelas }}">
                                                    {{ $kelas->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Guru -->
                        <div class="filter-panel" id="filter-guru">
                            <div class="row">
                                <div class="col-lg-4 offset-lg-4">
                                    <div class="form-group">
                                        <label>Pilih Guru</label>
                                        <select id="guruSelect" class="form-control">
                                            @foreach ($guruList as $guru)
                                                <option value="{{ $guru->id_guru }}">
                                                    {{ $guru->nama_guru }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END FILTER -->

    <!-- START FULL JADWAL -->
    <section class="section-padding bg-light" id="jadwal-full-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h4>Jadwal Lengkap</h4>
                        <h1>Full Jadwal Pelajaran</h1>
                        <p>Jadwal semua kelas dalam satu tampilan</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="jadwal-table-wrapper" id="jadwalFullContainer">
                        @include('landing.partials.jadwal-full-table', ['fullJadwal' => $fullJadwal])
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- START JADWAL BERDASARKAN KELAS -->
    <section class="section-padding" id="jadwal-kelas-section" style="display: none;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h4>Jadwal Kelas</h4>
                        <h1 id="kelasTitle">Jadwal {{ $kelasList->first()->nama_kelas ?? '' }}</h1>
                        <p>Jadwal pelajaran lengkap per kelas</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="jadwal-table-wrapper" id="jadwalKelasContainer">
                        <div class="text-center py-5">
                            <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                            <p>Pilih kelas untuk melihat jadwal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- START JADWAL BERDASARKAN GURU -->
    <section class="section-padding bg-light" id="jadwal-guru-section" style="display: none;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h4>Jadwal Guru</h4>
                        <h1 id="guruTitle">Jadwal {{ $guruList->first()->nama_guru ?? '' }}</h1>
                        <p>Jadwal mengajar guru per kelas</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="jadwal-table-wrapper" id="jadwalGuruContainer">
                        <div class="text-center py-5">
                            <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                            <p>Pilih guru untuk melihat jadwal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .filter-tabs-wrapper {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: none;
            border: none;
            padding: 12px 35px;
            font-size: 16px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 12px;
        }

        .filter-btn:hover {
            background: #f1f5f9;
            color: #667eea;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
        }

        .filter-panel {
            display: none;
        }

        .filter-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .jadwal-table-wrapper {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .jadwal-table th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
        }

        .jadwal-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .jadwal-table tr:hover {
            background: #f8fafc;
        }

        .keterangan-cell {
            background: #e8f4fd;
        }

        .keterangan-cell.kuning-cerah {
            background: #fff3cd;
        }

        .keterangan-text {
            font-weight: bold;
            color: #0066cc;
        }

        .keterangan-cell.kuning-cerah .keterangan-text {
            color: #856404;
        }

        .guru-jadwal-card {
            background: white;
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .guru-jadwal-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 15px 20px;
        }

        .guru-jadwal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .guru-jadwal-body {
            padding: 20px;
            overflow-x: auto;
        }

        .guru-table {
            width: 100%;
            border-collapse: collapse;
        }

        .guru-table th {
            background: #f1f5f9;
            padding: 10px;
            text-align: center;
            font-weight: 600;
        }

        .guru-table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .bg-light {
            background-color: #f8fafc;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        .jam-cell {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .filter-btn {
                padding: 8px 20px;
                font-size: 14px;
            }

            .jadwal-table {
                font-size: 10px;
            }

            .jadwal-table th,
            .jadwal-table td {
                padding: 6px 4px;
            }
        }
    </style>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi semua event listener
        initFilterButtons();
        initKelasSelect();
        initGuruSelect();

        // Load default Full Jadwal
        loadFullJadwal();
    });

    // Initialize Filter Buttons
    function initFilterButtons() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            // Hapus event listener lama jika ada
            btn.removeEventListener('click', handleFilterClick);
            btn.addEventListener('click', handleFilterClick);
        });
    }

    function handleFilterClick(e) {
        const btn = e.currentTarget;
        const filter = btn.dataset.filter;

        // Update active class
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Update filter panel
        document.querySelectorAll('.filter-panel').forEach(panel => panel.classList.remove('active'));
        const activePanel = document.getElementById(`filter-${filter}`);
        if (activePanel) activePanel.classList.add('active');

        // Hide all sections
        const fullSection = document.getElementById('jadwal-full-section');
        const kelasSection = document.getElementById('jadwal-kelas-section');
        const guruSection = document.getElementById('jadwal-guru-section');

        if (fullSection) fullSection.style.display = 'none';
        if (kelasSection) kelasSection.style.display = 'none';
        if (guruSection) guruSection.style.display = 'none';

        // Show selected section
        if (filter === 'full') {
            if (fullSection) fullSection.style.display = 'block';
            loadFullJadwal();
        } else if (filter === 'kelas') {
            if (kelasSection) kelasSection.style.display = 'block';
            const kelasSelect = document.getElementById('kelasSelect');
            if (kelasSelect && kelasSelect.value) {
                loadJadwalByKelas(kelasSelect.value);
            } else if (kelasSelect) {
                // Trigger change to load default
                kelasSelect.dispatchEvent(new Event('change'));
            }
        } else if (filter === 'guru') {
            if (guruSection) guruSection.style.display = 'block';
            const guruSelect = document.getElementById('guruSelect');
            if (guruSelect && guruSelect.value) {
                loadJadwalByGuru(guruSelect.value);
            } else if (guruSelect) {
                // Trigger change to load default
                guruSelect.dispatchEvent(new Event('change'));
            }
        }
    }

    // Initialize Kelas Select
    function initKelasSelect() {
        const kelasSelect = document.getElementById('kelasSelect');
        if (kelasSelect) {
            // Hapus event listener lama
            kelasSelect.removeEventListener('change', handleKelasChange);
            kelasSelect.addEventListener('change', handleKelasChange);
        }
    }

    function handleKelasChange(e) {
        const idKelas = e.target.value;
        const selectedOption = e.target.options[e.target.selectedIndex];
        const namaKelas = selectedOption.text;

        const kelasTitle = document.getElementById('kelasTitle');
        if (kelasTitle) {
            kelasTitle.innerHTML = `Jadwal ${namaKelas}`;
        }

        if (idKelas) {
            loadJadwalByKelas(idKelas);
        }
    }

    // Initialize Guru Select
    function initGuruSelect() {
        const guruSelect = document.getElementById('guruSelect');
        if (guruSelect) {
            // Hapus event listener lama
            guruSelect.removeEventListener('change', handleGuruChange);
            guruSelect.addEventListener('change', handleGuruChange);
        }
    }

    function handleGuruChange(e) {
        const idGuru = e.target.value;
        const selectedOption = e.target.options[e.target.selectedIndex];
        const namaGuru = selectedOption.text;

        const guruTitle = document.getElementById('guruTitle');
        if (guruTitle) {
            guruTitle.innerHTML = `Jadwal ${namaGuru}`;
        }

        if (idGuru) {
            loadJadwalByGuru(idGuru);
        }
    }

    // Load Full Jadwal
    function loadFullJadwal() {
        const container = document.getElementById('jadwalFullContainer');
        if (!container) return;

        container.innerHTML = `<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p>Memuat jadwal...</p></div>`;

        fetch('{{ route("api.jadwal.full") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    renderFullJadwal(data.data);
                } else {
                    container.innerHTML = `<div class="alert alert-warning text-center">${data.message || 'Belum ada jadwal'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `<div class="alert alert-danger text-center">Terjadi kesalahan saat memuat data</div>`;
            });
    }

    // Load Jadwal By Kelas
    function loadJadwalByKelas(idKelas) {
        const container = document.getElementById('jadwalKelasContainer');
        if (!container) return;

        container.innerHTML = `<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p>Memuat jadwal...</p></div>`;

        fetch(`{{ route("api.jadwal.by-kelas") }}?id_kelas=${idKelas}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    renderJadwalByKelas(data.data);
                } else {
                    container.innerHTML = `<div class="alert alert-warning text-center">${data.message || 'Belum ada jadwal untuk kelas ini'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `<div class="alert alert-danger text-center">Terjadi kesalahan saat memuat data</div>`;
            });
    }

    // Load Jadwal By Guru
    function loadJadwalByGuru(idGuru) {
        const container = document.getElementById('jadwalGuruContainer');
        if (!container) return;

        container.innerHTML = `<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p>Memuat jadwal...</p></div>`;

        fetch(`{{ route("api.jadwal.by-guru") }}?id_guru=${idGuru}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    renderJadwalByGuru(data.data);
                } else {
                    container.innerHTML = `<div class="alert alert-warning text-center">${data.message || 'Belum ada jadwal untuk guru ini'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `<div class="alert alert-danger text-center">Terjadi kesalahan saat memuat data</div>`;
            });
    }

    // Render Full Jadwal
    function renderFullJadwal(data) {
        const container = document.getElementById('jadwalFullContainer');
        if (!container) return;

        const kelasList = data.kelas_list;
        const jadwalData = data.jadwal_data;
        const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        if (!kelasList || kelasList.length === 0) {
            container.innerHTML = `<div class="alert alert-warning text-center">Belum ada data kelas</div>`;
            return;
        }

        let html = `
            <table class="jadwal-table">
                <thead>
                    <tr>
                        <th style="min-width: 80px">Hari / Jam</th>
                        <th style="min-width: 80px">Waktu</th>
        `;

        kelasList.forEach(kelas => {
            html += `<th style="min-width: 150px">${kelas.nama_kelas}</th>`;
        });

        html += `</thead><tbody>`;

        for (const hari of hariList) {
            const rows = jadwalData[hari] || [];
            if (rows.length === 0) {
                html += `<tr><td colspan="${kelasList.length + 2}" class="text-center text-muted">Tidak ada jadwal untuk hari ${hari}</td></tr>`;
            } else {
                rows.forEach((row, idx) => {
                    const waktuMulai = row.waktu_mulai ? row.waktu_mulai.substring(0,5) : '';
                    const waktuSelesai = row.waktu_selesai ? row.waktu_selesai.substring(0,5) : '';

                    html += `<tr>`;
                    if (idx === 0) {
                        html += `<td rowspan="${rows.length}"><strong>${hari}</strong></td>`;
                    }
                    html += `<td class="jam-cell">${row.jam_ke}<br><small>${waktuMulai}-${waktuSelesai}</small></td>`;

                    if (row.is_keterangan) {
                        const isKuning = row.keterangan === 'Istirahat' || row.keterangan === 'Ishoma';
                        html += `<td colspan="${kelasList.length}" class="keterangan-cell ${isKuning ? 'kuning-cerah' : ''}">
                                    <div class="keterangan-text"><strong>${row.keterangan}</strong></div>
                                  </td>`;
                    } else {
                        row.kelas_data.forEach(kelasItem => {
                            if (kelasItem.nama_mapel === '-') {
                                html += `<td class="text-muted">-</td>`;
                            } else {
                                html += `<td>
                                            <strong>${kelasItem.nama_mapel}</strong><br>
                                            <small>${kelasItem.nama_guru}</small>
                                        </td>`;
                            }
                        });
                    }
                    html += `</tr>`;
                });
            }
        }

        html += `</tbody></table>`;
        container.innerHTML = html;
    }

    // Render Jadwal By Kelas
    function renderJadwalByKelas(jadwal) {
        const container = document.getElementById('jadwalKelasContainer');
        if (!container) return;

        const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        if (!jadwal || jadwal.length === 0) {
            container.innerHTML = `<div class="alert alert-warning text-center">Belum ada jadwal untuk kelas ini</div>`;
            return;
        }

        let html = `
            <table class="jadwal-table">
                <thead>
                    <tr>
                        <th style="width: 100px">Hari</th>
                        <th style="width: 120px">Jam / Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th style="width: 150px">Guru</th>
                    </tr>
                </thead>
                <tbody>
        `;

        let hariData = {};
        jadwal.forEach(item => {
            if (!hariData[item.hari]) hariData[item.hari] = [];
            hariData[item.hari].push(item);
        });

        for (const hari of hariList) {
            const items = hariData[hari] || [];
            if (items.length === 0) {
                html += `<tr><td colspan="4" class="text-center text-muted">Tidak ada jadwal</td></tr>`;
            } else {
                items.forEach((item, idx) => {
                    const waktuMulai = item.waktu_mulai ? item.waktu_mulai.substring(0,5) : '';
                    const waktuSelesai = item.waktu_selesai ? item.waktu_selesai.substring(0,5) : '';
                    const isKeterangan = item.is_keterangan;

                    html += `<tr>`;
                    if (idx === 0) {
                        html += `<td rowspan="${items.length}"><strong>${hari}</strong></td>`;
                    }
                    html += `<td class="jam-cell">${item.jam_ke}<br><small>${waktuMulai}-${waktuSelesai}</small></td>`;

                    if (isKeterangan) {
                        const isKuning = item.nama_mapel === 'Istirahat' || item.nama_mapel === 'Ishoma';
                        html += `<td colspan="2" class="keterangan-cell ${isKuning ? 'kuning-cerah' : ''}">
                                    <div class="keterangan-text"><strong>${item.nama_mapel}</strong></div>
                                </td>`;
                    } else {
                        html += `<td><strong>${item.nama_mapel}</strong></td>`;
                        html += `<td>${item.nama_guru}</td>`;
                    }
                    html += `</tr>`;
                });
            }
        }

        html += `</tbody></table>`;
        container.innerHTML = html;
    }

    // Render Jadwal By Guru
    function renderJadwalByGuru(jadwalData) {
        const container = document.getElementById('jadwalGuruContainer');
        if (!container) return;

        let html = '';
        const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        if (Object.keys(jadwalData).length === 0) {
            container.innerHTML = `<div class="alert alert-info text-center">Tidak ada jadwal untuk guru ini</div>`;
            return;
        }

        for (const [kelas, jadwal] of Object.entries(jadwalData)) {
            html += `
                <div class="guru-jadwal-card">
                    <div class="guru-jadwal-header">
                        <h3><i class="fa-solid fa-building"></i> Kelas ${kelas}</h3>
                    </div>
                    <div class="guru-jadwal-body">
                        <table class="guru-table">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            // Kelompokkan per hari
            let hariGroup = {};
            jadwal.forEach(item => {
                if (!hariGroup[item.hari]) hariGroup[item.hari] = [];
                hariGroup[item.hari].push(item);
            });

            for (const hari of hariList) {
                const items = hariGroup[hari] || [];
                if (items.length === 0) {
                    html += `<tr class="text-muted"><td colspan="3" class="text-center">Tidak ada jadwal</td></tr>`;
                } else {
                    items.forEach((item, idx) => {
                        const waktuMulai = item.waktu_mulai ? item.waktu_mulai.substring(0,5) : '';
                        const waktuSelesai = item.waktu_selesai ? item.waktu_selesai.substring(0,5) : '';

                        html += `<tr>`;
                        if (idx === 0) {
                            html += `<td rowspan="${items.length}"><strong>${hari}</strong></td>`;
                        }
                        html += `<td>${item.jam_ke}<br><small>${waktuMulai}-${waktuSelesai}</small></td>`;
                        html += `<td><strong>${item.nama_mapel}</strong></td>`;
                        html += `</tr>`;
                    });
                }
            }

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }

        container.innerHTML = html;
    }
</script>
