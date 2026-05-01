<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\JadwalMaster;

class PrintController extends Controller
{
    public function printFullJadwal()
    {
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return redirect()->back()->with('error', 'Tidak ada jadwal aktif');
        }

        $fullJadwal = $this->getFullJadwalData($jadwalAktif);

        $pdf = PDF::loadView('pdf.full-jadwal', compact('fullJadwal'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('jadwal_lengkap.pdf');
    }

    public function printJadwalByKelas(Request $request)
    {
        $idKelas = $request->input('id_kelas');
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return redirect()->back()->with('error', 'Tidak ada jadwal aktif');
        }

        $kelasData = DB::table('kelas')->where('id_kelas', $idKelas)->first();
        if (!$kelasData) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        $jadwal = $this->getJadwalByKelasData($jadwalAktif, $kelasData->id_kelas);

        $pdf = PDF::loadView('pdf.jadwal-kelas', compact('jadwal', 'kelasData'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('jadwal_kelas_' . $kelasData->nama_kelas . '.pdf');
    }

    public function printJadwalByGuru(Request $request)
    {
        $idGuru = $request->input('id_guru');
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return redirect()->back()->with('error', 'Tidak ada jadwal aktif');
        }

        $guruData = DB::table('guru')->where('id_guru', $idGuru)->first();
        if (!$guruData) {
            return redirect()->back()->with('error', 'Guru tidak ditemukan');
        }

        $jadwal = $this->getJadwalByGuruData($jadwalAktif, $idGuru);

        $pdf = PDF::loadView('pdf.jadwal-guru', compact('jadwal', 'guruData'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('jadwal_guru_' . $guruData->nama_guru . '.pdf');
    }

    private function getFullJadwalData($jadwalAktif)
    {
        $kelasList = DB::table('kelas')->orderBy('id_kelas')->get();
        $semuaWaktu = DB::table('waktu')->orderBy('id_waktu')->get();

        $jadwal = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->select(
                'k.id_kelas',
                'k.nama_kelas',
                'w.id_waktu',
                'w.hari',
                'w.jam_ke',
                'w.waktu_mulai',
                'w.waktu_selesai',
                'w.keterangan',
                'm.nama_mapel',
                'g.nama_guru'
            )
            ->get();

        $jadwalMap = [];
        foreach ($jadwal as $item) {
            $key = $item->id_waktu . '_' . $item->id_kelas;
            $jadwalMap[$key] = $item;
        }

        $result = [];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        foreach ($hariList as $hari) {
            $waktuPerHari = $semuaWaktu->filter(function($w) use ($hari) {
                return $w->hari == $hari;
            });

            $result[$hari] = [];
            foreach ($waktuPerHari as $waktu) {
                $row = (object)[
                    'jam_ke' => $waktu->jam_ke,
                    'waktu_mulai' => $waktu->waktu_mulai,
                    'waktu_selesai' => $waktu->waktu_selesai,
                    'is_keterangan' => !empty($waktu->keterangan),
                    'keterangan' => $waktu->keterangan,
                    'kelas_data' => []
                ];

                foreach ($kelasList as $kelas) {
                    $key = $waktu->id_waktu . '_' . $kelas->id_kelas;
                    if (isset($jadwalMap[$key])) {
                        $item = $jadwalMap[$key];
                        $row->kelas_data[] = (object)[
                            'kelas' => $kelas->nama_kelas,
                            'nama_mapel' => $item->nama_mapel,
                            'nama_guru' => $item->nama_guru
                        ];
                    } else {
                        $row->kelas_data[] = (object)[
                            'kelas' => $kelas->nama_kelas,
                            'nama_mapel' => '-',
                            'nama_guru' => '-'
                        ];
                    }
                }
                $result[$hari][] = $row;
            }
        }

        return ['kelas_list' => $kelasList, 'jadwal_data' => $result];
    }

    private function getJadwalByKelasData($jadwalAktif, $idKelas)
    {
        $semuaWaktu = DB::table('waktu')->orderBy('id_waktu')->get();

        $jadwal = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->where('gm.id_kelas', $idKelas)
            ->select(
                'w.id_waktu',
                'w.hari',
                'w.jam_ke',
                'w.waktu_mulai',
                'w.waktu_selesai',
                'w.keterangan',
                'm.nama_mapel',
                'g.nama_guru'
            )
            ->orderByRaw("FIELD(w.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('w.id_waktu')
            ->get();

        $jadwalMap = [];
        foreach ($jadwal as $item) {
            $jadwalMap[$item->id_waktu] = $item;
        }

        $result = [];
        foreach ($semuaWaktu as $waktu) {
            if (isset($jadwalMap[$waktu->id_waktu])) {
                $item = $jadwalMap[$waktu->id_waktu];
                $result[] = (object)[
                    'hari' => $waktu->hari,
                    'jam_ke' => $waktu->jam_ke,
                    'waktu_mulai' => $waktu->waktu_mulai,
                    'waktu_selesai' => $waktu->waktu_selesai,
                    'nama_mapel' => $item->nama_mapel,
                    'nama_guru' => $item->nama_guru,
                    'is_keterangan' => false
                ];
            } elseif (!empty($waktu->keterangan)) {
                $result[] = (object)[
                    'hari' => $waktu->hari,
                    'jam_ke' => $waktu->jam_ke,
                    'waktu_mulai' => $waktu->waktu_mulai,
                    'waktu_selesai' => $waktu->waktu_selesai,
                    'nama_mapel' => $waktu->keterangan,
                    'nama_guru' => '-',
                    'is_keterangan' => true
                ];
            }
        }

        return $result;
    }

    private function getJadwalByGuruData($jadwalAktif, $idGuru)
    {
        $jadwal = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->where('gm.id_guru', $idGuru)
            ->select(
                'k.nama_kelas',
                'w.hari',
                'w.jam_ke',
                'w.waktu_mulai',
                'w.waktu_selesai',
                'm.nama_mapel'
            )
            ->orderBy('k.nama_kelas')
            ->orderByRaw("FIELD(w.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('w.jam_ke')
            ->get();

        $result = [];
        foreach ($jadwal as $item) {
            if (!isset($result[$item->nama_kelas])) {
                $result[$item->nama_kelas] = [];
            }
            $result[$item->nama_kelas][] = $item;
        }

        return $result;
    }
}
