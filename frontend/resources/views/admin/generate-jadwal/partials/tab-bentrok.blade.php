<div id="tab-bentrok" class="tab-content active">
    @php
        $bentrok_detail = [];
        foreach ($jadwal as $j) {
            if (!isset($j['hari'], $j['jam'], $j['guru']) || empty($j['guru'])) continue;
            $key = $j['hari'] . '-' . $j['jam'] . '-' . $j['guru'];
            if (!isset($bentrok_detail[$key])) {
                $bentrok_detail[$key] = ['guru' => $j['guru'], 'hari' => $j['hari'], 'jam' => $j['jam'], 'kelas' => []];
            }
            $bentrok_detail[$key]['kelas'][] = $j['kelas'];
        }
        $bentrok_only = array_filter($bentrok_detail, fn($item) => count($item['kelas']) > 1);
    @endphp

    @if (count($bentrok_only) > 0)
        <div class="alert alert-danger">
            <h5><i class="mdi mdi-alert-circle"></i> Ditemukan {{ count($bentrok_only) }} Bentrok Guru!</h5>
            <p>Berikut adalah detail bentrok yang terjadi:</p>
        </div>
        <table class="table table-bordered analysis-table">
            <thead>
                <tr><th>No</th><th>Hari</th><th>Jam Ke</th><th>Guru</th><th>Jumlah Kelas</th><th>Kelas yang Bentrok</th></tr>
            </thead>
            <tbody>
                @foreach ($bentrok_only as $bentrok)
                    <tr class="table-danger">
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $bentrok['hari'] }}</strong></td>
                        <td><strong>{{ $bentrok['jam'] }}</strong></td>
                        <td><strong>{{ $bentrok['guru'] }}</strong></td>
                        <td><span class="badge bg-danger">{{ count($bentrok['kelas']) }}</span></td>
                        <td>{{ implode(', ', $bentrok['kelas']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="alert alert-warning mt-2">
            <strong>💡 Solusi:</strong> Bentrok terjadi karena guru mengajar di beberapa kelas pada waktu yang sama.
        </div>
    @else
        <div class="alert alert-success">
            <h5><i class="mdi mdi-check-circle"></i> ✅ Tidak Ada Bentrok Guru!</h5>
            <p>Semua guru mengajar di waktu yang berbeda untuk setiap kelas.</p>
        </div>
    @endif
</div>
