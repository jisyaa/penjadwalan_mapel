<?php

namespace App\Http\Controllers\Admin;

use App\Models\Guru;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GuruController extends Controller
{
    public function index()
    {
        $data_guru = Guru::all();

        return view('admin.read.guru', compact('data_guru'));

    }

    public function create()
    {
        return view('admin.create.guru');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_guru' => 'required',
            'nip' => 'required|unique:guru,nip',
            'jam_mingguan' => 'required',
            'mapel' => 'required'
        ], [
            'nip.unique' => 'Guru sudah terdaftar.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = [
            'nama_guru' => $request->nama_guru,
            'nip' => $request->nip,
            'jam_mingguan' => $request->jam_mingguan,
            'mapel' => $request->mapel,
        ];

        Guru::create($data);

        return redirect()->route('guru');
    }

    public function edit(Request $request, string $id)
    {

        $data_guru = Guru::where('id_guru', $id)->first();

        return view('admin.update.guru', compact('data_guru'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_guru' => 'required',
            'nip' => 'required|unique:guru,nip,' . $id . ',id_guru',
            'jam_mingguan' => 'required',
            'mapel' => 'required'
        ], [
            'nip.unique' => 'Guru sudah terdaftar.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $guru = Guru::where('id_guru', $id)->first();
        if ($guru) {
            $data = [
                'nama_guru' => $request->nama_guru,
                'nip' => $request->nip,
                'jam_mingguan' => $request->jam_mingguan,
                'mapel' => $request->mapel,
            ];

            $guru->update($data);
        }
        return redirect()->route('guru');
    }

    public function delete(Request $request, string $id)
    {
        $data_guru = Guru::where('id_guru', $id)->first();

        if ($data_guru) {
            $data_guru->delete();
        }
        return redirect()->route('guru');
    }

    // public function export(){
    //     return Excel::download(new dosenExport, "Dosen.xlsx");
    // }

    // public function import(Request $request){
    //     try {
    //         Excel::import(new dosenImport, $request->file('file'));
    //         return redirect('dosen')->with('success', 'Data berhasil diimpor.');
    //     } catch (ValidationException $e) {
    //         $errorMessages = $e->errors()['duplicate_data'] ?? [];
    //         return redirect()->back()->withErrors(['error' => $errorMessages]);
    //     } catch (\Exception $e) {
    //         Log::error('General Exception: ' . $e->getMessage());
    //         Log::error('Trace: ' . $e->getTraceAsString());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat import data.');
    //     }
    // }
}
