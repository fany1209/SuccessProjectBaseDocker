<?php
/*
Controlador: SalesStatusController
13/08/25
stefany
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleStatus;
use Illuminate\Http\Request;

class SalesStatusController extends Controller
{
   
    public function index(Request $request)
    {
        $salesStatus = SaleStatus::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(10);

        $open = false;
        $create = false;
        $status = null;

        return view('admin.sales_status', compact('salesStatus', 'open', 'create', 'status'));
    }

    
    public function create()
    {
        $salesStatus = SaleStatus::paginate(10);
        $open = true;
        $create = true;
        $status = null;

        return view('admin.sales_status', compact('salesStatus', 'open', 'create', 'status'));
    }

 
    public function edit($id)
    {
        $salesStatus = SaleStatus::paginate(10);
        $status = SaleStatus::findOrFail($id);
        $open = true;
        $create = false;

        return view('admin.sales_status', compact('salesStatus', 'open', 'create', 'status'));
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:70',
            'description' => 'nullable|string|max:255',
        ]);

        SaleStatus::create($request->all());

        return redirect()->route('admin.sales_status.index')->with('success', 'Status created successfully.');
    }

  
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:70',
            'description' => 'nullable|string|max:255',
        ]);

        $status = SaleStatus::findOrFail($id);
        $status->update($request->all());

        return redirect()->route('admin.sales_status.index')->with('success', 'Status updated successfully.');
    }


    public function destroy($id)
    {
        $status = SaleStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('admin.sales_status.index')->with('success', 'Status deleted successfully.');
    }
}
