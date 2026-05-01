<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jadwal Guru {{ $guruData->nama_guru }}</title>
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
        .kelas-card {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .kelas-title {
            background-color: #4472C4;
            color: white;
            padding: 8px;
            margin: 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        @page {
            size: portrait;
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>JADWAL MENGAJAR</h1>
        <p>Guru: {{ $guruData->nama_guru }}</p>
        <p>SMPN 1 Enam Lingkung</p>
    </div>

    @foreach($jadwal as $kelas => $jadwalKelas)
        <div class="kelas-card">
            <h3 class="kelas-title">Kelas {{ $kelas }}</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px">Hari</th>
                        <th style="width: 100px">Jam / Waktu</th>
                        <th>Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                        $jadwalPerHari = [];
                        foreach ($jadwalKelas as $item) {
                            $jadwalPerHari[$item->hari][] = $item;
                        }
                    @endphp
                    @foreach($hariList as $hari)
                        @php $items = $jadwalPerHari[$hari] ?? []; @endphp
                        @if(count($items) > 0)
                            @foreach($items as $idx => $item)
                                <tr>
                                    @if($idx == 0)
                                        <td rowspan="{{ count($items) }}"><strong>{{ $hari }}</strong></td>
                                    @endif
                                    <td>
                                        {{ $item->jam_ke }}
                                        @if($item->waktu_mulai && $item->waktu_selesai)
                                            <br><small>{{ substr($item->waktu_mulai, 0, 5) }}-{{ substr($item->waktu_selesai, 0, 5) }}</small>
                                        @endif
                                    </td>
                                    <td><strong>{{ $item->nama_mapel }}</strong></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>{{ $hari }}</td>
                                <td colspan="2">Tidak ada jadwal</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
