@php
    // Hitung beban guru aktual dari data jadwal
    $beban_guru_aktual = [];
    if (isset($jadwal) && !empty($jadwal)) {
        foreach ($jadwal as $j) {
            if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) continue;
            $guru = $j['guru'];
            if (empty($guru)) continue;
            $beban_guru_aktual[$guru] = ($beban_guru_aktual[$guru] ?? 0) + 1;
        }
    }

    // Urutkan berdasarkan beban tertinggi
    arsort($beban_guru_aktual);
    $total_jam_semua = array_sum($beban_guru_aktual);
    $jumlah_guru = count($beban_guru_aktual);
    $rata_rata = $jumlah_guru > 0 ? round($total_jam_semua / $jumlah_guru) : 0;
@endphp

<div id="tab-beban-guru" class="tab-content">
    <h5><i class="mdi mdi-account-multiple"></i> Analisis Beban Mengajar Guru</h5>
    <p class="text-muted">
        Target beban guru = Total jam_per_minggu dari semua mapel yang diampu (diambil dari database)
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
                @forelse ($beban_guru_aktual as $guru => $jam_aktual)
                    @php
                        // Ambil target dari session (sudah dihitung dari database)
                        $target = $target_beban_guru[$guru] ?? 0;
                        $selisih = $jam_aktual - $target;
                        $persen = $target > 0 ? min(100, ($jam_aktual / $target) * 100) : 0;

                        if ($target == 0) {
                            $status_class = 'status-mismatch';
                            $status_text = '⚠️ Tidak ada data mapel';
                            $progress_class = 'danger';
                        } elseif ($selisih == 0) {
                            $status_class = 'status-ok';
                            $status_text = '✅ Sesuai';
                            $progress_class = '';
                        } elseif ($selisih > 0) {
                            $status_class = 'status-mismatch';
                            $status_text = '⚠️ Kelebihan ' . $selisih . ' jam';
                            $progress_class = 'danger';
                        } else {
                            $status_class = 'status-mismatch';
                            $status_text = '⚠️ Kekurangan ' . abs($selisih) . ' jam';
                            $progress_class = 'warning';
                        }
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $guru }}</strong></td>
                        <td><strong>{{ $jam_aktual }} jam</strong></td>
                        <td>
                            {{ $target }} jam
                        </td>
                        <td class="{{ $status_class }}">
                            {{ $selisih >= 0 ? '+' : '' }}{{ $selisih }} jam
                        </td>
                        <td>{{ $status_text }}</td>
                        <td style="width: 200px;">
                            <div class="progress-bar">
                                <div class="progress-fill {{ $progress_class }}" style="width: {{ $persen }}%;">
                                    {{ number_format($persen, 1) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Belum ada data guru untuk dianalisis
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="alert alert-info mt-2">
        <strong>📊 Statistik Beban Guru:</strong>
        <ul>
            <li>Total jam mengajar semua guru (aktual): <strong>{{ $total_jam_semua }} jam</strong></li>
            <li>Rata-rata beban per guru: <strong>{{ $rata_rata }} jam</strong></li>
            <li>Jumlah guru: <strong>{{ $jumlah_guru }}</strong></li>
        </ul>
    </div>

    <div class="alert alert-secondary mt-2">
        <strong>ℹ️ Informasi Target:</strong>
        <p class="mb-0">Target jam guru dihitung dari <strong>total jam_per_minggu</strong> semua mata pelajaran yang diampu oleh guru tersebut di tabel <code>guru_mapel</code> (status aktif).</p>
    </div>
</div>
