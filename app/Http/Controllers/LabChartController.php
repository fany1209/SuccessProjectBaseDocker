<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabChartController extends Controller
{
    public function index()
    {
        return view('laboratory.grafica');
    }

    public function counts()
    {
        $reception = \DB::table('reception_of_samples')->count();
        $weekly    = \DB::table('weekly_results')->count();
        $lab       = \DB::table('laboratory_samples')->count();

        $pdfClicksD = \DB::table('pdf_clicks')
            ->whereRaw('UPPER(TRIM(pdf_type)) = ?', ['D'])
            ->count();

        return response()->json([
            'reception_of_samples' => $reception,
            'weekly_results'       => $weekly,
            'laboratory_samples'   => $lab,
            'pdf_clicks_d'         => $pdfClicksD,   
        ]);
    }

}
