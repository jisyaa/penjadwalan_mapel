<?php

namespace App\Http\Controllers\Admin;

use App\Models\Mapel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MapelController extends Controller
{
    public function index()
    {
        $data_mapel = Mapel::all();

        return view('admin.read.mapel', compact('data_mapel'));

    }

    public function create()
    {
        return view('admin.create.mapel');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_mapel' => 'required',
            'jam_per_minggu' => 'required',
            'kategori' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = [
            'nama_mapel' => $request->nama_mapel,
            'jam_per_minggu' => $request->jam_per_minggu,
            'kategori' => $request->kategori,
        ];

        Mapel::create($data);

        return redirect()->route('mapel');
    }

    public function edit(Request $request, string $id)
    {

        $data_mapel = Mapel::where('id_mapel', $id)->first();

        return view('admin.update.mapel', compact('data_mapel'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_mapel' => 'required',
            'jam_per_minggu' => 'required',
            'kategori' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $mapel = Mapel::where('id_mapel', $id)->first();
        if ($mapel) {
            $data = [
                'nama_mapel' => $request->nama_mapel,
                'jam_per_minggu' => $request->jam_per_minggu,
                'kategori' => $request->kategori,
            ];

            $mapel->update($data);
        }
        return redirect()->route('mapel');
    }

    public function delete(Request $request, string $id)
    {
        $data_mapel = Mapel::where('id_mapel', $id)->first();

        if ($data_mapel) {
            $data_mapel->delete();
        }
        return redirect()->route('mapel');
    }
}
