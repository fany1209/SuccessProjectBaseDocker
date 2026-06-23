<?php
/*
controlador
transport-linecontroller
13/08/25
stefany
*/ 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransportLine;

class TransportLineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $transport_lines = TransportLine::where('name', 'like', "%$search%")
            ->orderBy('name')
            ->paginate(10);

        return view('admin.transport-lines', [
            'transport_lines' => $transport_lines,
            'open' => false,
            'create' => false,
            'transport_line' => null,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('admin.transport-lines', [
            'transport_lines' => TransportLine::orderBy('name')->paginate(10),
            'open' => true,
            'create' => true,
            'transport_line' => null,
            'search' => '',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        TransportLine::create(['name' => $request->name]);

        return redirect()->route('admin.transport-lines.index')
            ->with('success', 'Transport Line created successfully.');
    }

    public function edit(TransportLine $transport_line)
    {
        return view('admin.transport-lines', [
            'transport_lines' => TransportLine::orderBy('name')->paginate(10),
            'open' => true,
            'create' => false,
            'transport_line' => $transport_line,
            'search' => '',
        ]);
    }

    public function update(Request $request, TransportLine $transport_line)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $transport_line->update(['name' => $request->name]);

        return redirect()->route('admin.transport-lines.index')
            ->with('success', 'Transport Line updated successfully.');
    }

    public function destroy(TransportLine $transport_line)
    {
        $transport_line->delete();

        return redirect()->route('admin.transport-lines.index')
            ->with('success', 'Transport Line deleted successfully.');
    }
}
