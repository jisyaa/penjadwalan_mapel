<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Ruang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    public function index()
    {
        $data_kelas = Kelas::with(['r_guru', 'r_ruang'])->get();

        return view('admin.read.kelas', compact('data_kelas'));

    }

    public function create()
    {
        $data_guru = Guru::all();
        $data_ruang = Ruang::all();

        return view('admin.create.kelas', compact('data_guru', 'data_ruang'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required',
            'tingkat' => 'required',
            'jumlah_siswa' => 'required',
            'wali_kelas' => 'required',
            'id_ruang' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = [
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'jumlah_siswa' => $request->jumlah_siswa,
            'wali_kelas' => $request->wali_kelas,
            'id_ruang' => $request->id_ruang,
        ];

        Kelas::create($data);

        return redirect()->route('kelas');
    }

    public function edit(Request $request, string $id)
    {

        $data_kelas = Kelas::where('id_kelas', $id)->first();
        $data_guru = Guru::all();
        $data_ruang = Ruang::all();

        return view('admin.update.kelas', compact('data_kelas', 'data_guru', 'data_ruang'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required',
            'tingkat' => 'required',
            'jumlah_siswa' => 'required',
            'wali_kelas' => 'required',
            'id_ruang' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $kelas = Kelas::where('id_kelas', $id)->first();
        if ($kelas) {
            $data = [
                'nama_kelas' => $request->nama_kelas,
                'tingkat' => $request->tingkat,
                'jumlah_siswa' => $request->jumlah_siswa,
                'wali_kelas' => $request->wali_kelas,
                'id_ruang' => $request->id_ruang,
            ];

            $kelas->update($data);
        }
        return redirect()->route('kelas');
    }

    public function delete(Request $request, string $id)
    {
        $data_kelas = Kelas::where('id_kelas', $id)->first();

        if ($data_kelas) {
            $data_kelas->delete();
        }
        return redirect()->route('kelas');
    }
}
