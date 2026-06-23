<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;


class WeeklyPlanController extends Controller
{
    public function index()
    {
        return view('weekly_plans.index');
    }

    public function datatable(Request $request)
    {
        $rows = DB::table('weekly_work_plans')
            ->select('id','week_range','review_date','project_name','responsible_name','created_at')
            ->orderByDesc('review_date')
            ->get()
            ->map(function($r){
                return [
                    'id'          => $r->id,
                    'report_code' => $r->week_range ?: sprintf('WKP-%05d', $r->id),
                    'entry_date'  => optional(\Carbon\Carbon::parse($r->review_date))->format('Y-m-d'),
                    'issue_date'  => optional(\Carbon\Carbon::parse($r->created_at))->format('Y-m-d'),
                    'client_name' => $r->responsible_name ?: ($r->project_name ?: '—'),
                    'pdf_url'     => route('weekly.plans.pdf', $r->id),
                    'delete_url'  => route('weekly.plans.delete', $r->id),
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function pdf($id)
    {
        $plan = DB::table('weekly_work_plans')->where('id', $id)->first();
        abort_unless($plan, 404, 'Registro no encontrado');

        $objs = DB::table('weekly_objectives')
            ->where('plan_id', $id)
            ->orderBy('position_order')
            ->get();

        $res = DB::table('weekly_results')
            ->where('plan_id', $id)
            ->orderBy('position_order')
            ->get();

        $find = DB::table('weekly_findings')
            ->where('plan_id', $id)
            ->orderBy('position_order')
            ->get();

        $next = DB::table('weekly_next_actions')
            ->where('plan_id', $id)
            ->orderBy('position_order')
            ->get();

        $data = [
            'pagina_actual'        => 1,
            'paginas_total'        => 3,
            'semana_rango'         => $plan->week_range,       
            'fecha_revision'       => $plan->review_date,     
            'proyecto'             => $plan->project_name,
            'responsable'          => $plan->responsible_name,
            'total_horas'          => $plan->total_hours,
            'objetivos' => $objs->map(fn($o) => [
                'n'           => $o->item_number,
                'titulo'      => $o->title,
                'descripcion' => $o->description,
                'horas'       => $o->hours,
            ])->toArray(),

            'resultados' => $res->map(fn($r) => [
                'n'      => $r->objective_number,
                'texto'  => $r->result_text,
                'cumple' => (bool) $r->is_met,
            ])->toArray(),

            'hallazgos' => $find->map(fn($h) => [
                'hallazgo' => $h->finding_text,
                'causa'    => $h->cause_text,
                'propuesta'=> $h->proposal_text,
            ])->toArray(),

            'proxima_semana_rango' => optional($next->first())->next_week_range ?? '',
            'plan_proxima'         => $next->map(fn($n) => [
                'plan'     => $n->plan_text,
                'acciones' => $n->actions_text,
            ])->toArray(),
        ];

        $pdf = Pdf::loadView('formats.laboratory.11', $data)->setPaper('letter');

        $slug = Str::slug(($data['semana_rango'] ?: 'semana'), '-');
        $fileName = 'PlanSemanal_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function destroy($id)
    {
        if (Gate::denies('laboratory.delete')) { 
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $exists = DB::table('weekly_work_plans')->where('id',$id)->exists();
        if (!$exists) return response()->json(['message'=>'Registro no encontrado.'],404);

        DB::transaction(function() use ($id) {
            DB::table('weekly_work_plans')->where('id',$id)->delete();
        });

        return response()->json(['message' => 'Eliminado correctamente.']);
    }
}
