<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kelas {{ $kelasData->nama_kelas }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
        }
        .keterangan-cell {
            background-color: #e8f4fd;
        }
        .kuning-cerah {
            background-color: #fff3cd;
        }
        .jam-cell {
            white-space: nowrap;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        @page {
            size: landscape;
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>JADWAL PELAJARAN</h1>
        <p>Kelas {{ $kelasData->nama_kelas }}</p>
        <p>SMPN 1 Enam Lingkung</p>
    </div>

    @php
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];
        foreach ($jadwal as $item) {
            $jadwalPerHari[$item->hari][] = $item;
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 100px">Hari</th>
                <th style="width: 100px">Jam / Waktu</th>
                <th>Mata Pelajaran</th>
                <th style="width: 150px">Guru</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hariList as $hari)
                @php $items = $jadwalPerHari[$hari] ?? []; @endphp
                @if(count($items) > 0)
                    @foreach($items as $idx => $item)
                        <tr>
                            @if($idx == 0)
                                <td rowspan="{{ count($items) }}"><strong>{{ $hari }}</strong></td>
                            @endif
                            <td class="jam-cell">
                                {{ $item->jam_ke }}
                                @if($item->waktu_mulai && $item->waktu_selesai)
                                    <br><small>{{ substr($item->waktu_mulai, 0, 5) }}-{{ substr($item->waktu_selesai, 0, 5) }}</small>
                                @endif
                            </td>
                            @if($item->is_keterangan)
                                <td colspan="2" class="keterangan-cell {{ $item->nama_mapel == 'Istirahat' || $item->nama_mapel == 'Ishoma' ? 'kuning-cerah' : '' }}">
                                    <strong>{{ $item->nama_mapel }}</strong>
                                </td>
                            @else
                                <td><strong>{{ $item->nama_mapel }}</strong></td>
                                <td>{{ $item->nama_guru }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $hari }}</td>
                        <td colspan="3">Tidak ada jadwal</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
