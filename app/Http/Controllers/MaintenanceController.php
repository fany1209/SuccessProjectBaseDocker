<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function index()
    {
        $records = MaintenanceRecord::with(['equipment', 'maintenancePlan'])
            ->whereIn('status', ['pending', 'printed'])
            ->orderBy('scheduled_date', 'asc')
            ->get();
            
        return view('maintenance.index', compact('records'));
    }
    
    public function history()
    {
        $records = MaintenanceRecord::with(['equipment', 'maintenancePlan'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);
            
        return view('maintenance.history', compact('records'));
    }

    public function printChecklist(MaintenanceRecord $record)
    {
        $record->load(['equipment', 'maintenancePlan.checklistItems']);
        
        if ($record->status === 'pending') {
            $record->update([
                'status' => 'printed',
                'printed_at' => now(),
            ]);
        }
        
        $pdf = Pdf::loadView('maintenance.pdf.checklist', compact('record'));
        return $pdf->download("Checklist_{$record->code}.pdf");
    }

    public function complete(Request $request, MaintenanceRecord $record)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($record->status !== 'printed') {
            return back()->withErrors(['Debes imprimir el formato antes de poder completarlo.']);
        }
        
        $request->validate([
            'evidence_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $path = $request->file('evidence_file')->store('maintenance_evidence', 'public');

        $record->update([
            'status' => 'completed',
            'completed_at' => now(),
            'evidence_file' => $path
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Mantenimiento marcado como completado y evidencia guardada.');
    }
}
