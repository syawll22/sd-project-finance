<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function index()
    {
        $rekening = Rekening::all();
        return view('rekening.index', compact('rekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_rekening' => 'required|string|max:255',
            'saldo'         => 'nullable|numeric|min:0',
        ]);

        Rekening::create([
            'nama_rekening' => $request->nama_rekening,
            'saldo'         => $request->saldo ?? 0, // Jika kosong, set ke 0
        ]);

        return redirect()->route('rekening.index')->with('success', 'Rekening berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_rekening' => 'required|string|max:255',
            'saldo'         => 'nullable|numeric|min:0',
        ]);

        $rekening = Rekening::findOrFail($id);
        $rekening->update([
            'nama_rekening' => $request->nama_rekening,
            'saldo'         => $request->saldo ?? 0,
        ]);

        return redirect()->route('rekening.index')->with('success', 'Rekening berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $rekening = Rekening::findOrFail($id);
        $rekening->delete();

        return redirect()->route('rekening.index')->with('success', 'Rekening berhasil dihapus!');
    }
}