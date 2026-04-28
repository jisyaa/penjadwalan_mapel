@php
    $jadwal = collect($jadwal)->sortBy(fn($item) => $item['id_waktu'] ?? 999)->toArray();
    $urutan_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    $jadwal_per_hari = [];
    $keterangan_per_hari = [];

    foreach ($jadwal as $j) {
        if (!isset($j['hari'])) {
            continue;
        }
        $hari = $j['hari'];
        $jamKey = $j['id_waktu'] ?? (is_null($j['jam'] ?? null) ? 'khusus_' . rand() : $j['jam']);

        if (!isset($jadwal_per_hari[$hari])) {
            $jadwal_per_hari[$hari] = [];
            $keterangan_per_hari[$hari] = [];
        }

        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) {
            $keterangan_per_hari[$hari][$jamKey] = [
                'teks' => $j['keterangan'],
                'warna_class' => $getWarnaByKeterangan($j['keterangan']),
                'id_waktu' => $j['id_waktu'],
                'jam_ke' => $j['jam_ke'] ?? null,
                'jam' => $j['jam'] ?? null,
                'waktu_mulai' => $j['waktu_mulai'] ?? '',
                'waktu_selesai' => $j['waktu_selesai'] ?? '',
            ];
        } else {
            if (!isset($jadwal_per_hari[$hari][$jamKey])) {
                $jadwal_per_hari[$hari][$jamKey] = [];
            }
            $jadwal_per_hari[$hari][$jamKey][$j['kelas']] = $j;
        }
    }

    // =====================================================
    // URUTKAN KELAS BERDASARKAN ID KELAS
    // =====================================================
    // Ambil daftar kelas unik beserta id_kelas dari data jadwal
    $kelasWithId = collect($jadwal)
        ->where('is_keterangan', '!=', true)
        ->whereNotNull('kelas')
        ->map(function($item) {
            return [
                'kelas' => $item['kelas'],
                'id_kelas' => $item['id_kelas'] ?? 0
            ];
        })
        ->unique('kelas')
        ->values();

    // Urutkan berdasarkan id_kelas
    $kelasWithId = $kelasWithId->sortBy('id_kelas');

    // Ambil hanya nama kelas saja untuk $kelas_list
    $kelas_list = $kelasWithId->pluck('kelas');

    // Jika tidak ada kelas, gunakan default
    if ($kelas_list->isEmpty()) {
        // Ambil semua kelas dari database dan urutkan berdasarkan id_kelas
        $kelas_list = DB::table('kelas')
            ->orderBy('id_kelas', 'asc')
            ->pluck('nama_kelas');
    }

    $jam_list_per_hari = [];
    foreach ($urutan_hari as $hari) {
        $jam_dari_jadwal = isset($jadwal_per_hari[$hari]) ? array_keys($jadwal_per_hari[$hari]) : [];
        $jam_dari_keterangan = isset($keterangan_per_hari[$hari]) ? array_keys($keterangan_per_hari[$hari]) : [];
        $semua_jam = array_unique(array_merge($jam_dari_jadwal, $jam_dari_keterangan));
        sort($semua_jam, SORT_NUMERIC);
        $jam_list_per_hari[$hari] = $semua_jam;
    }

    // Hitung bentrok
    $bentrok = [];
    foreach ($jadwal as $j) {
        if (!isset($j['hari'], $j['guru']) || empty($j['guru'])) {
            continue;
        }
        if (isset($j['is_keterangan']) && $j['is_keterangan'] === true) {
            continue;
        }
        $jamKey = $j['id_waktu'] ?? (is_null($j['jam'] ?? null) ? 'khusus' : $j['jam']);
        $key = $j['hari'] . '-' . $jamKey . '-' . $j['guru'];
        $bentrok[$key] = ($bentrok[$key] ?? 0) + 1;
    }
