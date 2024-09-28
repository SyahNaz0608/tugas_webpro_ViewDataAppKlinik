<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pasien = \App\Models\Pasien::latest()->paginate(10);
        $data['pasien'] = $pasien;
        return view('pasien_index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pasien_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'no_pasien'     => 'required|unique:pasiens,no_pasien',
            'nama'          => 'required',
            'umur'          => 'required|numeric',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'alamat'        => 'nullable',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:5000'
        ]);
        // $pasien = new \App\Models\Pasien(); // membuat objek kosong
        $pasien = new Pasien(); // membuat objek kosong dengan cara import class Pasien
        $pasien->no_pasien     = $requestData['no_pasien'];
        $pasien->nama          = $requestData['nama'];
        $pasien->umur          = $requestData['umur'];
        $pasien->jenis_kelamin = $requestData['jenis_kelamin'];
        $pasien->alamat        = $requestData['alamat'];
        $pasien->save();
        if ($request->hasFile('foto')) {
            $request->file('foto')->move('storage/images/', $request->file('foto')->getClientOriginalName());
            $pasien->foto = $request->file('foto')->getClientOriginalName();
            $pasien->save();
        }
        return redirect('/pasien')->with('pesan', 'Data Sudah Disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['pasien'] = \App\Models\Pasien::findOrFail($id);
        return view('pasien_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $requestData = $request->validate([
            'nama'          => 'required|min:3',
            'no_pasien'     => 'required|unique:pasiens,no_pasien,' . $id,
            'umur'          => 'required',
            'alamat'        => 'nullable',
            'jenis_kelamin' => 'required',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:10000'
        ]);
        $pasien = \App\Models\Pasien::findOrFail($id);
        $pasien->fill($requestData);

        if ($request->hasFile('foto')) {

            if ($pasien->foto && file_exists(public_path('storage/images/' . $pasien->foto))) {
                unlink(public_path('storage/images/' . $pasien->foto));
            }

            $fileName = time() . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('storage/images'), $fileName);
            $pasien->foto = $fileName;
        }

        $pasien->save();
        return redirect('/pasien')->with('pesan', 'Data sudah disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pasien = \App\Models\Pasien::findOrFail($id);
        $pasien->delete();
        return back()->with('pesan', 'Data Sudah Dihapus!');
    }
}
