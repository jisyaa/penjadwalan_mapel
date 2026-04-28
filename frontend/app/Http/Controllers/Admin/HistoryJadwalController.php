<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\JadwalMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistoryJadwalController extends Controller
{
    public function index()
    {
        $jadwalMasters = JadwalMaster::orderBy('tanggal_generate', 'desc')->get();
        return view('admin.history.index', compact('jadwalMasters'));
    }

    public function show($id)
    {
        $master = JadwalMaster::findOrFail($id);
        $jadwalDetails = Jadwal::where('id_master', $id)->get();

        // Konversi ke format yang sama dengan generate jadwal
        $jadwal = $this->konversiKeFormatJadwal($master);

        $target_mapel = $this->hitungTargetJamMapel();
        $target_beban_guru = $this->hitungTargetBebanGuru();

        $isAktif = ($master->aktif == 'aktif');

        $getWarnaByKeterangan = function ($teks) {
            return $this->getWarnaByKeterangan($teks);
        };

        $masterData = [
            'id_master' => $master->id_master,
            'tahun_ajaran' => $master->tahun_ajaran,
            'semester' => $master->semester,
            'keterangan' => $master->keterangan,
            'aktif' => $master->aktif,
            'tanggal_generate' => $master->tanggal_generate->format('d/m/Y H:i:s')
        ];

        return view('admin.history.detail', compact('masterData', 'master', 'jadwal', 'target_mapel', 'target_beban_guru', 'isAktif', 'getWarnaByKeterangan'));
    }

    private function getWarnaByKeterangan($teks)
    {
        $teksLower = strtolower($teks);
        if (strpos($teksLower, 'istirahat') !== false || strpos($teksLower, 'ishoma') !== false) {
            return 'kuning-cerah';
        }
        return 'biru-cerah';
    }

    private function konversiKeFormatJadwal($master)
    {
        // Query untuk mengambil data jadwal
        $jadwalDetails = DB::table('jadwal as j')
            ->join('guru_mapel as gm', 'j.id_guru_mapel', '=', 'gm.id_guru_mapel')
            ->join('guru as g', 'gm.id_guru', '=', 'g.id_guru')
            ->join('mapel as m', 'gm.id_mapel', '=', 'm.id_mapel')
            ->join('kelas as k', 'gm.id_kelas', '=', 'k.id_kelas')
            ->join('waktu as w', 'j.id_waktu', '=', 'w.id_waktu')
            ->where('j.id_master', $master->id_master)
            ->select(
                'j.id_jadwal',
                'j.id_guru_mapel',
                'j.id_waktu',
                'k.id_kelas',
                'k.nama_kelas',
                'g.nama_guru',
                'm.nama_mapel',
                'w.hari',
                'w.jam_ke',
                'w.waktu_mulai',
                'w.waktu_selesai',
                'w.keterangan'
            )
            ->get();

        $jadwalMap = [];

        foreach ($jadwalDetails as $detail) {
            $key = $detail->id_waktu . '_' . $detail->id_kelas;

            $jadwalMap[$key] = [
                'id_jadwal' => $detail->id_jadwal,
                'id_guru_mapel' => $detail->id_guru_mapel,
                'id_waktu' => $detail->id_waktu,
                'id_kelas' => $detail->id_kelas,
                'kelas' => $detail->nama_kelas,
                'guru' => $detail->nama_guru,
                'mapel' => $detail->nama_mapel,
                'hari' => $detail->hari,
                'jam' => $detail->jam_ke,
                'jam_ke' => $detail->jam_ke,
                'waktu_mulai' => $detail->waktu_mulai,
                'waktu_selesai' => $detail->waktu_selesai,
                'keterangan' => '',
                'is_keterangan' => false
            ];
        }

        // Ambil semua waktu yang memiliki keterangan dari database
        $waktuKeterangan = DB::table('waktu')
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->get();

        // Ambil semua kelas unik dari jadwal yang ada
        $kelasList = collect($jadwalMap)
            ->pluck('kelas')
            ->unique()
            ->filter()
            ->values();

        if ($kelasList->isEmpty()) {
            $kelasList = collect(['IX A', 'IX B', 'IX C', 'IX D', 'IX E', 'IX F', 'IX G', 'VIII A', 'VIII B']);
        }

        $existingKeys = [];
        foreach ($jadwalMap as $key => $item) {
            $existingKeys[$key] = true;
        }

        // Tambahkan slot keterangan untuk setiap kelas
        foreach ($waktuKeterangan as $wkt) {
            foreach ($kelasList as $kelas) {
                $key = $wkt->id_waktu . '_' . $kelas;

                if (!isset($existingKeys[$key])) {
                    $jadwalMap[$key] = [
                        'id_jadwal' => null,
                        'id_guru_mapel' => null,
                        'id_waktu' => $wkt->id_waktu,
                        'id_kelas' => null,
                        'kelas' => $kelas,
                        'guru' => '',
                        'mapel' => '',
                        'hari' => $wkt->hari,
                        'jam' => null,
                        'jam_ke' => $wkt->jam_ke,
                        'waktu_mulai' => $wkt->waktu_mulai,
                        'waktu_selesai' => $wkt->waktu_selesai,
                        'keterangan' => $wkt->keterangan,
                        'is_keterangan' => true
                    ];
                }
            }
        }

        $jadwal = array_values($jadwalMap);

        $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        usort($jadwal, function ($a, $b) use ($urutanHari) {
            $hariA = array_search($a['hari'], $urutanHari);
            $hariB = array_search($b['hari'], $urutanHari);
            if ($hariA != $hariB) return $hariA - $hariB;

            $jamA = $a['jam_ke'] ?? 999;
            $jamB = $b['jam_ke'] ?? 999;
            return $jamA - $jamB;
        });

        return $jadwal;
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

        // Kelompokkan per guru dan jumlahkan jam_per_minggu
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

    public function destroy($id)
    {
        // hapus semua jadwal dulu
        Jadwal::where('id_master', $id)->delete();

        // baru hapus master
        JadwalMaster::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function updateMaster(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $master = JadwalMaster::findOrFail($id);

            $request->validate([
                'tahun_ajaran' => 'required|string|max:20',
                'semester' => 'required|in:ganjil,genap',
                'aktif' => 'required|in:aktif,tidak',
                'keterangan' => 'nullable|string'
            ]);

            // Jika mengaktifkan, nonaktifkan yang lain
            if ($request->aktif == 'aktif' && $master->aktif != 'aktif') {
                JadwalMaster::where('aktif', 'aktif')->update(['aktif' => 'tidak']);
            }

            $master->update([
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => $request->semester,
                'aktif' => $request->aktif,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data master berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error update master: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateCell(Request $request, $id)
    {
        try {
            $master = JadwalMaster::findOrFail($id);

            if ($master->aktif != 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak aktif, tidak dapat diubah'
                ]);
            }

            $kelas = $request->input('kelas');
            $hari = $request->input('hari');
            $jam = $request->input('jam');
            $newGuruMapelId = $request->input('id_guru_mapel');
            $oldGuruMapelId = $request->input('old_id_guru_mapel');

            // Cari id_waktu
            $waktu = DB::table('waktu')
                ->where('hari', $hari)
                ->where('jam_ke', $jam)
                ->first();

            if (!$waktu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu tidak ditemukan'
                ]);
            }

            // UPDATE berdasarkan old_id_guru_mapel (jika ada)
            if ($oldGuruMapelId) {
                // Cari data lama
                $existingJadwal = Jadwal::where('id_master', $id)
                    ->where('id_waktu', $waktu->id_waktu)
                    ->where('id_guru_mapel', $oldGuruMapelId)
                    ->first();

                if ($existingJadwal) {
                    if ($newGuruMapelId) {
                        $existingJadwal->id_guru_mapel = $newGuruMapelId;
                        $existingJadwal->save();
                    } else {
                        $existingJadwal->delete();
                    }
                } elseif ($newGuruMapelId) {
                    // Data lama tidak ditemukan, buat baru
                    Jadwal::create([
                        'id_master' => $id,
                        'id_waktu' => $waktu->id_waktu,
                        'id_guru_mapel' => $newGuruMapelId
                    ]);
                }
            } else {
                // Tidak ada old_id, buat baru
                if ($newGuruMapelId) {
                    // Cek apakah sudah ada
                    $exists = Jadwal::where('id_master', $id)
                        ->where('id_waktu', $waktu->id_waktu)
                        ->where('id_guru_mapel', $newGuruMapelId)
                        ->exists();

                    if (!$exists) {
                        Jadwal::create([
                            'id_master' => $id,
                            'id_waktu' => $waktu->id_waktu,
                            'id_guru_mapel' => $newGuruMapelId
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error update cell: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveChanges(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $master = JadwalMaster::findOrFail($id);

            if ($master->aktif != 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak aktif, tidak dapat diubah'
                ]);
            }

            $changes = $request->input('changes', []);
            $deleted = $request->input('deleted', []);

            Log::info('SaveChanges - Master ID: ' . $id);
            Log::info('Changes: ', $changes);
            Log::info('Deleted: ', $deleted);

            // Proses delete
            foreach ($deleted as $deleteItem) {
                if (isset($deleteItem['id_waktu']) && isset($deleteItem['kelas'])) {
                    $kelasData = DB::table('kelas')->where('nama_kelas', $deleteItem['kelas'])->first();

                    if ($kelasData) {
                        if (isset($deleteItem['old_id_guru_mapel']) && !empty($deleteItem['old_id_guru_mapel'])) {
                            Jadwal::where('id_master', $id)
                                ->where('id_waktu', $deleteItem['id_waktu'])
                                ->where('id_guru_mapel', $deleteItem['old_id_guru_mapel'])
                                ->delete();
                        } else {
                            $guruMapelIds = DB::table('guru_mapel')
                                ->where('id_kelas', $kelasData->id_kelas)
                                ->pluck('id_guru_mapel')
                                ->toArray();

                            if (!empty($guruMapelIds)) {
                                Jadwal::where('id_master', $id)
                                    ->where('id_waktu', $deleteItem['id_waktu'])
                                    ->whereIn('id_guru_mapel', $guruMapelIds)
                                    ->delete();
                            }
                        }
                    }
                }
            }

            // Proses update/insert
            foreach ($changes as $change) {
                if (isset($change['id_waktu']) && isset($change['kelas']) && !empty($change['id_guru_mapel'])) {
                    $kelasData = DB::table('kelas')->where('nama_kelas', $change['kelas'])->first();

                    if ($kelasData) {
                        // Hapus data lama (jika ada old_id)
                        if (isset($change['old_id_guru_mapel']) && !empty($change['old_id_guru_mapel'])) {
                            Jadwal::where('id_master', $id)
                                ->where('id_waktu', $change['id_waktu'])
                                ->where('id_guru_mapel', $change['old_id_guru_mapel'])
                                ->delete();
                        } else {
                            $oldGuruMapelIds = DB::table('guru_mapel')
                                ->where('id_kelas', $kelasData->id_kelas)
                                ->pluck('id_guru_mapel')
                                ->toArray();

                            if (!empty($oldGuruMapelIds)) {
                                Jadwal::where('id_master', $id)
                                    ->where('id_waktu', $change['id_waktu'])
                                    ->whereIn('id_guru_mapel', $oldGuruMapelIds)
                                    ->delete();
                            }
                        }

                        // Cek apakah sudah ada
                        $exists = Jadwal::where('id_master', $id)
                            ->where('id_waktu', $change['id_waktu'])
                            ->where('id_guru_mapel', $change['id_guru_mapel'])
                            ->exists();

                        if (!$exists) {
                            Jadwal::create([
                                'id_master' => $id,
                                'id_waktu' => $change['id_waktu'],
                                'id_guru_mapel' => $change['id_guru_mapel']
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perubahan berhasil disimpan',
                'reload' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saveChanges: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
