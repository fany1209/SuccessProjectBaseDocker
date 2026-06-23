<?php
/*
Controlador: CustomerController
13/08/25
stefany
Actualizado por: fany
Fecha de actualización: 19-03-2026
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sector;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $sectors = Sector::all();
        $customers = Customer::with('sector')
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%")
                          ->orWhere('contact', 'like', "%{$request->search}%")
                          ->orWhere('rfc', 'like', "%{$request->search}%")
                          ->orWhere('customer_code', 'like', "%{$request->search}%");
                });
            })
            ->when($request->sector_id, function($q) use ($request) {
                $q->where('sector_id', $request->sector_id);
            })
            ->when($request->city, function($q) use ($request) {
                $q->where('city', 'like', "%{$request->city}%");
            })
            ->when($request->state, function($q) use ($request) {
                $q->where('state', 'like', "%{$request->state}%");
            })
            ->latest() 
            ->paginate(10);

        $open = false;
        $create = false;
        $customer = null;

        return view('admin.customers', compact('sectors', 'customers', 'open', 'create', 'customer'));
    }


    public function create()
    {
        $sectors = Sector::all();
        $customers = Customer::with('sector')->paginate(10);
        $open = true;
        $create = true;
        $customer = null;

        return view('admin.customers', compact('sectors', 'customers', 'open', 'create', 'customer'));
    }

    public function edit($id)
    {
        $sectors = Sector::all();
        $customers = Customer::with('sector')->paginate(10);
        $customer = Customer::findOrFail($id);
        $open = true;
        $create = false;

        return view('admin.customers', compact('sectors', 'customers', 'open', 'create', 'customer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'contact' => 'nullable|string|max:150', 
            'vendedor' => 'nullable|string|max:150', 
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:13',
            'postal_code' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:200',
            'delivery_address' => 'nullable|string|max:500', 
            'customer_code' => 'required|string|max:15',
            'sector_id' => 'nullable|exists:sectors,sector_id',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'contact' => 'nullable|string|max:150', 
            'vendedor' => 'nullable|string|max:150', 
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:13',
            'postal_code' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:200',
            'delivery_address' => 'nullable|string|max:500', 
            'customer_code' => 'required|string|max:15',
            'sector_id' => 'nullable|exists:sectors,sector_id',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
