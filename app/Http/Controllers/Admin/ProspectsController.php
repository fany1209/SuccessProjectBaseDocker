<?php
/*
Controlador: ProspectController
13/08/25
stefany
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\Sector;
use Illuminate\Http\Request;

class ProspectsController extends Controller
{
    
    public function index(Request $request)
    {
        $sectors = Sector::all();
        $prospects = Prospect::with('sector')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->sector_id, function($q) use ($request) {
                $q->where('sector_id', $request->sector_id);
            })
            ->when($request->city, function($q) use ($request) {
                $q->where('city', 'like', "%{$request->city}%");
            })
            ->when($request->state, function($q) use ($request) {
                $q->where('state', 'like', "%{$request->state}%");
            })
            ->paginate(10);

        $open = false;
        $create = false;
        $prospect = null;

        return view('admin.prospects', compact('sectors', 'prospects', 'open', 'create', 'prospect'));
    }


    public function create()
    {
        $sectors = Sector::all();
        $prospects = Prospect::with('sector')->paginate(10);
        $open = true;
        $create = true;
        $prospect = null;

        return view('admin.prospects', compact('sectors', 'prospects', 'open', 'create', 'prospect'));
    }

    public function edit($id)
    {
        $sectors = Sector::all();
        $prospects = Prospect::with('sector')->paginate(10);
        $prospect = Prospect::findOrFail($id);
        $open = true;
        $create = false;

        return view('admin.prospects', compact('sectors', 'prospects', 'open', 'create', 'prospect'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:13',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'address' => 'nullable|string|max:200',
            'sector_id' => 'nullable|exists:sectors,sector_id',
        ]);

        Prospect::create($request->all());

        return redirect()->route('admin.prospects.index')->with('success', 'Prospect created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:13',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'address' => 'nullable|string|max:200',
            'sector_id' => 'nullable|exists:sectors,sector_id',
        ]);

        $prospect = Prospect::findOrFail($id);
        $prospect->update($request->all());

        return redirect()->route('admin.prospects.index')->with('success', 'Prospect updated successfully.');
    }

    public function destroy($id)
    {
        $prospect = Prospect::findOrFail($id);
        $prospect->delete();

        return redirect()->route('admin.prospects.index')->with('success', 'Prospect deleted successfully.');
    }
}
