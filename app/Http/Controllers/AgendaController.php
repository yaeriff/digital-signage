<?php

namespace App\Http\Controllers;
use App\Models\Agenda;

use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agendas = Agenda::orderBy('start_time', 'asc')->get();

        // Kirim data tersebut ke view (halaman HTML)
        return view('agendas.index', compact('agendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('agendas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required',
            'title'      => 'required|max:255',
            'location'   => 'required|max:255',
            'description'=> 'nullable' // Boleh kosong
        ]);

        // 2. Simpan ke Database
        Agenda::create($request->all());

        // 3. Balikin ke halaman daftar dengan pesan sukses
        return redirect()->route('agendas.index')
                         ->with('success', 'Agenda berhasil ditambahkan!');
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
    public function edit(Agenda $agenda)
    {
        return view('agendas.edit', compact('agenda'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agenda $agenda)
    {
        // Validasi input
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required',
            'title'      => 'required|max:255',
            'location'   => 'required|max:255',
            'description'=> 'nullable'
        ]);

        // Update data ke database
        $agenda->update($request->all());

        // Kembali ke daftar dengan pesan sukses
        return redirect()->route('agendas.index')
                         ->with('success', 'Agenda berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agenda $agenda)
    {
        $agenda->delete();

        return redirect()->route('agendas.index')
                         ->with('success', 'Agenda berhasil dihapus!');
    }
}
