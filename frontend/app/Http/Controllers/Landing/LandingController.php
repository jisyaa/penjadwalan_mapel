<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalMaster;

class LandingController extends Controller
{
    /**
     * Halaman Home / Beranda
     */
    public function index()
    {
        // Statistik utama
        $totalKelas = DB::table('kelas')->count();
        $totalGuru = DB::table('guru')->count();
        $totalMapel = DB::table('mapel')->count();

        // Jadwal aktif
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();
        $totalJadwal = $jadwalAktif ? DB::table('jadwal')->where('id_master', $jadwalAktif->id_master)->count() : 0;

        // Data untuk preview singkat (6 jadwal terbaru)
        $previewJadwal = [];
        if ($jadwalAktif) {
            $previewJadwal = DB::table('jadwal as j')
                ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
                ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
                ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
                ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
                ->where('j.id_master', $jadwalAktif->id_master)
                ->select('k.nama_kelas', 'w.hari', 'w.jam_ke', 'm.nama_mapel')
                ->limit(6)
                ->get();
        }

        return view('landing.partials.home', compact(
            'totalKelas',
            'totalGuru',
            'totalMapel',
            'totalJadwal',
            'previewJadwal'
        ));
    }

    /**
     * API untuk preview jadwal (AJAX)
     */
    public function getJadwalPreview(Request $request)
    {
        $kelas = $request->input('kelas');
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();

        if (!$jadwalAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada jadwal aktif',
                'data' => []
            ]);
        }

        $kelasData = DB::table('kelas')->where('nama_kelas', $kelas)->first();
        if (!$kelasData) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan',
                'data' => []
            ]);
        }

        // Ambil semua waktu untuk hari ini (termasuk yang ada keterangan)
        $semuaWaktu = DB::table('waktu')
            ->orderBy('id_waktu')
            ->get();

        // Ambil jadwal untuk kelas tersebut
        $jadwal = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->where('gm.id_kelas', $kelasData->id_kelas)
            ->select(
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

        // Buat mapping jadwal per hari dan jam
        $jadwalMap = [];
        foreach ($jadwal as $item) {
            $key = $item->hari . '_' . $item->jam_ke;
            $jadwalMap[$key] = $item;
        }

        // Gabungkan dengan semua waktu (termasuk yang tidak ada jadwal/keterangan)
        $result = [];
        foreach ($semuaWaktu as $waktu) {
            $key = $waktu->hari . '_' . $waktu->jam_ke;

            if (isset($jadwalMap[$key])) {
                $item = $jadwalMap[$key];
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
                // Slot keterangan (Upacara, Istirahat, Ishoma)
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
                // Slot kosong
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

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function tentang()
    {
        return view('landing.partials.tentang');
    }

    /**
     * Halaman Fitur
     */
    public function fitur()
    {
        return view('landing.partials.fitur');
    }

    public function kontak()
    {
        return view('landing.partials.kontak');
    }

    /**
     * Halaman Preview Jadwal
     */
    public function jadwalPreview()
    {
        $kelasList = DB::table('kelas')->orderBy('id_kelas')->get();
        return view('landing.partials.jadwal_preview', compact('kelasList'));
    }

    /**
     * Halaman Statistik
     */
    // Di PageController.php
    public function statistik()
    {
        $totalKelas = DB::table('kelas')->count();
        $totalGuru = DB::table('guru')->count();
        $totalMapel = DB::table('mapel')->count();

        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();
        $totalJadwal = $jadwalAktif ? DB::table('jadwal')->where('id_master', $jadwalAktif->id_master)->count() : 0;

        // Hitung bentrok
        $totalBentrok = 0;
        if ($jadwalAktif) {
            $totalBentrok = DB::table('jadwal as j')
                ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
                ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
                ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
                ->where('j.id_master', $jadwalAktif->id_master)
                ->select('g.nama_guru', 'w.hari', 'w.jam_ke', DB::raw('COUNT(*) as total'))
                ->groupBy('g.nama_guru', 'w.hari', 'w.jam_ke')
                ->having('total', '>', 1)
                ->count();
        }

        // Data untuk grafik beban guru
        $bebanGuru = [];
        if ($jadwalAktif) {
            $beban = DB::table('jadwal as j')
                ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
                ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
                ->where('j.id_master', $jadwalAktif->id_master)
                ->select('g.nama_guru', DB::raw('COUNT(*) as total_jam'))
                ->groupBy('g.nama_guru')
                ->orderBy('total_jam', 'desc')
                ->get();

            foreach ($beban as $item) {
                $bebanGuru[] = ['nama' => $item->nama_guru, 'jam' => $item->total_jam];
                $guruNames[] = $item->nama_guru;
                $guruJams[] = $item->total_jam;
            }
        }

        return view('landing.partials.statistik', compact(
            'totalKelas',
            'totalGuru',
            'totalMapel',
            'totalJadwal',
            'totalBentrok',
            'bebanGuru',
            'guruNames',
            'guruJams'
        ));
    }
}
