<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalMaster;

class JadwalPreviewController extends Controller
{
    public function index()
    {
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        // Data untuk filter
        $kelasList = DB::table('kelas')->orderBy('id_kelas')->get();
        $guruList = DB::table('guru')->orderBy('nama_guru')->get();

        // Default: Full Jadwal (semua kelas)
        $fullJadwal = $this->getFullJadwalData($jadwalAktif);

        $semuaWaktu = DB::table('waktu')->orderBy('id_waktu')->get();

        return view('landing.partials.jadwal-preview', compact('jadwalAktif', 'kelasList', 'guruList', 'fullJadwal', 'semuaWaktu'));
    }

    /**
     * Get Full Jadwal (Semua Kelas)
     */
    public function getFullJadwal(Request $request)
    {
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return response()->json(['success' => false, 'message' => 'Belum ada jadwal aktif', 'data' => []]);
        }

        $fullJadwal = $this->getFullJadwalData($jadwalAktif);

        return response()->json([
            'success' => true,
            'data' => $fullJadwal
        ]);
    }

    /**
     * Get Jadwal Berdasarkan Kelas
     */
    public function getJadwalByKelas(Request $request)
    {
        $idKelas = $request->input('id_kelas');
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return response()->json(['success' => false, 'message' => 'Belum ada jadwal aktif', 'data' => []]);
        }

        $kelasData = DB::table('kelas')->where('id_kelas', $idKelas)->first();
        if (!$kelasData) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan', 'data' => []]);
        }

        $jadwal = $this->getJadwalByKelasData($jadwalAktif, $kelasData->id_kelas);

        return response()->json([
            'success' => true,
            'data' => $jadwal,
            'kelas' => $kelasData->nama_kelas
        ]);
    }

    /**
     * Get Jadwal Berdasarkan Guru
     */
    public function getJadwalByGuru(Request $request)
    {
        $idGuru = $request->input('id_guru');
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return response()->json(['success' => false, 'message' => 'Belum ada jadwal aktif', 'data' => []]);
        }

        $guruData = DB::table('guru')->where('id_guru', $idGuru)->first();
        if (!$guruData) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan', 'data' => []]);
        }

        $jadwal = $this->getJadwalByGuruData($jadwalAktif, $idGuru);

        return response()->json([
            'success' => true,
            'data' => $jadwal,
            'guru' => $guruData->nama_guru
        ]);
    }

    /**
     * Get Full Jadwal (Matrix Kelas vs Waktu)
     */
    private function getFullJadwalData($jadwalAktif)
    {
        if (!$jadwalAktif) return [];

        // Ambil semua kelas
        $kelasList = DB::table('kelas')->orderBy('id_kelas')->get();

        // Ambil semua waktu
        $semuaWaktu = DB::table('waktu')->orderBy('id_waktu')->get();

        // Ambil semua jadwal
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

        // Buat mapping jadwal per id_waktu dan id_kelas
        $jadwalMap = [];
        foreach ($jadwal as $item) {
            $key = $item->id_waktu . '_' . $item->id_kelas;
            $jadwalMap[$key] = $item;
        }

        // Kelompokkan per hari
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
                    } elseif (!empty($waktu->keterangan)) {
                        $row->kelas_data[] = (object)[
                            'kelas' => $kelas->nama_kelas,
                            'nama_mapel' => $waktu->keterangan,
                            'nama_guru' => '-'
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

        return [
            'kelas_list' => $kelasList,
            'jadwal_data' => $result
        ];
    }

    /**
     * Get Jadwal Berdasarkan Kelas (satu kelas)
     */
    private function getJadwalByKelasData($jadwalAktif, $idKelas)
    {
        if (!$jadwalAktif || !$idKelas) return [];

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
            } else {
                $result[] = (object)[
                    'hari' => $waktu->hari,
                    'jam_ke' => $waktu->jam_ke,
                    'waktu_mulai' => $waktu->waktu_mulai,
                    'waktu_selesai' => $waktu->waktu_selesai,
                    'nama_mapel' => '-',
                    'nama_guru' => '-',
                    'is_keterangan' => false
                ];
            }
        }

        return $result;
    }

    /**
     * Get Jadwal Berdasarkan Guru
     */
    private function getJadwalByGuruData($jadwalAktif, $idGuru)
    {
        if (!$jadwalAktif || !$idGuru) return [];

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
