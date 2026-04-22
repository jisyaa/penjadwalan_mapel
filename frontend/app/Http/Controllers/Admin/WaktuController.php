<?php

namespace App\Http\Controllers\Admin;

use App\Models\Waktu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WaktuController extends Controller
{
    public function index()
    {
        $data_waktu = Waktu::all();

        return view('admin.read.waktu', compact('data_waktu'));

    }

    public function create()
    {
        return view('admin.create.waktu');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required',
            'jam_ke' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'keterangan' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = [
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keterangan' => $request->keterangan,
        ];

        Waktu::create($data);

        return redirect()->route('waktu');
    }

    public function edit(Request $request, string $id)
    {

        $data_waktu = Waktu::where('id_waktu', $id)->first();

        return view('admin.update.waktu', compact('data_waktu'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required',
            'jam_ke' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'keterangan' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $waktu = Waktu::where('id_waktu', $id)->first();
        if ($waktu) {
            $data = [
                'hari' => $request->hari,
                'jam_ke' => $request->jam_ke,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'keterangan' => $request->keterangan
            ];

            $waktu->update($data);
        }
        return redirect()->route('waktu');
    }

    public function delete(Request $request, string $id)
    {
        $data_waktu = Waktu::where('id_waktu', $id)->first();

        if ($data_waktu) {
            $data_waktu->delete();
        }
        return redirect()->route('waktu');
    }
}
