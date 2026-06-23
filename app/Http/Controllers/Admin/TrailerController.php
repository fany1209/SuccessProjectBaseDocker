<?php
/*
Controlador: TrailerController
13/08/25
stefany
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trailer;
use App\Models\TransportLine;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
public function index(Request $request)
{
    $transport_lines = TransportLine::all();
    $trailers = Trailer::with('transportLine')
        ->when($request->search, fn($q) => $q->where('plate', 'like', "%{$request->search}%"))
        ->paginate(10);

    $open = false;
    $create = false;
    $trailer = null;

    return view('admin.trailers', compact('transport_lines', 'trailers', 'open', 'create', 'trailer'));
}


    public function create()
    {
        $transport_lines = TransportLine::all();
        $trailers = Trailer::with('transportLine')->paginate(10);
        $open = true;
        $create = true;
        $trailer = null;

        return view('admin.trailers', compact('transport_lines', 'trailers', 'open', 'create', 'trailer'));
    }


    public function edit($id)
{
    $transport_lines = TransportLine::all();
    $trailers = Trailer::with('transportLine')->paginate(10); // También traemos para la tabla
    $trailer = Trailer::findOrFail($id);
    $open = true;
    $create = false;

    return view('admin.trailers', compact('transport_lines', 'trailers', 'open', 'create', 'trailer'));
}
    public function store(Request $request)
    {
        $request->validate([
            'plate' => 'required|string|max:255',
            'transport_line_id' => 'required|exists:transport_lines,transport_line_id',
            'unit_number' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        Trailer::create($request->all());

        return redirect()->route('admin.trailers.index')->with('success', 'Trailer created successfully.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'plate' => 'required|string|max:255',
            'transport_line_id' => 'required|exists:transport_lines,transport_line_id',
            'unit_number' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $trailer = Trailer::findOrFail($id);
        $trailer->update($request->all());

        return redirect()->route('admin.trailers.index')->with('success', 'Trailer updated successfully.');
    }

    public function destroy($id)
    {
        $trailer = Trailer::findOrFail($id);
        $trailer->delete();

        return redirect()->route('admin.trailers.index')->with('success', 'Trailer deleted successfully.');
    }
}
