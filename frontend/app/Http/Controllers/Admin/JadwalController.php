<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Waktu;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = session('jadwal_generate');
        $fitness = session('fitness_best');
        $fitness_history = session('fitness_history');
        $generasi = session('generasi');

        $semuaWaktu = Waktu::orderBy('id_waktu', 'asc')->get();

        $target_mapel = $this->hitungTargetJamMapel();
        $target_beban_guru = $this->hitungTargetBebanGuru();

        $getWarnaByKeterangan = function($teks) {
            return $this->getWarnaByKeterangan($teks);
        };

        $availableIds = [];
        if ($jadwal && !empty($jadwal)) {
            $availableIds = collect($jadwal)
                ->where('is_keterangan', '!=', true)
                ->pluck('id_waktu')
                ->unique()
                ->toArray();
        }

        return view('admin.generate-jadwal.index', compact(
            'jadwal',
            'fitness',
            'fitness_history',
            'target_mapel',
            'target_beban_guru',
            'generasi',
            'semuaWaktu',
            'availableIds',
            'getWarnaByKeterangan'
        ));
    }

    private function getWarnaByKeterangan($teks)
    {
        $teksLower = strtolower($teks);
        if (strpos($teksLower, 'istirahat') !== false || strpos($teksLower, 'ishoma') !== false) {
            return 'kuning-cerah';
        }
        return 'biru-cerah';
    }

    private function hitungTargetBebanGuru()
    {
        $targetBebanGuru = [];

        $guruMapels = DB::table('guru_mapel')
            ->join('mapel', 'guru_mapel.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru', 'guru_mapel.id_guru', '=', 'guru.id_guru')
            ->where('guru_mapel.aktif', 'aktif')
            ->select(
                'guru.nama_guru',
                'mapel.nama_mapel',
                'mapel.jam_per_minggu',
                'guru_mapel.id_guru'
            )
            ->get();

        foreach ($guruMapels as $gm) {
            $namaGuru = $gm->nama_guru;
            if (!isset($targetBebanGuru[$namaGuru])) {
                $targetBebanGuru[$namaGuru] = 0;
            }
            $targetBebanGuru[$namaGuru] += $gm->jam_per_minggu;
        }

        return $targetBebanGuru;
    }

    private function hitungTargetJamMapel()
    {
        $targetMapel = [];

        // Ambil semua mapel aktif beserta jam_per_minggunya
        $mapels = DB::table('mapel')
            ->get();

        foreach ($mapels as $mapel) {
            $targetMapel[$mapel->nama_mapel] = $mapel->jam_per_minggu;
        }

        return $targetMapel;
    }

    public function generate()
    {
        set_time_limit(0);

        try {
            session()->forget(['jadwal_generate', 'fitness_best', 'fitness_history', 'generasi', 'target_mapel', 'target_beban_guru']);

            $response = Http::timeout(0)
                ->get('http://127.0.0.1:8001/generate-jadwal', [
                    'populasi_size' => 30,
                    'generasi' => 100
                ]);

            $data = $response->json();

            if ($data['status'] != 'success') {
                return redirect()->route('generate-jadwal')->with('error', $data['message'] ?? 'Gagal generate jadwal');
            }

            // Merge dengan waktu khusus dari database (termasuk yang jam_ke = NULL)
            $jadwalWithWaktuKhusus = $this->mergeWaktuKhususDariDB($data['jadwal']);

            session(['jadwal_generate' => $jadwalWithWaktuKhusus]);
            session(['fitness_best' => $data['fitness_best']]);
            session(['fitness_history' => $data['fitness_history']]);
            session(['generasi' => $data['generasi']]);

            return redirect()->route('generate-jadwal')->with('success', 'Jadwal berhasil digenerate! Fitness: ' . $data['fitness_best']);
        } catch (\Exception $e) {
            return redirect()->route('generate-jadwal')->with('error', 'Gagal terhubung ke API Python: ' . $e->getMessage());
        }
    }

    private function mergeWaktuKhususDariDB($jadwalFromAPI)
    {
        // Ambil semua data waktu dari database
        $semuaWaktu = Waktu::orderBy('id_waktu', 'asc')->get();

        // Ambil id_waktu yang sudah ada di jadwal dari API
        $existingIds = collect($jadwalFromAPI)->pluck('id_waktu')->unique()->toArray();

        $finalJadwal = [];

        // Masukkan jadwal dari API terlebih dahulu
        foreach ($jadwalFromAPI as $j) {
            $finalJadwal[] = $j;
        }

        // Tambahkan waktu khusus yang tidak ada di API
        foreach ($semuaWaktu as $waktu) {
            if (!in_array($waktu->id_waktu, $existingIds)) {
                $exists = false;
                foreach ($finalJadwal as $fj) {
                    if (isset($fj['id_waktu']) && $fj['id_waktu'] == $waktu->id_waktu) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    // PERBAIKAN: Kirim jam_ke dengan benar
                    $finalJadwal[] = [
                        'id_waktu' => $waktu->id_waktu,
                        'hari' => $waktu->hari,
                        'jam' => $waktu->jam_ke,           // Untuk kompatibilitas
                        'jam_ke' => $waktu->jam_ke,        // PENTING: Kirim jam_ke asli
                        'kelas' => null,
                        'guru' => null,
                        'mapel' => null,
                        'ruangan' => null,
                        'id_guru_mapel' => null,
                        'is_keterangan' => true,
                        'keterangan' => !empty($waktu->keterangan) ? $waktu->keterangan : 'Kegiatan Khusus',
                        'dari_database' => true,
                        'has_null_jam' => is_null($waktu->jam_ke),
                    ];
                }
            }
        }

        // Urutkan berdasarkan id_waktu
        usort($finalJadwal, function ($a, $b) {
            return ($a['id_waktu'] ?? 999) - ($b['id_waktu'] ?? 999);
        });

        // Debug: cek data untuk id_waktu 1 (Upacara)
        Log::info('Data jadwal setelah merge:', ['jadwal' => $finalJadwal]);

        return $finalJadwal;
    }

    public function simpan(Request $request)
    {
        $jadwal = session('jadwal_generate');

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal belum digenerate');
        }

        // Filter hanya jadwal yang bukan keterangan (hanya simpan yang punya id_guru_mapel)
        $jadwalToSave = array_filter($jadwal, function ($j) {
            return isset($j['id_guru_mapel']) && !is_null($j['id_guru_mapel']);
        });

        DB::beginTransaction();

        try {
            // nonaktifkan jadwal lama
            DB::table('jadwal_master')->update([
                'aktif' => 'tidak'
            ]);

            // insert master
            $id_master = DB::table('jadwal_master')->insertGetId([
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => $request->semester,
                'keterangan' => $request->keterangan,
                'tanggal_generate' => now(),
                'aktif' => 'aktif'
            ]);

            // insert detail jadwal
            foreach ($jadwalToSave as $j) {
                DB::table('jadwal')->insert([
                    'id_master' => $id_master,
                    'id_guru_mapel' => $j['id_guru_mapel'],
                    'id_waktu' => $j['id_waktu']
                ]);
            }

            DB::commit();

            session()->forget('jadwal_generate');
            session()->forget('fitness_best');
            session()->forget('fitness_history');
            session()->forget('generasi');

            return redirect()->back()->with('success', 'Jadwal berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getGuruMapelOptions()
    {
        try {
            $guruMapel = DB::table('guru_mapel as gm')
                ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
                ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
                ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
                ->where('gm.aktif', 'aktif')
                ->select(
                    'gm.id_guru_mapel',
                    'g.nama_guru',
                    'm.nama_mapel',
                    'k.nama_kelas'
                )
                ->orderBy('k.nama_kelas')
                ->orderBy('g.nama_guru')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $guruMapel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateCell(Request $request)
    {
        try {
            $kelas = $request->input('kelas');
            $hari = $request->input('hari');
            $jam = $request->input('jam');
            $idWaktu = $request->input('id_waktu');
            $newGuruMapelId = $request->input('id_guru_mapel');
            $oldGuruMapelId = $request->input('old_id_guru_mapel');

            // Ambil jadwal dari session
            $jadwal = session('jadwal_generate');

            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan di session'
                ]);
            }

            Log::info('Update Cell Request:', [
                'kelas' => $kelas,
                'hari' => $hari,
                'jam' => $jam,
                'id_waktu' => $idWaktu,
                'new_id' => $newGuruMapelId,
                'old_id' => $oldGuruMapelId
            ]);

            // Cari dan update jadwal
            $updated = false;
            foreach ($jadwal as &$item) {
                // Cek berdasarkan kelas, hari, dan jam (atau id_waktu)
                $matchJam = ($item['jam'] == $jam) || ($item['id_waktu'] == $idWaktu);

                if (
                    $item['kelas'] == $kelas &&
                    $item['hari'] == $hari &&
                    $matchJam
                ) {

                    $item['id_guru_mapel'] = $newGuruMapelId;

                    // Update guru dan mapel berdasarkan id_guru_mapel
                    if ($newGuruMapelId) {
                        $guruMapel = DB::table('guru_mapel as gm')
                            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
                            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
                            ->where('gm.id_guru_mapel', $newGuruMapelId)
                            ->first();

                        if ($guruMapel) {
                            $item['guru'] = $guruMapel->nama_guru;
                            $item['mapel'] = $guruMapel->nama_mapel;
                        } else {
                            $item['guru'] = '';
                            $item['mapel'] = '';
                        }
                    } else {
                        $item['guru'] = '';
                        $item['mapel'] = '';
                    }

                    $updated = true;
                    Log::info('Item updated:', $item);
                    break;
                }
            }

            if ($updated) {
                // Simpan kembali ke session
                session(['jadwal_generate' => $jadwal]);

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil diupdate'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data jadwal tidak ditemukan. Kelas: ' . $kelas . ', Hari: ' . $hari . ', Jam: ' . $jam
            ]);
        } catch (\Exception $e) {
            Log::error('Error update cell: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
