<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function index()
    {
        $rekening = Rekening::latest()->paginate(10);
        return view('rekening.index', compact('rekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_rekening'  => 'required|string|max:255',
            'nomor_rekening' => 'nullable|string|max:100',
            'atas_nama'      => 'nullable|string|max:255',
            'saldo'          => 'required|numeric',
        ]);

        Rekening::create($request->all());

        return redirect()->back()->with('success', 'Rekening berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_rekening'  => 'required|string|max:255',
            'nomor_rekening' => 'nullable|string|max:100',
            'atas_nama'      => 'nullable|string|max:255',
            'saldo'          => 'required|numeric',
        ]);

        $rek = Rekening::findOrFail($id);
        $rek->update($request->all());

        return redirect()->back()->with('success', 'Data rekening berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $rek = Rekening::findOrFail($id);
        $rek->delete();

        return redirect()->back()->with('success', 'Rekening berhasil dihapus!');
    }
}