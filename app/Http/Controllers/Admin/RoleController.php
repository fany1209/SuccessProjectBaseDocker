<?php
/*
controlador
rolecontroller
11/08/25
stefany
*/ 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $open = false;
        $create = false;
        $role = null;

        $roles = Role::with('permissions')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(10);

        $permissions = Permission::all();

        return view('admin.roles', compact(
            'roles', 'permissions', 'open', 'create', 'role', 'search'
        ));
    }

  
    public function create(Request $request)
    {
        $search = $request->get('search', '');
        $open = true;
        $create = true;
        $role = new Role();
        $role->guard_name = 'web'; 

        $roles = Role::with('permissions')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(10);

        $permissions = Permission::where('guard_name', $role->guard_name)->get();

        return view('admin.roles', compact(
            'roles', 'permissions', 'open', 'create', 'role', 'search'
        ));
    }

  
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web'
        ]);

        
        $permissionIds = $validated['permissions'] ?? [];
        $permissions = Permission::whereIn('id', $permissionIds)
            ->where('guard_name', $role->guard_name)
            ->pluck('name')
            ->toArray();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

  
    public function edit(Role $role, Request $request)
    {
        $search = $request->get('search', '');
        $open = true;
        $create = false;

        $roles = Role::with('permissions')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(10);

        $permissions = Permission::where('guard_name', $role->guard_name)->get();
        $rolePermissions = $role->permissions()->pluck('id')->toArray();

        return view('admin.roles', compact(
            'roles', 'permissions', 'open', 'create', 'role', 'rolePermissions', 'search'
        ));
    }

  
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $validated['name']]);

        $permissionIds = $validated['permissions'] ?? [];
        $permissions = Permission::whereIn('id', $permissionIds)
            ->where('guard_name', $role->guard_name)
            ->pluck('name')
            ->toArray();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}