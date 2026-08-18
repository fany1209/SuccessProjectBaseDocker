<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceEquipment;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Notifications\MaintenanceConfirmedNotification;
use App\Models\User;

class MaintenanceController extends Controller
{
    public function index()
    {
        $equipments = MaintenanceEquipment::with(['role', 'maintenanceRecords' => function ($query) {
            $query->latest()->limit(1);
        }])->get();

        $roles = Role::all();

        return view('maintenance.index', compact('equipments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            'frequency_days' => 'required|integer|min:1',
            'role_id' => 'required|exists:roles,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('equipments', 'public');
        }

        $equipment = MaintenanceEquipment::create([
            'name' => $request->name,
            'model' => $request->model,
            'comments' => $request->comments,
            'frequency_days' => $request->frequency_days,
            'role_id' => $request->role_id,
            'image_path' => $imagePath,
            'next_maintenance_date' => Carbon::now()->addDays($request->frequency_days),
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Equipo registrado exitosamente.');
    }

    public function show($id)
    {
        $equipment = MaintenanceEquipment::with(['role', 'maintenanceRecords.departmentUser', 'maintenanceRecords.adminUser'])->findOrFail($id);
        
        $currentRecord = $equipment->maintenanceRecords()->whereIn('status', ['pending', 'pending_admin'])->first();

        return view('maintenance.show', compact('equipment', 'currentRecord'));
    }

    public function confirmDepartment(Request $request, $id)
    {
        $equipment = MaintenanceEquipment::findOrFail($id);
        
        // Find existing pending record or create one if it doesn't exist (e.g. triggered manually)
        $record = $equipment->maintenanceRecords()->where('status', 'pending')->first();
        
        if (!$record) {
            $record = new MaintenanceRecord([
                'equipment_id' => $equipment->id,
                'scheduled_date' => $equipment->next_maintenance_date,
            ]);
        }

        $record->status = 'pending_admin';
        $record->department_confirmed_at = now();
        $record->department_user_id = auth()->id();
        if ($request->has('comments')) {
            $record->comments = $request->comments;
        }
        $record->save();

        // Notify admins
        $admins = User::role('Admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new MaintenanceConfirmedNotification($equipment));
        }

        return redirect()->back()->with('success', 'Mantenimiento confirmado. En espera de revisión del administrador.');
    }

    public function confirmAdmin(Request $request, $id)
    {
        $equipment = MaintenanceEquipment::findOrFail($id);
        
        $record = $equipment->maintenanceRecords()->where('status', 'pending_admin')->firstOrFail();
        
        $record->status = 'completed';
        $record->admin_confirmed_at = now();
        $record->admin_user_id = auth()->id();
        $record->save();

        // Calculate next maintenance date
        $equipment->next_maintenance_date = now()->addDays($equipment->frequency_days);
        $equipment->save();

        return redirect()->back()->with('success', 'Mantenimiento completado exitosamente.');
    }
}
