@extends('admin')

<style>
    .bentrok {
        background: #ffb3b3;
        color: #900;
        font-weight: bold;
        border-radius: 4px;
        padding: 4px;
    }

    .keterangan-cell {
        text-align: center;
        vertical-align: middle;
    }

    /* Warna untuk Istirahat dan Ishoma - Kuning Cerah */
    .keterangan-cell.kuning-cerah {
        background: #fff3cd;
    }

    .keterangan-cell.kuning-cerah .keterangan-text {
        color: #856404;
    }

    /* Warna untuk keterangan lainnya - Biru Cerah */
    .keterangan-cell.biru-cerah {
        background: #e8f4fd;
    }

    .keterangan-cell.biru-cerah .keterangan-text {
        color: #0066cc;
    }

    .keterangan-text {
        font-weight: bold;
    }

    .empty-cell {
        background: #f5f5f5;
        color: #999;
    }

    table {
        font-size: 12px;
    }

    td {
        vertical-align: middle;
        padding: 8px !important;
    }

    /* Style untuk tab */
    .tab-container {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .tab-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0;
    }

    .tab-btn {
        padding: 10px 20px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s;
        border-radius: 5px 5px 0 0;
    }

    .tab-btn:hover {
        background: #f8f9fa;
        color: #007bff;
    }

    .tab-btn.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background: #f8f9fa;
    }

    .tab-content {
        display: none;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 0 0 10px 10px;
        border: 1px solid #dee2e6;
        border-top: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Style untuk analisis */
    .analysis-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .analysis-table th {
        background: #007bff;
        color: white;
        padding: 10px;
        text-align: center;
    }

    .analysis-table td {
        padding: 8px;
        text-align: center;
    }

    .status-ok {
        color: green;
        font-weight: bold;
    }

    .status-mismatch {
        color: red;
        font-weight: bold;
    }

    .guru-beban-table {
        margin-top: 20px;
    }

    .progress-bar {
        width: 100%;
        background-color: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        background-color: #28a745;
        height: 20px;
        border-radius: 10px;
        transition: width 0.3s;
        color: white;
        font-size: 10px;
        line-height: 20px;
        padding-left: 5px;
    }

    .progress-fill.warning {
        background-color: #ffc107;
        color: #333;
    }

    .progress-fill.danger {
        background-color: #dc3545;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 5px;
    }

    .bg-success {
        background-color: #28a745;
        color: white;
    }

    .bg-warning {
        background-color: #ffc107;
        color: #333;
    }

    .bg-danger {
        background-color: #dc3545;
        color: white;
    }

    .bg-info {
        background-color: #17a2b8;
        color: white;
    }
</style>

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Generate Jadwal</h3>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tombol Generate -->
    <div class="mb-3">
        @if (empty($jadwal))
            {{-- Kalau belum ada jadwal --}}
            <a href="{{ route('generate-jadwal.run') }}" class="btn btn-success">
                <i class="mdi mdi-play"></i> Generate Jadwal
            </a>
        @else
            {{-- Kalau sudah ada jadwal --}}
            <a href="{{ route('generate-jadwal.run') }}" class="btn btn-primary"
                onclick="return confirm('Generate ulang jadwal? Data jadwal saat ini akan hilang.')">
                <i class="mdi mdi-refresh"></i> Generate Ulang Jadwal
            </a>
        @endif
    </div>

    @if (isset($jadwal) && !empty($jadwal))
        <form method="POST" action="{{ route('generate-jadwal.simpan') }}">
            @csrf

            <div class="alert alert-info mt-2">
                <b>Hasil Genetic Algorithm</b><br>
                Fitness Terbaik : <b>{{ $fitness ?? 'N/A' }}</b><br>
                Jumlah Generasi : <b>{{ $generasi ?? 'N/A' }}</b><br>
                Total Data : <b>{{ count($jadwal) }}</b>
            </div>

            @if (isset($fitness_history) && count($fitness_history) > 0)
                <div style="width:100%;max-width:800px;margin:auto">
                    <canvas id="fitnessChart"></canvas>
                </div>
            @endif

            <div class="row mb-3 mt-3">
                <div class="col-md-3">
                    <label>Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026" required>
                </div>
                <div class="col-md-3">
                    <label>Semester</label>
                    <select name="semester" class="form-control" required>
                        <option value="">Pilih</option>
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control">
                </div>
            </div>

            @php
                // Fungsi helper untuk menentukan warna berdasarkan keterangan
                function getWarnaByKeterangan($teks)
                {
                    $teksLower = strtolower($teks);
                    // Istirahat dan Ishoma pakai kuning cerah
                    if (strpos($teksLower, 'istirahat') !== false || strpos($teksLower, 'ishoma') !== false) {
                        return 'kuning-cerah';
                    }
                    // Selain itu pakai biru cerah
                    return 'biru-cerah';
                }
            @endphp

            @php
                // Proses data jadwal untuk ditampilkan
                if (isset($jadwal) && !empty($jadwal)) {
                    // URUTKAN JADWAL BERDASARKAN ID_WAKTU
                    $jadwal = collect($jadwal)
                        ->sortBy(function ($item) {
                            return $item['id_waktu'] ?? 999;
                        })
                        ->toArray();

                    $urutan_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

                    $jadwal_per_hari = [];
                    $keterangan_per_hari = [];

                    foreach ($jadwal as $j) {
                        if (!isset($j['hari'])) {
                            continue;
                        }
                        $hari = $j['hari'];

                        // Gunakan id_waktu sebagai key
                        $jamKey = $j['id_waktu'] ?? (is_null($j['jam'] ?? null) ? 'khusus_' . rand() : $j['jam']);

                        if (!isset($jadwal_per_hari[$hari])) {
                            $jadwal_per_hari[$hari] = [];
                            $keterangan_per_hari[$hari] = [];
                        }

                        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) {
                            // Tentukan kelas warna berdasarkan teks keterangan
                            $warnaClass = getWarnaByKeterangan($j['keterangan']);

                            $keterangan_per_hari[$hari][$jamKey] = [
                                'teks' => $j['keterangan'],
                                'warna_class' => $warnaClass,
                                'id_waktu' => $j['id_waktu'],
                                'jam_ke' => $j['jam_ke'] ?? null,
                                'jam' => $j['jam'] ?? null,
                                'has_null_jam' => is_null($j['jam_ke'] ?? ($j['jam'] ?? null)),
                            ];
                        } else {
                            if (!isset($jadwal_per_hari[$hari][$jamKey])) {
                                $jadwal_per_hari[$hari][$jamKey] = [];
                            }
                            $jadwal_per_hari[$hari][$jamKey][$j['kelas']] = $j;
                        }
                    }

                    // Dapatkan daftar kelas unik
                    $kelas_list = collect($jadwal)
                        ->where('is_keterangan', '!=', true)
                        ->whereNotNull('kelas')
                        ->pluck('kelas')
                        ->unique()
                        ->sort()
                        ->values();

                    if ($kelas_list === null || $kelas_list->isEmpty()) {
                        $kelas_list = collect(['IX A', 'IX B', 'IX C']);
                    }

                    // Dapatkan daftar jam unik per hari
                    $jam_list_per_hari = [];
                    foreach ($urutan_hari as $hari) {
                        $jam_dari_jadwal = isset($jadwal_per_hari[$hari]) ? array_keys($jadwal_per_hari[$hari]) : [];
                        $jam_dari_keterangan = isset($keterangan_per_hari[$hari])
                            ? array_keys($keterangan_per_hari[$hari])
                            : [];
                        $semua_jam = array_unique(array_merge($jam_dari_jadwal, $jam_dari_keterangan));

                        // Urutkan secara numerik
                        sort($semua_jam, SORT_NUMERIC);

                        $jam_list_per_hari[$hari] = $semua_jam;
                    }

                    // Hitung bentrok
                    $bentrok = [];
                    foreach ($jadwal as $j) {
                        if (!isset($j['hari']) || !isset($j['guru']) || empty($j['guru'])) {
                            continue;
                        }
                        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) {
                            continue;
                        }
                        $jamKey = $j['id_waktu'] ?? (is_null($j['jam'] ?? null) ? 'khusus' : $j['jam']);
                        $key = $j['hari'] . '-' . $jamKey . '-' . $j['guru'];
                        $bentrok[$key] = ($bentrok[$key] ?? 0) + 1;
                    }
                } else {
                    $kelas_list = collect(['IX A', 'IX B', 'IX C']);
                    $jam_list_per_hari = [];
                    $urutan_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    $jadwal_per_hari = [];
                    $keterangan_per_hari = [];
                    $bentrok = [];
                }
            @endphp

            <div class="row">
                <div class="grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Hasil Generate</h4>
                            </div>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px">Hari</th>
                                            <th style="width: 120px">Jam Ke</th>
                                            @if (isset($kelas_list) && $kelas_list->count() > 0)
                                                @foreach ($kelas_list as $kelas)
                                                    <th style="min-width: 150px">{{ $kelas }}</th>
                                                @endforeach
                                            @else
                                                <th style="min-width: 150px">Kelas</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($urutan_hari) && !empty($urutan_hari))
                                            @foreach ($urutan_hari as $hari)
                                                @php
                                                    $jam_list = $jam_list_per_hari[$hari] ?? [];
                                                @endphp

                                                @if (count($jam_list) > 0)
                                                    @foreach ($jam_list as $index => $jamKey)
                                                        {{-- Di dalam loop foreach ($jam_list as $index => $jamKey) --}}

                                                        {{-- Di dalam loop foreach ($jam_list as $index => $jamKey) --}}

                                                        @php
                                                            $isKeterangan = isset($keterangan_per_hari[$hari][$jamKey]);
                                                            $keteranganData = $isKeterangan
                                                                ? $keterangan_per_hari[$hari][$jamKey]
                                                                : null;

                                                            // PERBAIKAN: Tentukan tampilan jam
                                                            $displayJam = '';
                                                            $showJamKe = false;

                                                            if ($isKeterangan && $keteranganData) {
                                                                // Data dari database (keterangan seperti Upacara, Istirahat, dll)
                                                                // Coba ambil dari jam_ke dulu, baru jam
                                                                $jamKe =
                                                                    $keteranganData['jam_ke'] ??
                                                                    ($keteranganData['jam'] ?? null);

                                                                // Debug: log untuk melihat nilai jam_ke
                                                                if ($keteranganData['id_waktu'] == 1) {
                                                                    \Log::info('ID Waktu 1 (Upacara):', [
                                                                        'jam_ke' => $jamKe,
                                                                        'keteranganData' => $keteranganData,
                                                                    ]);
                                                                }

                                                                // Jika jam_ke ada (tidak null, tidak kosong, dan bukan 0), tampilkan
                                                                if (!is_null($jamKe) && $jamKe !== '' && $jamKe !== 0) {
                                                                    $displayJam = $jamKe;
                                                                    $showJamKe = true;
                                                                }
                                                                // Jika jam_ke NULL, biarkan $showJamKe = false
                                                            } else {
                                                                // Data jadwal normal dari API
                                                                $firstJadwal = isset($jadwal_per_hari[$hari][$jamKey])
                                                                    ? reset($jadwal_per_hari[$hari][$jamKey])
                                                                    : null;
                                                                if ($firstJadwal && isset($firstJadwal['jam'])) {
                                                                    $jamVal = $firstJadwal['jam'];
                                                                    if (!is_null($jamVal) && $jamVal !== '') {
                                                                        $displayJam = $jamVal;
                                                                        $showJamKe = true;
                                                                    }
                                                                } elseif (is_numeric($jamKey)) {
                                                                    $displayJam = $jamKey;
                                                                    $showJamKe = true;
                                                                }
                                                            }

                                                            $isJamKhusus =
                                                                $isKeterangan &&
                                                                ($keteranganData['has_null_jam'] ?? false);
                                                            $colspanCount =
                                                                $kelas_list && $kelas_list->count() > 0
                                                                    ? $kelas_list->count()
                                                                    : 1;
                                                        @endphp

                                                        <tr>
                                                            @if ($loop->first)
                                                                <td rowspan="{{ count($jam_list) }}">
                                                                    <strong>{{ $hari }}</strong>
                                                                </td>
                                                            @endif

                                                            {{-- KOLOM JAM --}}
                                                            <td class="text-center">
                                                                @if ($showJamKe && $displayJam !== '')
                                                                    <strong>{{ $displayJam }}</strong>
                                                                @else
                                                                    <span style="opacity: 0.3;"></span>
                                                                @endif
                                                            </td>

                                                            {{-- KOLOM UTAMA (MERGE CELL UNTUK KETERANGAN) --}}
                                                            @if ($isKeterangan)
                                                                @php
                                                                    $warnaClass =
                                                                        $keteranganData['warna_class'] ?? 'biru-cerah';
                                                                @endphp
                                                                <td colspan="{{ $colspanCount }}"
                                                                    class="keterangan-cell {{ $warnaClass }}"
                                                                    style="text-align: center; vertical-align: middle;">
                                                                    <div class="keterangan-text">
                                                                        <strong>{{ $keteranganData['teks'] }}</strong>
                                                                    </div>
                                                                </td>
                                                            @else
                                                                {{-- JADWAL NORMAL PER KELAS --}}
                                                                @if ($kelas_list && $kelas_list->count() > 0)
                                                                    @foreach ($kelas_list as $kelas)
                                                                        <td class="text-center">
                                                                            @if (isset($jadwal_per_hari[$hari][$jamKey][$kelas]))
                                                                                @php
                                                                                    $data =
                                                                                        $jadwal_per_hari[$hari][
                                                                                            $jamKey
                                                                                        ][$kelas];
                                                                                    $key =
                                                                                        $data['hari'] .
                                                                                        '-' .
                                                                                        $jamKey .
                                                                                        '-' .
                                                                                        $data['guru'];
                                                                                    $isBentrok =
                                                                                        isset($bentrok[$key]) &&
                                                                                        $bentrok[$key] > 1;
                                                                                @endphp

                                                                                <div
                                                                                    class="{{ $isBentrok ? 'bentrok' : '' }}">
                                                                                    <strong>{{ $data['guru'] }}</strong>
                                                                                    <br>
                                                                                    <small>{{ $data['mapel'] }}</small>
                                                                                    @if ($isBentrok)
                                                                                        <br>
                                                                                        <span
                                                                                            class="badge bg-danger">Bentrok!</span>
                                                                                    @endif
                                                                                </div>
                                                                            @else
                                                                                <span class="text-muted"></span>
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                @else
                                                                    <td class="text-center text-muted">
                                                                        Tidak ada data kelas
                                                                    </td>
                                                                @endif
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td><strong>{{ $hari }}</strong></td>
                                                        @php
                                                            $colspanCount =
                                                                $kelas_list && $kelas_list->count() > 0
                                                                    ? $kelas_list->count() + 1
                                                                    : 2;
                                                        @endphp
                                                        <td colspan="{{ $colspanCount }}" class="text-muted text-center">
                                                            Tidak ada jadwal untuk hari {{ $hari }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    Belum ada data jadwal
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tab Container -->
                            <div class="tab-container">
                                <div class="tab-buttons">
                                    <button class="tab-btn active" onclick="switchTab('tab-bentrok')">
                                        <i class="mdi mdi-alert"></i> Informasi Bentrok Guru
                                    </button>
                                    <button class="tab-btn" onclick="switchTab('tab-jam-mapel')">
                                        <i class="mdi mdi-chart-bar"></i> Analisis Jam Mapel
                                    </button>
                                    <button class="tab-btn" onclick="switchTab('tab-beban-guru')">
                                        <i class="mdi mdi-account"></i> Beban Guru
                                    </button>
                                </div>

                                <!-- Tab 1: Informasi Bentrok Guru -->
                                <div id="tab-bentrok" class="tab-content active">
                                    @php
                                        // Hitung ulang bentrok untuk ditampilkan
                                        $bentrok_detail = [];
                                        foreach ($jadwal as $j) {
                                            if (
                                                !isset($j['hari']) ||
                                                !isset($j['jam']) ||
                                                !isset($j['guru']) ||
                                                empty($j['guru'])
                                            ) {
                                                continue;
                                            }
                                            $key = $j['hari'] . '-' . $j['jam'] . '-' . $j['guru'];
                                            if (!isset($bentrok_detail[$key])) {
                                                $bentrok_detail[$key] = [
                                                    'guru' => $j['guru'],
                                                    'hari' => $j['hari'],
                                                    'jam' => $j['jam'],
                                                    'kelas' => [],
                                                ];
                                            }
                                            $bentrok_detail[$key]['kelas'][] = $j['kelas'];
                                        }

                                        // Filter hanya yang bentrok (lebih dari 1 kelas)
                                        $bentrok_only = array_filter($bentrok_detail, function ($item) {
                                            return count($item['kelas']) > 1;
                                        });
                                    @endphp

                                    @if (count($bentrok_only) > 0)
                                        <div class="alert alert-danger">
                                            <h5><i class="mdi mdi-alert-circle"></i> Ditemukan {{ count($bentrok_only) }}
                                                Bentrok Guru!</h5>
                                            <p>Berikut adalah detail bentrok yang terjadi:</p>
                                        </div>

                                        <table class="table table-bordered analysis-table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Hari</th>
                                                    <th>Jam Ke</th>
                                                    <th>Guru</th>
                                                    <th>Jumlah Kelas</th>
                                                    <th>Kelas yang Bentrok</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($bentrok_only as $index => $bentrok)
                                                    <tr class="table-danger">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td><strong>{{ $bentrok['hari'] }}</strong></td>
                                                        <td><strong>{{ $bentrok['jam'] }}</strong></td>
                                                        <td><strong>{{ $bentrok['guru'] }}</strong></td>
                                                        <td><span
                                                                class="badge bg-danger">{{ count($bentrok['kelas']) }}</span>
                                                        </td>
                                                        <td>{{ implode(', ', $bentrok['kelas']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="alert alert-warning mt-2">
                                            <strong>💡 Solusi:</strong> Bentrok terjadi karena guru mengajar di beberapa
                                            kelas pada waktu yang sama.
                                            Lakukan generate ulang jadwal dengan parameter yang lebih optimal atau sesuaikan
                                            data guru_mapel.
                                        </div>
                                    @else
                                        <div class="alert alert-success">
                                            <h5><i class="mdi mdi-check-circle"></i> ✅ Tidak Ada Bentrok Guru!</h5>
                                            <p>Semua guru mengajar di waktu yang berbeda untuk setiap kelas.</p>
                                        </div>
                                    @endif
                                </div>

                                @php
                                    // Ambil target dari session
                                    $target_mapel = session('target_mapel', []);
                                    $target_beban_guru = session('target_beban_guru', []);

                                    // Hitung jam mapel per kelas untuk analisis
                                    $jam_mapel_analisis = [];
                                    foreach ($jadwal as $j) {
                                        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) {
                                            continue;
                                        }
                                        $kelas = $j['kelas'];
                                        $mapel = $j['mapel'];

                                        if (!isset($jam_mapel_analisis[$kelas])) {
                                            $jam_mapel_analisis[$kelas] = [];
                                        }
                                        $jam_mapel_analisis[$kelas][$mapel] =
                                            ($jam_mapel_analisis[$kelas][$mapel] ?? 0) + 1;
                                    }

                                    // Hitung beban guru aktual
                                    $beban_guru_aktual = [];
                                    foreach ($jadwal as $j) {
                                        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) {
                                            continue;
                                        }
                                        $guru = $j['guru'];
                                        if (empty($guru)) {
                                            continue;
                                        }
                                        $beban_guru_aktual[$guru] = ($beban_guru_aktual[$guru] ?? 0) + 1;
                                    }
                                @endphp

                                <!-- Tab 2: Analisis Jam Mapel dengan target yang benar -->
                                <div id="tab-jam-mapel" class="tab-content">
                                    <h5><i class="mdi mdi-chart-bar"></i> Analisis Pemenuhan Jam Mata Pelajaran per Kelas
                                    </h5>
                                    <p class="text-muted">Target jam berdasarkan tabel mapel di database</p>

                                    <div class="table-responsive">
                                        <table class="table table-bordered analysis-table">
                                            <thead>
                                                <tr>
                                                    <th>Kelas</th>
                                                    @foreach ($target_mapel as $mapel => $target)
                                                        <th>
                                                            {{ $mapel }}<br>
                                                            <small class="text-muted">Target: {{ $target }}
                                                                jam</small>
                                                        </th>
                                                    @endforeach
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $semua_kelas = array_keys($jam_mapel_analisis);
                                                    sort($semua_kelas);
                                                @endphp

                                                @foreach ($semua_kelas as $kelas)
                                                    @php
                                                        $total_jam = 0;
                                                        $semua_sesuai = true;
                                                    @endphp
                                                    <tr>
                                                        <td><strong>{{ $kelas }}</strong></td>
                                                        @foreach ($target_mapel as $mapel => $target)
                                                            @php
                                                                $aktual = $jam_mapel_analisis[$kelas][$mapel] ?? 0;
                                                                $total_jam += $aktual;
                                                                $selisih = $aktual - $target;
                                                                $sesuai = $aktual == $target;
                                                                if (!$sesuai) {
                                                                    $semua_sesuai = false;
                                                                }
                                                            @endphp
                                                            <td class="{{ $sesuai ? 'status-ok' : 'status-mismatch' }}">
                                                                {{ $aktual }} jam
                                                                @if (!$sesuai)
                                                                    <br>
                                                                    <small>({{ $selisih >= 0 ? '+' : '' }}{{ $selisih }})</small>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td><strong>{{ $total_jam }} / 40 jam</strong></td>
                                                        <td>
                                                            @if ($semua_sesuai)
                                                                <span class="badge bg-success">✅ Sesuai</span>
                                                            @else
                                                                <span class="badge bg-warning">⚠️ Belum Sesuai</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-info mt-2">
                                        <strong>📊 Keterangan Target Jam Mapel (dari database):</strong>
                                        <ul>
                                            @foreach ($target_mapel as $mapel => $target)
                                                <li><strong>{{ $mapel }}</strong>: {{ $target }} jam/minggu
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <!-- Tab 3: Beban Guru dengan target yang benar -->
                                <div id="tab-beban-guru" class="tab-content">
                                    <h5><i class="mdi mdi-account-multiple"></i> Analisis Beban Mengajar Guru</h5>
                                    <p class="text-muted">Target beban guru dihitung dari total jam mapel yang diajarkan
                                    </p>

                                    <div class="table-responsive">
                                        <table class="table table-bordered analysis-table guru-beban-table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Guru</th>
                                                    <th>Aktual Jam</th>
                                                    <th>Target Jam</th>
                                                    <th>Selisih</th>
                                                    <th>Status</th>
                                                    <th>Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    // Urutkan berdasarkan beban tertinggi
                                                    arsort($beban_guru_aktual);
                                                    $no = 1;
                                                @endphp

                                                @foreach ($beban_guru_aktual as $guru => $jam_aktual)
                                                    @php
                                                        $target = $target_beban_guru[$guru] ?? 20;
                                                        $selisih = $jam_aktual - $target;
                                                        $persen = min(100, ($jam_aktual / max($target, 1)) * 100);

                                                        if ($selisih == 0) {
                                                            $status_class = 'status-ok';
                                                            $status_text = '✅ Sesuai';
                                                        } elseif ($selisih > 0) {
                                                            $status_class = 'status-mismatch';
                                                            $status_text = '⚠️ Kelebihan ' . $selisih . ' jam';
                                                        } else {
                                                            $status_class = 'status-mismatch';
                                                            $status_text = '⚠️ Kekurangan ' . abs($selisih) . ' jam';
                                                        }

                                                        $progress_class = '';
                                                        if ($persen >= 95 && $persen <= 105) {
                                                            $progress_class = '';
                                                        } elseif ($persen > 105) {
                                                            $progress_class = 'danger';
                                                        } elseif ($persen < 95) {
                                                            $progress_class = 'warning';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td><strong>{{ $guru }}</strong></td>
                                                        <td><strong>{{ $jam_aktual }} jam</strong></td>
                                                        <td>{{ $target }} jam</td>
                                                        <td class="{{ $status_class }}">
                                                            {{ $selisih >= 0 ? '+' : '' }}{{ $selisih }} jam</td>
                                                        <td>{{ $status_text }}</td>
                                                        <td style="width: 200px;">
                                                            <div class="progress-bar">
                                                                <div class="progress-fill {{ $progress_class }}"
                                                                    style="width: {{ $persen }}%;">
                                                                    {{ number_format($persen, 1) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-info mt-2">
                                        <strong>📊 Statistik Beban Guru:</strong>
                                        <ul>
                                            <li>Total jam mengajar semua guru: <strong>{{ array_sum($beban_guru_aktual) }}
                                                    jam</strong></li>
                                            <li>Rata-rata beban per guru:
                                                <strong>{{ round(array_sum($beban_guru_aktual) / max(count($beban_guru_aktual), 1)) }}
                                                    jam</strong>
                                            </li>
                                            <li>Jumlah guru: <strong>{{ count($beban_guru_aktual) }}</strong></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-2">
                                    <strong>💡 Catatan:</strong> Beban guru idealnya 20-24 jam per minggu.
                                    Jika ada guru dengan beban terlalu tinggi atau rendah, sesuaikan data guru_mapel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <br>

            <button class="btn btn-success">
                <i class="mdi mdi-content-save"></i> Simpan Jadwal
            </button>
        </form>

        @if (isset($fitness_history) && count($fitness_history) > 0)
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                var fitnessData = @json($fitness_history);

                if (fitnessData.length > 200) {
                    var sampledData = [];
                    var sampledLabels = [];
                    for (var i = 0; i < fitnessData.length; i += 10) {
                        sampledData.push(fitnessData[i]);
                        sampledLabels.push("Gen " + (i + 1));
                    }
                    fitnessData = sampledData;
                    var labels = sampledLabels;
                } else {
                    var labels = fitnessData.map((v, i) => "Gen " + (i + 1));
                }

                var ctx = document.getElementById('fitnessChart');

                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Fitness',
                                data: fitnessData,
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0,123,255,0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Grafik Konvergensi Genetic Algorithm'
                                },
                                legend: {
                                    display: true
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Generasi'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Fitness (semakin kecil semakin baik)'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            </script>
        @endif
    @else
        <div class="text-center mt-5">
            <div class="alert alert-info">
                <h4>Belum Ada Jadwal</h4>
                <p>Klik tombol "Generate Jadwal Baru" di atas untuk menghasilkan jadwal menggunakan algoritma genetika.</p>
            </div>
        </div>
    @endif
@endsection

<script>
    function switchTab(tabId) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(function(tab) {
            tab.classList.remove('active');
        });

        // Nonaktifkan semua tombol tab
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.remove('active');
        });

        // Tampilkan tab yang dipilih
        document.getElementById(tabId).classList.add('active');

        // Aktifkan tombol yang sesuai
        event.currentTarget.classList.add('active');
    }

    function clearJadwal() {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal saat ini? Data yang belum disimpan akan hilang.')) {
            document.getElementById('clearForm').submit();
        }
    }
</script>
