@if(isset($fullJadwal) && !empty($fullJadwal))
    @php
        $kelasList = $fullJadwal['kelas_list'];
        $jadwalData = $fullJadwal['jadwal_data'];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    @endphp

    <table class="jadwal-table">
        <thead>
            <tr>
                <th style="min-width: 80px">Hari / Jam</th>
                <th style="min-width: 80px">Waktu</th>
                @foreach($kelasList as $kelas)
                    <th style="min-width: 150px">{{ $kelas->nama_kelas }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($hariList as $hari)
                @php $rows = $jadwalData[$hari] ?? []; @endphp
                @foreach($rows as $idx => $row)
                    <tr>
                        @if($idx == 0)
                            <td rowspan="{{ count($rows) }}"><strong>{{ $hari }}</strong></td>
                        @endif

                        <td class="jam-cell">
                            {{ $row->jam_ke }}
                            @if($row->waktu_mulai && $row->waktu_selesai)
                                <br><small>{{ substr($row->waktu_mulai, 0, 5) }}-{{ substr($row->waktu_selesai, 0, 5) }}</small>
                            @endif
                        </td>

                        @if($row->is_keterangan)
                            @php $isKuning = $row->keterangan == 'Istirahat' || $row->keterangan == 'Ishoma'; @endphp
                            <td colspan="{{ count($kelasList) }}" class="keterangan-cell {{ $isKuning ? 'kuning-cerah' : '' }}">
                                <div class="keterangan-text"><strong>{{ $row->keterangan }}</strong></div>
                            </td>
                        @else
                            @foreach($row->kelas_data as $kelasItem)
                                @if($kelasItem->nama_mapel == '-')
                                    <td class="text-muted">-</td>
                                @else
                                    <td>
                                        <strong>{{ $kelasItem->nama_mapel }}</strong><br>
                                        <small>{{ $kelasItem->nama_guru }}</small>
                                    </td>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info text-center">Belum ada jadwal yang digenerate</div>
@endif
