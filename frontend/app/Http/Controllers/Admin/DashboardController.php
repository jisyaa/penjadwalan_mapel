<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\JadwalMaster;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama
        $totalKelas = DB::table('kelas')->count();
        $totalGuru = DB::table('guru')->count();
        $totalMapel = DB::table('mapel')->count();

        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();
        $isJadwalAktif = $jadwalAktif ? true : false;

        // 2. Hitung total jam mengajar dari jadwal aktif
        $totalJamMengajar = 0;
        if ($jadwalAktif) {
            $totalJamMengajar = Jadwal::where('id_master', $jadwalAktif->id_master)->count();
        }

        // 3. Bentrok Guru dari Jadwal Aktif
        $bentrokGuru = $this->hitungBentrokGuru($jadwalAktif);

        // 4. Beban Guru (Aktual vs Target)
        $bebanGuru = $this->hitungBebanGuru($jadwalAktif);
        $guruOverload = array_filter($bebanGuru, function ($guru) {
            return $guru['selisih'] > 0 && $guru['aktual'] > 24;
        });
        $guruUnderload = array_filter($bebanGuru, function ($guru) {
            return $guru['selisih'] < 0 && $guru['aktual'] < 20;
        });
        $guruIdeal = array_filter($bebanGuru, function ($guru) {
            return $guru['selisih'] == 0 || ($guru['aktual'] >= 20 && $guru['aktual'] <= 24);
        });

        // 5. Data untuk grafik (Nama Guru dan Jam)
        $guruNames = array_column($bebanGuru, 'nama');
        $guruJams = array_column($bebanGuru, 'aktual');

        // 6. Pemenuhan Jam Mapel per Kelas
        $pemenuhanMapel = $this->hitungPemenuhanMapel($jadwalAktif);
        $rataRataPemenuhan = $this->hitungRataRataPemenuhan($pemenuhanMapel);

        // 7. Jadwal Hari Ini
        $hariIni = $this->getNamaHari(date('N'));
        $jadwalHariIni = $this->getJadwalHariIni($jadwalAktif, $hariIni);

        // 8. History Generate Jadwal (5 terakhir)
        $historyJadwal = JadwalMaster::orderBy('tanggal_generate', 'desc')->limit(5)->get();

        // 9. Distribusi Jam per Hari
        $distribusiJam = $this->hitungDistribusiJam($jadwalAktif);

        // 10. Statistik Cepat
        $statCepat = $this->hitungStatCepat($pemenuhanMapel, $bebanGuru);

        return view('admin.tools.dashboard', compact(
            'totalKelas',
            'totalGuru',
            'totalMapel',
            'jadwalAktif',
            'isJadwalAktif',
            'totalJamMengajar',
            'bentrokGuru',
            'bebanGuru',
            'guruOverload',
            'guruUnderload',
            'guruIdeal',
            'guruNames',
            'guruJams',
            'pemenuhanMapel',
            'rataRataPemenuhan',
            'hariIni',
            'jadwalHariIni',
            'historyJadwal',
            'distribusiJam',
            'statCepat'
        ));
    }

    /**
     * Hitung bentrok guru dari jadwal aktif
     */
    private function hitungBentrokGuru($jadwalAktif)
    {
        if (!$jadwalAktif) return [];

        $jadwalDetails = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->select('g.nama_guru', 'w.hari', 'w.jam_ke')
            ->get();

        $bentrokMap = [];
        foreach ($jadwalDetails as $item) {
            $key = $item->hari . '_' . $item->jam_ke . '_' . $item->nama_guru;
            if (!isset($bentrokMap[$key])) {
                $bentrokMap[$key] = [
                    'guru' => $item->nama_guru,
                    'hari' => $item->hari,
                    'jam' => $item->jam_ke,
                    'count' => 0
                ];
            }
            $bentrokMap[$key]['count']++;
        }

        // Filter yang count > 1
        return array_values(array_filter($bentrokMap, function ($item) {
            return $item['count'] > 1;
        }));
    }

    /**
     * Hitung beban guru (aktual vs target)
     */
    private function hitungBebanGuru($jadwalAktif)
    {
        // Target beban guru dari mapel yang diajar
        $targetBeban = DB::table('guru_mapel as gm')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->where('gm.aktif', 'aktif')
            ->select('g.id_guru', 'g.nama_guru', DB::raw('SUM(m.jam_per_minggu) as target_jam'))
            ->groupBy('g.id_guru', 'g.nama_guru')
            ->get()
            ->keyBy('id_guru');

        // Aktual beban guru dari jadwal aktif
        $aktualBeban = [];
        if ($jadwalAktif) {
            $aktualData = DB::table('jadwal as j')
                ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
                ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
                ->where('j.id_master', $jadwalAktif->id_master)
                ->select('g.id_guru', 'g.nama_guru', DB::raw('COUNT(*) as aktual_jam'))
                ->groupBy('g.id_guru', 'g.nama_guru')
                ->get();

            foreach ($aktualData as $item) {
                $aktualBeban[$item->id_guru] = $item->aktual_jam;
            }
        }

        $result = [];
        foreach ($targetBeban as $idGuru => $target) {
            $aktual = $aktualBeban[$idGuru] ?? 0;
            $selisih = $aktual - $target->target_jam;

            $result[] = [
                'id_guru' => $idGuru,
                'nama' => $target->nama_guru,
                'target' => $target->target_jam,
                'aktual' => $aktual,
                'selisih' => $selisih,
                'persen' => $target->target_jam > 0 ? round(($aktual / $target->target_jam) * 100, 1) : 0,
                'status' => $this->getStatusBeban($selisih, $aktual)
            ];
        }

        // Urutkan berdasarkan beban tertinggi
        usort($result, function ($a, $b) {
            return $b['aktual'] - $a['aktual'];
        });

        return $result;
    }

    private function getStatusBeban($selisih, $aktual)
    {
        if ($aktual == 0) return 'Belum Ada Jadwal';
        if ($selisih > 0) return 'Overload';
        if ($selisih < 0) return 'Underload';
        return 'Ideal';
    }

    /**
     * Hitung pemenuhan jam mapel per kelas
     */
    private function hitungPemenuhanMapel($jadwalAktif)
    {
        if (!$jadwalAktif) return [];

        // Target jam mapel
        $targetMapel = DB::table('mapel')
            ->select('id_mapel', 'nama_mapel', 'jam_per_minggu')
            ->get()
            ->keyBy('id_mapel');

        // Aktual jam mapel per kelas
        $aktualMapel = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->select('k.nama_kelas', 'm.nama_mapel', DB::raw('COUNT(*) as aktual_jam'))
            ->groupBy('k.nama_kelas', 'm.nama_mapel')
            ->get();

        $hasil = [];
        foreach ($aktualMapel as $item) {
            $target = $targetMapel[$item->nama_mapel] ?? null;
            $targetJam = $target ? $target->jam_per_minggu : 0;
            $selisih = $item->aktual_jam - $targetJam;

            if (!isset($hasil[$item->nama_kelas])) {
                $hasil[$item->nama_kelas] = [];
            }

            $hasil[$item->nama_kelas][] = [
                'mapel' => $item->nama_mapel,
                'target' => $targetJam,
                'aktual' => $item->aktual_jam,
                'selisih' => $selisih,
                'status' => $selisih == 0 ? 'Sesuai' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan')
            ];
        }

        return $hasil;
    }

    /**
     * Hitung rata-rata pemenuhan jam mapel
     */
    private function hitungRataRataPemenuhan($pemenuhanMapel)
    {
        $totalSesuai = 0;
        $totalMapel = 0;

        foreach ($pemenuhanMapel as $kelas => $mapels) {
            foreach ($mapels as $mapel) {
                $totalMapel++;
                if ($mapel['status'] == 'Sesuai') {
                    $totalSesuai++;
                }
            }
        }

        return $totalMapel > 0 ? round(($totalSesuai / $totalMapel) * 100, 1) : 0;
    }

    /**
     * Dapatkan nama hari
     */
    private function getNamaHari($number)
    {
        $hari = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return $hari[$number] ?? '';
    }

    /**
     * Ambil jadwal untuk hari ini
     */
    private function getJadwalHariIni($jadwalAktif, $hari)
    {
        if (!$jadwalAktif) return [];

        // Ambil semua waktu untuk hari ini (termasuk yang ada keterangan)
        $semuaWaktu = DB::table('waktu')
            ->where('hari', $hari)
            ->orderBy('id_waktu')
            ->get();

        // Ambil semua kelas
        $kelasList = DB::table('kelas')->pluck('nama_kelas');

        // Ambil jadwal untuk hari ini
        $jadwal = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')  // TAMBAHKAN JOIN INI
            ->where('j.id_master', $jadwalAktif->id_master)
            ->where('w.hari', $hari)
            ->select(
                'k.nama_kelas',
                'w.jam_ke',
                'w.id_waktu',
                'm.nama_mapel',
                'g.nama_guru'
            )
            ->get();

        // Buat mapping jadwal per id_waktu dan kelas
        $jadwalMap = [];
        foreach ($jadwal as $item) {
            $key = $item->id_waktu . '_' . $item->nama_kelas;
            $jadwalMap[$key] = $item;
        }

        $result = [];
        foreach ($semuaWaktu as $waktu) {
            foreach ($kelasList as $kelas) {
                $key = $waktu->id_waktu . '_' . $kelas;

                if (isset($jadwalMap[$key])) {
                    $item = $jadwalMap[$key];
                    $result[] = (object)[
                        'jam_ke' => $waktu->jam_ke,
                        'id_waktu' => $waktu->id_waktu,
                        'nama_kelas' => $kelas,
                        'nama_mapel' => $item->nama_mapel,
                        'nama_guru' => $item->nama_guru,
                        'keterangan' => '',
                        'is_keterangan' => false,
                        'waktu_mulai' => $waktu->waktu_mulai,
                        'waktu_selesai' => $waktu->waktu_selesai
                    ];
                } elseif (!empty($waktu->keterangan)) {
                    $result[] = (object)[
                        'jam_ke' => $waktu->jam_ke,
                        'id_waktu' => $waktu->id_waktu,
                        'nama_kelas' => $kelas,
                        'nama_mapel' => '',
                        'nama_guru' => '',
                        'keterangan' => $waktu->keterangan,
                        'is_keterangan' => true,
                        'waktu_mulai' => $waktu->waktu_mulai,
                        'waktu_selesai' => $waktu->waktu_selesai
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Hitung distribusi jam per hari
     */
    private function hitungDistribusiJam($jadwalAktif)
    {
        if (!$jadwalAktif) return [];

        $distribusi = DB::table('jadwal as j')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $jadwalAktif->id_master)
            ->select('w.hari', DB::raw('COUNT(*) as total_jam'))
            ->groupBy('w.hari')
            ->get();

        $result = [];
        foreach ($distribusi as $item) {
            $result[$item->hari] = $item->total_jam;
        }

        return $result;
    }

    /**
     * Hitung statistik cepat
     */
    private function hitungStatCepat($pemenuhanMapel, $bebanGuru)
    {
        $totalKelas = count($pemenuhanMapel);
        $kelasSesuai = 0;

        foreach ($pemenuhanMapel as $kelas => $mapels) {
            $semuaSesuai = true;
            foreach ($mapels as $mapel) {
                if ($mapel['status'] != 'Sesuai') {
                    $semuaSesuai = false;
                    break;
                }
            }
            if ($semuaSesuai) $kelasSesuai++;
        }

        $totalGuru = count($bebanGuru);
        $guruOverload = 0;
        $guruUnderload = 0;
        $guruIdeal = 0;

        foreach ($bebanGuru as $guru) {
            if ($guru['status'] == 'Overload') $guruOverload++;
            elseif ($guru['status'] == 'Underload') $guruUnderload++;
            else $guruIdeal++;
        }

        return [
            'persen_pemenuhan_kelas' => $totalKelas > 0 ? round(($kelasSesuai / $totalKelas) * 100, 1) : 0,
            'guru_overload' => $guruOverload,
            'guru_underload' => $guruUnderload,
            'guru_ideal' => $guruIdeal,
            'total_kelas' => $totalKelas,
            'kelas_sesuai' => $kelasSesuai
        ];
    }

    /**
     * API: Ambil data bentrok untuk refresh real-time (opsional)
     */
    public function getBentrokData()
    {
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();
        $bentrokGuru = $this->hitungBentrokGuru($jadwalAktif);

        return response()->json([
            'success' => true,
            'bentrok' => $bentrokGuru,
            'total' => count($bentrokGuru)
        ]);
    }

    /**
     * API: Ambil data beban guru untuk refresh real-time (opsional)
     */
    public function getBebanGuruData()
    {
        $jadwalAktif = JadwalMaster::where('aktif', 'aktif')->first();
        $bebanGuru = $this->hitungBebanGuru($jadwalAktif);

        return response()->json([
            'success' => true,
            'data' => $bebanGuru
        ]);
    }
}
