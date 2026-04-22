@php
    $target_beban_guru = session('target_beban_guru', []);
    $beban_guru_aktual = [];
    foreach ($jadwal as $j) {
        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) continue;
        $guru = $j['guru'];
        if (empty($guru)) continue;
        $beban_guru_aktual[$guru] = ($beban_guru_aktual[$guru] ?? 0) + 1;
    }
    arsort($beban_guru_aktual);
@endphp

<div id="tab-beban-guru" class="tab-content">
    <h5><i class="mdi mdi-account-multiple"></i> Analisis Beban Mengajar Guru</h5>
    <p class="text-muted">Target beban guru dihitung dari total jam mapel yang diajarkan</p>
    <div class="table-responsive">
        <table class="table table-bordered analysis-table guru-beban-table">
            <thead>
                <tr><th>No</th><th>Nama Guru</th><th>Aktual Jam</th><th>Target Jam</th><th>Selisih</th><th>Status</th><th>Progress</th></tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($beban_guru_aktual as $guru => $jam_aktual)
                    @php
                        $target = $target_beban_guru[$guru] ?? 20;
                        $selisih = $jam_aktual - $target;
                        $persen = min(100, ($jam_aktual / max($target, 1)) * 100);
                        $status_class = $selisih == 0 ? 'status-ok' : 'status-mismatch';
                        $status_text = $selisih == 0 ? '✅ Sesuai' : ($selisih > 0 ? '⚠️ Kelebihan ' . $selisih . ' jam' : '⚠️ Kekurangan ' . abs($selisih) . ' jam');
                        $progress_class = $persen > 105 ? 'danger' : ($persen < 95 ? 'warning' : '');
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td><strong>{{ $guru }}</strong></td>
                        <td><strong>{{ $jam_aktual }} jam</strong></td>
                        <td>{{ $target }} jam</td>
                        <td class="{{ $status_class }}">{{ $selisih >= 0 ? '+' : '' }}{{ $selisih }} jam</td>
                        <td>{{ $status_text }}</td>
                        <td style="width: 200px;">
                            <div class="progress-bar">
                                <div class="progress-fill {{ $progress_class }}" style="width: {{ $persen }}%;">{{ number_format($persen, 1) }}%</div>
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
            <li>Total jam mengajar semua guru: <strong>{{ array_sum($beban_guru_aktual) }} jam</strong></li>
            <li>Rata-rata beban per guru: <strong>{{ round(array_sum($beban_guru_aktual) / max(count($beban_guru_aktual), 1)) }} jam</strong></li>
            <li>Jumlah guru: <strong>{{ count($beban_guru_aktual) }}</strong></li>
        </ul>
    </div>
</div>
