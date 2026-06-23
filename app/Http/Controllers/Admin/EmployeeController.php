<?php
/*
controlador
emmployeecontroller
11/08/25
stefany
*/ 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%")
                             ->orWhere('rfc', 'like', "%{$search}%")
                             ->orWhere('curp', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        
        return view('admin.employees', [
            'employees' => $employees,
            'open' => false,
            'create' => false,
            'employee' => null,
        ]);
    }

    public function create()
    {
        $employees = Employee::paginate(10);

        return view('admin.employees', [
            'employees' => $employees,
            'open' => true,      
            'create' => true,    
            'employee' => null,   
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
            'curp' => 'nullable|string|max:18',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
        ]);

        Employee::create($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $employees = Employee::paginate(10);

        return view('admin.employees', [
            'employees' => $employees,
            'open' => true,
            'create' => false, 
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
            'curp' => 'nullable|string|max:18',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }
}
