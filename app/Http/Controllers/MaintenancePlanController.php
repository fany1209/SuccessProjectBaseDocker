<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePlan;
use App\Models\Equipment;
use App\Models\ChecklistTemplateItem;
use Illuminate\Http\Request;

class MaintenancePlanController extends Controller
{
    public function index()
    {
        $plans = MaintenancePlan::with(['equipment', 'checklistItems'])->get();
        $equipments = Equipment::where('is_active', true)->get();
        return view('maintenance.plans.index', compact('plans', 'equipments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'name' => 'required',
            'frequency_days' => 'required|integer|min:1',
            'type' => 'required|in:frequent,deep',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'required_with:checklist_items|string'
        ]);

        $plan = MaintenancePlan::create([
            'equipment_id' => $request->equipment_id,
            'name' => $request->name,
            'frequency_days' => $request->frequency_days,
            'type' => $request->type
        ]);

        if($request->has('checklist_items')) {
            foreach($request->checklist_items as $index => $itemDescription) {
                if(!empty($itemDescription)) {
                    ChecklistTemplateItem::create([
                        'maintenance_plan_id' => $plan->id,
                        'description' => $itemDescription,
                        'order' => $index
                    ]);
                }
            }
        }

        return back()->with('success', 'Plan registrado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $plan = MaintenancePlan::findOrFail($id);
        
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'name' => 'required',
            'frequency_days' => 'required|integer|min:1',
            'type' => 'required|in:frequent,deep',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'nullable|string' // change required_with to nullable to prevent strict fail
        ]);
        
        \Log::info('Actualizando plan:', $request->all());

        $plan->update([
            'equipment_id' => $request->equipment_id,
            'name' => $request->name,
            'frequency_days' => $request->frequency_days,
            'type' => $request->type
        ]);

        // Recrear checklist items
        $plan->checklistItems()->delete();
        
        if($request->has('checklist_items')) {
            foreach($request->checklist_items as $index => $itemDescription) {
                if(!empty($itemDescription)) {
                    ChecklistTemplateItem::create([
                        'maintenance_plan_id' => $plan->id,
                        'description' => $itemDescription,
                        'order' => $index
                    ]);
                }
            }
        }

        return back()->with('success', 'Plan actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $plan = MaintenancePlan::findOrFail($id);
        $plan->delete();
        return back()->with('success', 'Plan eliminado exitosamente.');
    }
}
