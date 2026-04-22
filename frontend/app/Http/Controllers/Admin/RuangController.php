<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ruang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RuangController extends Controller
{
    public function index()
    {
        $data_ruang = Ruang::all();

        return view('admin.read.ruang', compact('data_ruang'));

    }

    public function create()
    {
        return view('admin.create.ruang');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_ruang' => 'required',
            'tipe' => 'required',
            'kapasitas' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = [
            'nama_ruang' => $request->nama_ruang,
            'tipe' => $request->tipe,
            'kapasitas' => $request->kapasitas,
        ];

        Ruang::create($data);

        return redirect()->route('ruang');
    }

    public function edit(Request $request, string $id)
    {

        $data_ruang = Ruang::where('id_ruang', $id)->first();

        return view('admin.update.ruang', compact('data_ruang'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_ruang' => 'required',
            'tipe' => 'required',
            'kapasitas' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $ruang = Ruang::where('id_ruang', $id)->first();
        if ($ruang) {
            $data = [
                'nama_ruang' => $request->nama_ruang,
                'tipe' => $request->tipe,
                'kapasitas' => $request->kapasitas
            ];

            $ruang->update($data);
        }
        return redirect()->route('ruang');
    }

    public function delete(Request $request, string $id)
    {
        $data_ruang = Ruang::where('id_ruang', $id)->first();

        if ($data_ruang) {
            $data_ruang->delete();
        }
        return redirect()->route('ruang');
    }
}
