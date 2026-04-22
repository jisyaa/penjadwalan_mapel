@php
    $target_mapel = session('target_mapel', []);
    $jam_mapel_analisis = [];
    foreach ($jadwal as $j) {
        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) continue;
        $kelas = $j['kelas'];
        $mapel = $j['mapel'];
        if (!isset($jam_mapel_analisis[$kelas])) $jam_mapel_analisis[$kelas] = [];
        $jam_mapel_analisis[$kelas][$mapel] = ($jam_mapel_analisis[$kelas][$mapel] ?? 0) + 1;
    }
@endphp

<div id="tab-jam-mapel" class="tab-content">
    <h5><i class="mdi mdi-chart-bar"></i> Analisis Pemenuhan Jam Mata Pelajaran per Kelas</h5>
    <p class="text-muted">Target jam berdasarkan tabel mapel di database</p>
    <div class="table-responsive">
        <table class="table table-bordered analysis-table">
            <thead>
                <tr>
                    <th>Kelas</th>
                    @foreach ($target_mapel as $mapel => $target)
                        <th>{{ $mapel }}<br><small class="text-muted">Target: {{ $target }} jam</small></th>
                    @endforeach
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $semua_kelas = array_keys($jam_mapel_analisis); sort($semua_kelas); @endphp
                @foreach ($semua_kelas as $kelas)
                    @php $total_jam = 0; $semua_sesuai = true; @endphp
                    <tr>
                        <td><strong>{{ $kelas }}</strong></td>
                        @foreach ($target_mapel as $mapel => $target)
                            @php
                                $aktual = $jam_mapel_analisis[$kelas][$mapel] ?? 0;
                                $total_jam += $aktual;
                                $selisih = $aktual - $target;
                                $sesuai = $aktual == $target;
                                if (!$sesuai) $semua_sesuai = false;
                            @endphp
                            <td class="{{ $sesuai ? 'status-ok' : 'status-mismatch' }}">
                                {{ $aktual }} jam
                                @if (!$sesuai)<br><small>({{ $selisih >= 0 ? '+' : '' }}{{ $selisih }})</small>@endif
                            </td>
                        @endforeach
                        <td><strong>{{ $total_jam }} / 40 jam</strong></td>
                        <td>@if($semua_sesuai)<span class="badge bg-success">✅ Sesuai</span>@else<span class="badge bg-warning">⚠️ Belum Sesuai</span>@endif</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="alert alert-info mt-2">
        <strong>📊 Keterangan Target Jam Mapel:</strong>
        <ul>@foreach ($target_mapel as $mapel => $target)<li><strong>{{ $mapel }}</strong>: {{ $target }} jam/minggu</li>@endforeach</ul>
    </div>
</div>
