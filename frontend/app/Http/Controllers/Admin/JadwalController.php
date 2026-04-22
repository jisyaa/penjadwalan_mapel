<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Waktu; // Import model Waktu

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = session('jadwal_generate');
        $fitness = session('fitness_best');
        $fitness_history = session('fitness_history');
        $generasi = session('generasi');

        // Ambil SEMUA data waktu dari database - URUTKAN BERDASARKAN ID
        $semuaWaktu = Waktu::orderBy('id_waktu', 'asc')->get();  // <-- URUTKAN ID

        // Ambil daftar id_waktu yang dikirim API
        $availableIds = [];
        if ($jadwal && !empty($jadwal)) {
            $availableIds = collect($jadwal)
                ->where('is_keterangan', '!=', true)
                ->pluck('id_waktu')
                ->unique()
                ->toArray();
        }

        return view('admin.read.generate', compact(
            'jadwal',
            'fitness',
            'fitness_history',
            'generasi',
            'semuaWaktu',
            'availableIds'
        ));
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
            session(['target_mapel' => $data['target_mapel']]);
            session(['target_beban_guru' => $data['target_beban_guru']]);

            return redirect()->route('generate-jadwal')->with('success', 'Jadwal berhasil digenerate! Fitness: ' . $data['fitness_best']);
        } catch (\Exception $e) {
            return redirect()->route('generate-jadwal')->with('error', 'Gagal terhubung ke API Python: ' . $e->getMessage());
        }
    }

    /**
     * Merge waktu khusus dari database (termasuk yang jam_ke = NULL)
     */
    // Di JadwalController.php

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

    // Endpoint untuk cek status API Python
    public function cekStatus()
    {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:8001/status');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Di controller (JadwalController.php)
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

    // Di JadwalController.php
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

            // Cari dan update jadwal
            $updated = false;
            foreach ($jadwal as &$item) {
                if (
                    $item['kelas'] == $kelas &&
                    $item['hari'] == $hari &&
                    $item['jam'] == $jam
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
                        }
                    } else {
                        $item['guru'] = '';
                        $item['mapel'] = '';
                    }

                    $updated = true;
                    break;
                }
            }

            if ($updated) {
                // Simpan kembali ke session
                session(['jadwal_generate' => $jadwal]);

                return response()->json([
                    'success' => true,
                    'updated_jadwal' => true
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data jadwal tidak ditemukan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
