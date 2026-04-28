@extends('landing.index')

@section('section-top')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <h1>Statistik Sistem</h1>
                    <ul>
                        <li><a href="{{ route('landing') }}">Home</a></li>
                        <li> / Statistik</li>
                    </ul>
                </div><!-- //.HERO-TEXT -->
            </div><!--- END COL -->
        </div><!--- END CONTAINER -->
    </section>
@endsection

@section('content')
    <!-- START STATISTIK UTAMA -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s"
                    data-wow-offset="0">
                    <div class="stat-card-primary text-center">
                        <div class="stat-icon">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <h2 class="stat-number">{{ $totalKelas ?? 19 }}</h2>
                        <p class="stat-label">Total Kelas</p>
                        <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> Terdata</span>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="stat-card-success text-center">
                        <div class="stat-icon">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h2 class="stat-number">{{ $totalGuru ?? 30 }}</h2>
                        <p class="stat-label">Total Guru</p>
                        <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> Aktif Mengajar</span>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s"
                    data-wow-offset="0">
                    <div class="stat-card-info text-center">
                        <div class="stat-icon">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <h2 class="stat-number">{{ $totalMapel ?? 11 }}</h2>
                        <p class="stat-label">Mata Pelajaran</p>
                        <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> Tersedia</span>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s"
                    data-wow-offset="0">
                    <div class="stat-card-warning text-center">
                        <div class="stat-icon">
                            <i class="fa-solid fa-calendar-week"></i>
                        </div>
                        <h2 class="stat-number">{{ $totalJadwal ?? 760 }}</h2>
                        <p class="stat-label">Total Jadwal</p>
                        <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> Tersusun</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END STATISTIK UTAMA -->

    <!-- START GRAFIK BEBAN GURU -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="section-title text-center">
                        <h4>Analisis Beban Guru</h4>
                        <h1>Distribusi Jam Mengajar</h1>
                        <p>Visualisasi beban mengajar per guru dalam satu minggu</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="chart-container">
                        <canvas id="bebanGuruChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <div class="info-card">
                        <h4><i class="fa-solid fa-chart-line"></i> Rata-rata Beban Guru</h4>
                        <p class="info-value">{{ round(array_sum($guruJams ?? []) / max(count($guruJams ?? []), 1)) }}
                            jam/minggu</p>
                        <small class="text-muted">Beban ideal: 20-24 jam/minggu</small>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" data-wow-offset="0">
                    <div class="info-card">
                        <h4><i class="fa-solid fa-chart-simple"></i> Rentang Beban</h4>
                        <p class="info-value">{{ min($guruJams ?? [0]) }} - {{ max($guruJams ?? [0]) }} jam/minggu</p>
                        <small class="text-muted">Guru dengan beban tertinggi & terendah</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END GRAFIK BEBAN GURU -->

    <!-- START BENTROK DAN KELAS -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="section-title">
                        <h4>Status Penjadwalan</h4>
                        <h1>Statistik Bentrok</h1>
                    </div>
                    <div class="chart-container small-chart">
                        <canvas id="bentrokChart" height="250"></canvas>
                    </div>
                    <div class="mt-4 text-center">
                        @if (($totalBentrok ?? 0) > 0)
                            <div class="alert-warning-box">
                                <i class="fa-solid fa-triangle-exclamation"></i> Terdapat {{ $totalBentrok ?? 0 }} bentrok
                                pada jadwal aktif
                            </div>
                        @else
                            <div class="alert-success-box">
                                <i class="fa-solid fa-check-circle"></i> Tidak ada bentrok guru pada jadwal aktif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="section-title">
                        <h4>Penyebaran</h4>
                        <h1>Distribusi Kelas</h1>
                    </div>
                    <div class="chart-container small-chart">
                        <canvas id="kelasDistributionChart" height="250"></canvas>
                    </div>
                    <div class="mt-4 text-center">
                        <div class="info-box">
                            <i class="fa-solid fa-building"></i> Total Kelas: {{ $totalKelas ?? 19 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END BENTROK DAN KELAS -->

    <!-- START TABEL GURU OVERLOAD -->
    {{-- <section class="section-padding bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="section-title text-center">
                        <h4>Monitoring</h4>
                        <h1>Guru dengan Beban Tertinggi</h1>
                        <p>10 guru dengan jam mengajar terbanyak</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="table-responsive">
                        <table class="table table-hover guru-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>Total Jam</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($bebanGuru ?? []) as $index => $guru)
                                    @php
                                        $persen = ($guru['jam'] / 24) * 100;
                                        $status =
                                            $guru['jam'] > 24
                                                ? 'Overload'
                                                : ($guru['jam'] < 20
                                                    ? 'Underload'
                                                    : 'Ideal');
                                        $statusClass =
                                            $status == 'Overload'
                                                ? 'danger'
                                                : ($status == 'Underload'
                                                    ? 'warning'
                                                    : 'success');
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $guru['nama'] }}</strong></td>
                                        <td>{{ $guru['jam'] }} jam</td>
                                        <td><span class="badge badge-{{ $statusClass }}">{{ $status }}</span></td>
                                        <td style="width: 200px;">
                                            <div class="progress-bar-custom">
                                                <div class="progress-fill {{ $statusClass }}"
                                                    style="width: {{ min($persen, 100) }}%;">
                                                    {{ round($persen) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data guru</td>
                                    <tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- END TABEL GURU OVERLOAD -->

    <!-- START INSIGHT -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="section-title text-center">
                        <h4>Insight</h4>
                        <h1>Ringkasan Statistik</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="insight-card">
                        <i class="fa-solid fa-clock"></i>
                        <h3>Total Jam Mengajar</h3>
                        <p class="insight-value">{{ array_sum($guruJams ?? []) }} jam/minggu</p>
                        <small>Seluruh guru dalam 1 minggu</small>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s"
                    data-wow-offset="0">
                    <div class="insight-card">
                        <i class="fa-solid fa-chart-line"></i>
                        <h3>Rata-rata per Guru</h3>
                        <p class="insight-value">{{ round(array_sum($guruJams ?? []) / max(count($guruJams ?? []), 1)) }}
                            jam</p>
                        <small>Rata-rata beban mengajar</small>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s"
                    data-wow-offset="0">
                    <div class="insight-card">
                        <i class="fa-solid fa-percent"></i>
                        <h3>Efektivitas</h3>
                        <p class="insight-value">
                            @php
                                $totalSlot = ($totalKelas ?? 19) * 40;
                                $efektivitas = $totalSlot > 0 ? round((($totalJadwal ?? 0) / $totalSlot) * 100, 1) : 0;
                            @endphp
                            {{ $efektivitas }}%
                        </p>
                        <small>Pemanfaatan slot jadwal</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END INSIGHT -->

    <style>
        /* Stat Card Styles */
        .stat-card-primary,
        .stat-card-success,
        .stat-card-info,
        .stat-card-warning {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card-primary:hover,
        .stat-card-success:hover,
        .stat-card-info:hover,
        .stat-card-warning:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .stat-card-primary .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card-success .stat-icon {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card-info .stat-icon {
            background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
        }

        .stat-card-warning .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-icon i {
            font-size: 28px;
            color: white;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            margin: 15px 0 5px;
        }

        .stat-label {
            color: #666;
            margin-bottom: 10px;
        }

        .stat-trend {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .stat-trend.up {
            background: #d1fae5;
            color: #065f46;
        }

        /* Info Card */
        .info-card,
        .insight-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .info-card h4,
        .insight-card h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin: 10px 0;
        }

        .insight-value {
            font-size: 36px;
            font-weight: 800;
            color: #667eea;
            margin: 15px 0;
        }

        /* Alert Box */
        .alert-warning-box,
        .alert-success-box {
            padding: 15px 20px;
            border-radius: 12px;
            display: inline-block;
        }

        .alert-warning-box {
            background: #fef3c7;
            color: #92400e;
        }

        .alert-success-box {
            background: #d1fae5;
            color: #065f46;
        }

        /* Progress Bar */
        .progress-bar-custom {
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            height: 24px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: white;
            font-weight: bold;
        }

        .small-chart {
            max-width: 450px;
            margin: 0 auto;
        }

        .progress-fill.success {
            background: #10b981;
        }

        .progress-fill.warning {
            background: #f59e0b;
        }

        .progress-fill.danger {
            background: #ef4444;
        }

        /* Badge */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Guru Table */
        .guru-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
        }

        .guru-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border: none;
        }

        .guru-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .bg-light {
            background-color: #f8fafc;
        }

        /* Insight Card */
        .insight-card {
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }

        .insight-card:hover {
            transform: translateY(-5px);
        }

        .insight-card i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .stat-number {
                font-size: 28px;
            }

            .info-value {
                font-size: 24px;
            }
        }
    </style>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Grafik Beban Guru
        const bebanCtx = document.getElementById('bebanGuruChart').getContext('2d');
        new Chart(bebanCtx, {
            type: 'bar',
            data: {
                labels: @json($guruNames ?? []),
                datasets: [{
                    label: 'Jam Mengajar per Minggu',
                    data: @json($guruJams ?? []),
                    backgroundColor: 'rgba(102, 126, 234, 0.6)',
                    borderColor: '#667eea',
                    borderWidth: 2,
                    borderRadius: 8,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw} jam`
                        }
                    }
                },
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
                }
            }
        });

        // Grafik Bentrok
        const bentrokCtx = document.getElementById('bentrokChart').getContext('2d');
        new Chart(bentrokCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tidak Bentrok', 'Bentrok'],
                datasets: [{
                    data: [{{ ($totalJadwal ?? 0) - ($totalBentrok ?? 0) }},
                        {{ $totalBentrok ?? 0 }}
                    ],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Grafik Distribusi Kelas
        const kelasCtx = document.getElementById('kelasDistributionChart').getContext('2d');
        new Chart(kelasCtx, {
            type: 'pie',
            data: {
                labels: ['Kelas VII', 'Kelas VIII', 'Kelas IX'],
                datasets: [{
                    data: [6, 7, 6],
                    backgroundColor: ['#667eea', '#764ba2', '#f093fb'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
