<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Jadwal Lengkap</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            color: #666;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            /* Mencegah tabel terlalu lebar */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
            font-size: 8px;
        }

        .hari-cell {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .jam-cell {
            white-space: nowrap;
            font-size: 8px;
        }

        .mapel-cell {
            font-size: 8px;
            line-height: 1.3;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        @page {
            size: landscape;
            margin: 0.5cm;
        }

        /* Gaya untuk mengulang header tabel di setiap halaman */
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>JADWAL PELAJARAN</h1>
        <p>SMPN 1 Enam Lingkung</p>
        <p>Dicetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    @php
        $kelasList = $fullJadwal['kelas_list'];
        $jadwalData = $fullJadwal['jadwal_data'];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Hitung lebar kolom
        $lebarHari = '8%';
        $lebarJam = '10%';
        $lebarKelas = 82 / count($kelasList) . '%';
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: {{ $lebarHari }}">Hari</th>
                <th style="width: {{ $lebarJam }}">Jam / Waktu</th>
                @foreach ($kelasList as $kelas)
                    <th style="width: auto">{{ $kelas->nama_kelas }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($hariList as $hariIndex => $hari)
                @php $rows = $jadwalData[$hari] ?? []; @endphp
                @if (count($rows) > 0)
                    @foreach ($rows as $rowIndex => $row)
                        @php
                            // Cek apakah ini baris keterangan (istirahat)
                            $isKeterangan = isset($row->is_keterangan) && $row->is_keterangan;
                            $keterangan = $row->keterangan ?? '';
                        @endphp
                        <tr>
                            @if ($rowIndex == 0)
                                <td class="hari-cell" rowspan="{{ count($rows) }}">
                                    <strong>{{ $hari }}</strong>
                                </td>
                            @endif

                            <!-- Kolom Jam / Waktu -->
                            <td class="jam-cell">
                                {{ $row->jam_ke ?? '' }}
                                @if (isset($row->waktu_mulai) && $row->waktu_mulai && isset($row->waktu_selesai) && $row->waktu_selesai)
                                    <br><small>{{ substr($row->waktu_mulai, 0, 5) }}-{{ substr($row->waktu_selesai, 0, 5) }}</small>
                                @endif
                            </td>

                            <!-- Kolom untuk setiap kelas -->
                            @if ($isKeterangan)
                                <td colspan="{{ count($kelasList) }}" class="keterangan-cell"
                                    style="background-color: #fff3cd;">
                                    <strong>{{ $keterangan }}</strong>
                                </td>
                            @else
                                @foreach ($row->kelas_data as $kelasItem)
                                    <td class="mapel-cell">
                                        @if ($kelasItem->nama_mapel != '-')
                                            <strong>{{ $kelasItem->nama_mapel }}</strong>
                                            @if ($kelasItem->nama_guru != '-')
                                                <br><small>{{ $kelasItem->nama_guru }}</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr class="empty-row">
                        <td colspan="{{ count($kelasList) + 2 }}"
                            style="text-align: center; background-color: #f9f9f9;">
                            Tidak ada jadwal untuk hari {{ $hari }}
                        </td>
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
