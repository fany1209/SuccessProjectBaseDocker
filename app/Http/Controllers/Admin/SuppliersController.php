<?php
/*
Controlador: SupplierController
13/08/25
stefany
Actualizado por: stefany 
Fecha de actualización: 19-03-2026
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Sector;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::with('sector')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('contact', 'like', "%{$request->search}%") 
                  ->orWhere('rfc', 'like', "%{$request->search}%");    
            })
            ->paginate(10);

        $open = false;
        $create = false;
        $supplier = null;
        $sectors = Sector::all();

        return view('admin.suppliers', compact('suppliers', 'open', 'create', 'supplier', 'sectors'));
    }

    public function create()
    {
        $open = true;
        $create = true;
        $supplier = null;
        $suppliers = Supplier::with('sector')->paginate(10);
        $sectors = Sector::all();

        return view('admin.suppliers', compact('suppliers', 'open', 'create', 'supplier', 'sectors'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $open = true;
        $create = false;
        $suppliers = Supplier::with('sector')->paginate(10);
        $sectors = Sector::all();

        return view('admin.suppliers', compact('suppliers', 'open', 'create', 'supplier', 'sectors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'contact' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:13',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'address' => 'nullable|string|max:200',
            'supplier_code' => 'required|string|max:15',
            'sector_id' => 'nullable|exists:sectors,sector_id',
        ]);

        Supplier::create($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:200',
            'contact' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:13',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'address' => 'nullable|string|max:200',
            'supplier_code' => 'required|string|max:15',
            'sector_id' => 'nullable|exists:sectors,sector_id',
        ]);

        try {
            $supplier = Supplier::findOrFail($id);
                    
            $supplier->update($request->all());
            
            $supplier->refresh(); 

            return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error al actualizar Supplier: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
