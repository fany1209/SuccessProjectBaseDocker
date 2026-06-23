<?php
/*
Controlador: SectorController
Fecha: 13/08/25
Autor: Stefany
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
   
    public function index(Request $request)
    {
        $sectors = Sector::when($request->search, function($q, $search) {
            $q->where('name', 'like', "%$search%");
        })->paginate(10);

        $open = false;
        $create = false;
        $sector = null;

        return view('admin.sectors', compact('sectors', 'open', 'create', 'sector'));
    }

    
    public function create()
    {
        $sectors = Sector::paginate(10);
        $open = true;
        $create = true;
        $sector = null;

        return view('admin.sectors', compact('sectors', 'open', 'create', 'sector'));
    }

    
    public function edit($id)
    {
        $sector = Sector::findOrFail($id);
        $sectors = Sector::paginate(10); // Para la tabla
        $open = true;
        $create = false;

        return view('admin.sectors', compact('sectors', 'sector', 'open', 'create'));
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:6',
        ]);

        Sector::create($request->all());

        return redirect()->route('admin.sectors.index')->with('success', 'Sector created successfully.');
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:6',
        ]);

        $sector = Sector::findOrFail($id);
        $old_code = 'SC'.$sector->code;
        $new_code = 'SC'.$request->code;
        Customer::where('sector_id',$sector->sector_id)->update([
            'customer_code' => DB::raw("replace(customer_code,'$old_code','$new_code')")
        ]);
        $sector->update($request->all());

        return redirect()->route('admin.sectors.index')->with('success', 'Sector updated successfully.');
    }

   
    public function destroy($id)
    {
        $sector = Sector::findOrFail($id);
        $sector->delete();

        return redirect()->route('admin.sectors.index')->with('success', 'Sector deleted successfully.');
    }
}
