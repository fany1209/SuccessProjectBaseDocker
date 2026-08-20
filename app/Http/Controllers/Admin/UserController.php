<?php
namespace App\Http\Controllers\Admin;
/*
Controlador
UserController
11/08/25
Stefany
*/
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $users = User::where('name', 'like', "%{$search}%")->paginate(10);
        $roles = Role::all();

        return view('admin.users', compact('users','roles','search'))
            ->with([
                'open' => false,
                'create' => false,
                'user' => null
            ]);
    }

    public function create()
    {
        $roles = Role::all();
        $users = User::paginate(10);

        return view('admin.users', compact('users','roles'))
            ->with([
                'open' => true,
                'create' => true,
                'user' => new User()
            ]);
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $users = User::paginate(10);

        return view('admin.users', compact('users','roles'))
            ->with([
                'open' => true,
                'create' => false,
                'user' => $user
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg,webp|max:2048',
            'status'   => 'required|in:Activo,Inactivo',
            'tipo_empleado' => 'required|in:Trabajador,Practicante',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->status = $request->status;
        $user->tipo_empleado = $request->tipo_empleado;

        if ($request->hasFile('profile_photo')) {
            $user->profile_photo_path = $request->file('profile_photo')->store('users', 'public');
        }

        $user->save();

        // Asignar rol
        $roleName = Role::find($request->role_id)?->name;
        $user->assignRole($roleName);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|string|email|max:255|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg,webp|max:2048',
            'status'   => 'required|in:Activo,Inactivo',
            'tipo_empleado' => 'required|in:Trabajador,Practicante',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->tipo_empleado = $request->tipo_empleado;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('profile_photo')->store('users', 'public');
        }

        $user->save();

        // Sincronizar rol
        $roleName = Role::find($request->role_id)?->name;
        $user->syncRoles([$roleName]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function removePhoto(User $user)
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Photo removed successfully.');
    }
}