@endphp

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Jadwal</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 100px">Hari</th>
                                <th style="width: 120px">Jam Ke</th>
                                <th style="width: 120px">Waktu</th>
                                @foreach ($kelas_list as $kelas)
                                    <th style="min-width: 150px">{{ $kelas }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($urutan_hari as $hari)
                                @php $jam_list = $jam_list_per_hari[$hari] ?? []; @endphp
                                @if (count($jam_list) > 0)
                                    @foreach ($jam_list as $index => $jamKey)
                                        @php
                                            $isKeterangan = isset($keterangan_per_hari[$hari][$jamKey]);
                                            $keteranganData = $isKeterangan
                                                ? $keterangan_per_hari[$hari][$jamKey]
                                                : null;
                                            $displayJam = '';
                                            $showJamKe = false;
                                            $waktuMulai = '';
                                            $waktuSelesai = '';
                                            $waktuRange = '';

                                            if ($isKeterangan && $keteranganData) {
                                                $jamKe = $keteranganData['jam_ke'] ?? ($keteranganData['jam'] ?? null);
                                                if (!is_null($jamKe) && $jamKe !== '' && $jamKe !== 0) {
                                                    $displayJam = $jamKe;
                                                    $showJamKe = true;
                                                }
                                                // Ambil waktu dari data keterangan
                                                $waktuMulai = $keteranganData['waktu_mulai'] ?? '';
                                                $waktuSelesai = $keteranganData['waktu_selesai'] ?? '';
                                            } else {
                                                $firstJadwal = isset($jadwal_per_hari[$hari][$jamKey])
                                                    ? reset($jadwal_per_hari[$hari][$jamKey])
                                                    : null;
                                                if (
                                                    $firstJadwal &&
                                                    isset($firstJadwal['jam']) &&
                                                    !is_null($firstJadwal['jam']) &&
                                                    $firstJadwal['jam'] !== ''
                                                ) {
                                                    $displayJam = $firstJadwal['jam'];
                                                    $showJamKe = true;
                                                } elseif (is_numeric($jamKey)) {
                                                    $displayJam = $jamKey;
                                                    $showJamKe = true;
                                                }
                                                // Ambil waktu dari data jadwal
                                                if ($firstJadwal) {
                                                    $waktuMulai = $firstJadwal['waktu_mulai'] ?? '';
                                                    $waktuSelesai = $firstJadwal['waktu_selesai'] ?? '';
                                                }
                                            }

                                            // Format waktu range
                                            if ($waktuMulai && $waktuSelesai) {
                                                $waktuRange =
                                                    '<br><small>' .
                                                    substr($waktuMulai, 0, 5) .
                                                    '-' .
                                                    substr($waktuSelesai, 0, 5) .
                                                    '</small>';
                                            }

                                            $colspanCount = $kelas_list->count();
                                        @endphp
                                        <tr>
                                            @if ($loop->first)
                                                <td rowspan="{{ count($jam_list) }}">
                                                    <strong>{{ $hari }}</strong>
                                                </td>
                                            @endif

                                            <td class="text-center">
                                                @if ($showJamKe && $displayJam !== '')
                                                    <strong>{{ $displayJam }}</strong>
                                                @else
                                                    <span style="opacity: 0.3;"></span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                    {!! $waktuRange !!}
                                            </td>

                                            @if ($isKeterangan)
                                                <td colspan="{{ $colspanCount }}"
                                                    class="keterangan-cell {{ $keteranganData['warna_class'] }}"
                                                    style="text-align: center; vertical-align: middle;">
                                                    <div class="keterangan-text">
                                                        <strong>{{ $keteranganData['teks'] }}</strong>
                                                    </div>
                                                </td>
                                            @else
                                                @foreach ($kelas_list as $kelas)
                                                    @php
                                                        $currentData = $jadwal_per_hari[$hari][$jamKey][$kelas] ?? null;
                                                        $currentGuruMapelId = $currentData['id_guru_mapel'] ?? '';
                                                        $isBentrok =
                                                            isset($currentData) &&
                                                            isset(
                                                                $bentrok[
                                                                    $currentData['hari'] .
                                                                        '-' .
                                                                        $jamKey .
                                                                        '-' .
                                                                        $currentData['guru']
                                                                ],
                                                            ) &&
                                                            $bentrok[
                                                                $currentData['hari'] .
                                                                    '-' .
                                                                    $jamKey .
                                                                    '-' .
                                                                    $currentData['guru']
                                                            ] > 1;
                                                    @endphp
                                                    <td class="text-center editable-cell-history"
                                                        data-key="{{ $kelas }}_{{ $hari }}_{{ $jamKey }}_{{ $currentData['id_waktu'] ?? '' }}"
                                                        data-kelas="{{ $kelas }}"
                                                        data-hari="{{ $hari }}" data-jam="{{ $jamKey }}"
                                                        data-id-waktu="{{ $currentData['id_waktu'] ?? '' }}"
                                                        data-original-id="{{ $currentGuruMapelId }}"
                                                        data-current-id="{{ $currentGuruMapelId }}">

                                                        <div class="cell-display {{ $isBentrok ? 'bentrok' : '' }}"
                                                            style="cursor: pointer; min-width: 150px;"
                                                            onclick="showDropdownHistory(this)">
                                                            @if ($currentData)
                                                                <strong>{{ $currentData['guru'] }}</strong><br>
                                                                <small>{{ $currentData['mapel'] }}</small>
                                                                @if ($isBentrok)
                                                                    <br><span class="badge bg-danger">Bentrok!</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">- Klik untuk isi -</span>
                                                            @endif
                                                        </div>

                                                        <select class="dropdown-select-history"
                                                            style="display: none; width: 100%;"
                                                            onchange="trackChange(this)">
                                                            <option value="">-- Kosongkan --</option>
                                                        </select>
                                                    </td>
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><strong>{{ $hari }}</strong></td>
                                        <td colspan="{{ $kelas_list->count() + 1 }}" class="text-muted text-center">
                                            Tidak ada jadwal untuk hari {{ $hari }}
                                        </td>
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
