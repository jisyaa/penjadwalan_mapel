@extends('admin')

<style>
    .pastel-primary {
        background-color: #6c9de7 !important;
        color: #084298 !important;
    }

    .pastel-success {
        background-color: #60d6a1 !important;
        color: #0f5132 !important;
    }

    .pastel-info {
        background-color: #74d5eb !important;
        color: #055160 !important;
    }

    .pastel-warning {
        background-color: #f5df96 !important;
        color: #664d03 !important;
    }

    td {
        vertical-align: middle;
        font-size: 12px;
    }

    .keterangan-cell {
        text-align: center;
        vertical-align: middle;
    }

    .keterangan-cell.kuning-cerah {
        background: #fff3cd;
    }

    .keterangan-cell.kuning-cerah .keterangan-text {
        color: #856404;
    }

    .keterangan-cell.biru-cerah {
        background: #e8f4fd;
    }

    .keterangan-cell.biru-cerah .keterangan-text {
        color: #0066cc;
    }

    .keterangan-text {
        font-weight: bold;
    }
</style>

@section('content')
    <div class="page-header">
        <h3 class="page-title">Dashboard</h3>
    </div>

    <!-- Statistik Utama -->
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white pastel-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Kelas</h5>
                    <p class="card-text display-4">{{ $totalKelas }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white pastel-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Guru</h5>
                    <p class="card-text display-4">{{ $totalGuru }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white pastel-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Mata Pelajaran</h5>
                    <p class="card-text display-4">{{ $totalMapel }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white pastel-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Jadwal Aktif</h5>
                    <p class="card-text display-4">{{ $isJadwalAktif ? 'Aktif' : 'Tidak Aktif' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cepat -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📊 Statistik Cepat</h5>
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h3>{{ $statCepat['persen_pemenuhan_kelas'] }}%</h3>
                            <p class="text-muted">Pemenuhan Jam Mapel</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3>{{ $statCepat['guru_ideal'] }}</h3>
                            <p class="text-muted">Guru dengan Beban Ideal</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-warning">{{ $statCepat['guru_overload'] }}</h3>
                            <p class="text-muted">Guru Overload</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-info">{{ $statCepat['guru_underload'] }}</h3>
                            <p class="text-muted">Guru Underload</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📅 Jadwal Hari Ini ({{ $hariIni }})</h5>
                    @php
                        // Ambil daftar kelas unik
                        $kelasList = collect($jadwalHariIni)->pluck('nama_kelas')->unique()->values();

                        // Kelompokkan berdasarkan id_waktu (urutan asli)
                        $jadwalPerWaktu = [];
                        foreach ($jadwalHariIni as $item) {
                            $idWaktu = $item->id_waktu;
                            if (!isset($jadwalPerWaktu[$idWaktu])) {
                                $jadwalPerWaktu[$idWaktu] = [];
                            }
                            $jadwalPerWaktu[$idWaktu][$item->nama_kelas] = $item;
                        }

                        // Urutkan berdasarkan id_waktu
                        ksort($jadwalPerWaktu);

                        // Ambil informasi waktu
                        $waktuInfo = [];
                        foreach ($jadwalHariIni as $item) {
                            if (!isset($waktuInfo[$item->id_waktu])) {
                                $waktuInfo[$item->id_waktu] = [
                                    'jam_ke' => $item->jam_ke,
                                    'waktu_mulai' => $item->waktu_mulai ?? '',
                                    'waktu_selesai' => $item->waktu_selesai ?? '',
                                    'keterangan' => $item->keterangan ?? '',
                                ];
                            }
                        }
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 60px">Jam Ke</th>
                                    <th style="width: 100px">Waktu</th>
                                    @foreach ($kelasList as $kelas)
                                        <th style="min-width: 120px">{{ $kelas }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwalPerWaktu as $idWaktu => $kelasData)
                                    @php
                                        $jamKe = $waktuInfo[$idWaktu]['jam_ke'] ?? '';
                                        $waktuMulai = $waktuInfo[$idWaktu]['waktu_mulai'] ?? '';
                                        $waktuSelesai = $waktuInfo[$idWaktu]['waktu_selesai'] ?? '';
                                        $waktuRange =
                                            $waktuMulai && $waktuSelesai
                                                ? substr($waktuMulai, 0, 5) . '-' . substr($waktuSelesai, 0, 5)
                                                : '';
                                        $keteranganWaktu = $waktuInfo[$idWaktu]['keterangan'] ?? '';

                                        // Cek apakah semua item di baris ini adalah keterangan yang sama
                                        $isAllKeterangan = true;
                                        $keteranganValue = '';

                                        foreach ($kelasData as $item) {
                                            if (!$item->is_keterangan) {
                                                $isAllKeterangan = false;
                                                break;
                                            }
                                            if ($keteranganValue === '') {
                                                $keteranganValue = $item->keterangan;
                                            }
                                        }
                                    @endphp

                                    @if ($isAllKeterangan && $keteranganValue)
                                        {{-- Baris keterangan (merged cell untuk semua kelas) --}}
                                        <tr>
                                            <td class="text-center keterangan-cell">
                                                <strong>{{ $jamKe }}</strong>
                                            </td>
                                            <td class="text-center keterangan-cell">
                                                <small>{{ $waktuRange }}</small>
                                            </td>
                                            <td colspan="{{ count($kelasList) }}"
                                                class="keterangan-cell {{ strpos(strtolower($keteranganValue), 'istirahat') !== false || strpos(strtolower($keteranganValue), 'ishoma') !== false ? 'kuning-cerah' : 'biru-cerah' }}">
                                                <div class="keterangan-text">
                                                    <strong>{{ $keteranganValue }}</strong>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Baris jadwal normal --}}
                                        <tr>
                                            <td class="text-center"><strong>{{ $jamKe }}</strong></td>
                                            <td class="text-center"><small>{{ $waktuRange }}</small></td>

                                            @foreach ($kelasList as $kelas)
                                                @php
                                                    $item = $kelasData[$kelas] ?? null;
                                                @endphp

                                                @if ($item && $item->is_keterangan)
                                                    <td
                                                        class="keterangan-cell {{ strpos(strtolower($item->keterangan), 'istirahat') !== false || strpos(strtolower($item->keterangan), 'ishoma') !== false ? 'kuning-cerah' : 'biru-cerah' }}">
                                                        <div class="keterangan-text">
                                                            <strong>{{ $item->keterangan }}</strong>
                                                        </div>
                                                    </td>
                                                @elseif($item && $item->nama_mapel)
                                                    <td class="text-center">
                                                        <strong>{{ $item->nama_mapel }}</strong><br>
                                                        <small>{{ $item->nama_guru }}</small>
                                                    </td>
                                                @else
                                                    <td class="text-center text-muted">-</td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-stretch">
        <div class="col-md-6 d-flex flex-column">
            <!-- Bentrok Guru -->
            <div class="card mb-3 flex-fill">
                <div class="card-body">
                    <h5 class="card-title">⚠️ Bentrok Guru</h5>
                    @if (count($bentrokGuru) > 0)
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($bentrokGuru as $bentrok)
                                    <li>
                                        <strong>{{ $bentrok['guru'] }}</strong> bentrok di
                                        <strong>{{ $bentrok['hari'] }}</strong> jam
                                        <strong>{{ $bentrok['jam'] }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-success">✅ Tidak ada bentrok guru</div>
                    @endif
                </div>
            </div>

            <!-- Guru Overload -->
            <div class="card flex-fill">
                <div class="card-body">
                    <h5 class="card-title">⚠️ Guru Overload (>24 jam)</h5>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Guru</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guruOverload as $guru)
                                <tr class="table-danger">
                                    <td>{{ $guru['nama'] }}</td>
                                    <td>{{ $guru['aktual'] }} jam</td>
                                    <td>Overload (+{{ $guru['selisih'] }})</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada guru overload</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">📊 Beban Mengajar Guru</h5>
                    <div class="flex-grow-1">
                        <canvas id="bebanGuruChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- History Generate -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📋 Riwayat Generate Jadwal</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Tanggal Generate</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyJadwal as $history)
                                    <tr>
                                        <td>{{ $history->tahun_ajaran }}</td>
                                        <td>{{ ucfirst($history->semester) }}</td>
                                        <td>{{ $history->tanggal_generate->format('d/m/Y H:i') }}</td>
                                        <td>{!! $history->aktif == 'aktif'
                                            ? '<span class="badge bg-success">Aktif</span>'
                                            : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                                        <td>
                                            <a href="{{ route('history.jadwal.show', $history->id_master) }}"
                                                class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada history generate jadwal</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Beban Guru
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('bebanGuruChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($guruNames),
                datasets: [{
                    label: 'Jam Mengajar per Minggu',
                    data: @json($guruJams),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jam/Minggu'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Jam Mengajar: ${context.raw} jam`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
