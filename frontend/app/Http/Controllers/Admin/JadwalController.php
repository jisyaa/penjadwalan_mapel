<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = session('jadwal_generate');
        $fitness = session('fitness_best');
        $fitness_history = session('fitness_history');
        $generasi = session('generasi');

        return view('admin.read.generate', compact('jadwal', 'fitness', 'fitness_history', 'generasi'));
    }

    public function generate()
    {
        set_time_limit(0);

        try {
            // Hapus session lama
            session()->forget(['jadwal_generate', 'fitness_best', 'fitness_history', 'generasi', 'target_mapel', 'target_beban_guru']);

            $response = Http::timeout(0)
                ->get('http://127.0.0.1:8001/generate-jadwal', [
                    'populasi_size' => 300,
                    'generasi' => 1000
                ]);

            $data = $response->json();

            if ($data['status'] != 'success') {
                return redirect()->route('generate-jadwal')->with('error', $data['message'] ?? 'Gagal generate jadwal');
            }

            // Simpan ke session
            session(['jadwal_generate' => $data['jadwal']]);
            session(['fitness_best' => $data['fitness_best']]);
            session(['fitness_history' => $data['fitness_history']]);
            session(['generasi' => $data['generasi']]);
            session(['target_mapel' => $data['target_mapel']]);  // Tambahkan
            session(['target_beban_guru' => $data['target_beban_guru']]);  // Tambahkan

            return redirect()->route('generate-jadwal')->with('success', 'Jadwal berhasil digenerate! Fitness: ' . $data['fitness_best']);
        } catch (\Exception $e) {
            return redirect()->route('generate-jadwal')->with('error', 'Gagal terhubung ke API Python: ' . $e->getMessage());
        }
    }

    public function simpan(Request $request)
    {
        $jadwal = session('jadwal_generate');

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal belum digenerate');
        }

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
            foreach ($jadwal as $j) {
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
}
