<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\GuruMapel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GuruMapelController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'guru');

        if ($filter == 'kelas') {

            $data = GuruMapel::with(['r_guru', 'r_mapel', 'r_kelas'])
                ->orderBy('id_kelas', 'asc')
                ->get();

            $grouped = $data->groupBy('id_kelas');
        } else {

            $data = GuruMapel::with(['r_guru', 'r_mapel', 'r_kelas'])
                ->join('guru', 'guru.id_guru', '=', 'guru_mapel.id_guru')
                ->orderBy('guru.nama_guru', 'asc')
                ->orderBy('id_kelas', 'asc')
                ->select('guru_mapel.*')
                ->get();

            $grouped = $data->groupBy('id_guru');
        }

        return view('admin.read.guru_mapel', [
            'grouped_data' => $grouped,
            'filter' => $filter
        ]);
    }


    public function create()
    {
        $data_guru = Guru::all();
        $data_mapel = Mapel::all();
        $data_kelas = Kelas::all();

        return view('admin.create.guru_mapel', compact('data_guru', 'data_mapel', 'data_kelas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_guru' => 'required',
            'id_mapel' => 'required',
            'id_kelas' => 'required',
            'aktif' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = [
            'id_guru' => $request->id_guru,
            'id_mapel' => $request->id_mapel,
            'id_kelas' => $request->id_kelas,
            'aktif' => $request->aktif,
        ];

        GuruMapel::create($data);

        return redirect()->route('guru_mapel');
    }

    public function edit(Request $request, string $id)
    {

        $data_guru_mapel = GuruMapel::where('id', $id)->first();
        $data_guru = Guru::all();
        $data_mapel = Mapel::all();
        $data_kelas = Kelas::all();

        return view('admin.update.guru_mapel', compact('data_guru_mapel', 'data_guru', 'data_mapel', 'data_kelas'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'id_guru' => 'required',
            'id_mapel' => 'required',
            'id_kelas' => 'required',
            'aktif' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $guru_mapel = GuruMapel::where('id', $id)->first();
        if ($guru_mapel) {
            $data = [
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_kelas' => $request->id_kelas,
                'aktif' => $request->aktif,
            ];

            $guru_mapel->update($data);
        }
        return redirect()->route('guru_mapel');
    }

    public function delete(Request $request, string $id)
    {
        $data_guru_mapel = GuruMapel::where('id', $id)->first();

        if ($data_guru_mapel) {
            $data_guru_mapel->delete();
        }
        return redirect()->route('guru_mapel');
    }
}
