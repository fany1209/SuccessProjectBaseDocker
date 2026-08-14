<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $equipments = DB::table('laboratory_equipments')
                ->orderBy('id', 'desc') 
                ->get();
                
            return response()->json(['data' => $equipments]);
        }

        return view('laboratory.table_equipments'); 
    }

    public function edit($id)
    {
        $equipment = DB::table('laboratory_equipments')->where('id', $id)->first();
        return response()->json($equipment);
    }

    public function update(Request $request, $id)
    {
        DB::table('laboratory_equipments')->where('id', $id)->update([
            'internal_code' => $request->input('internal_code'),
            'name'          => $request->input('name'),
            'quantity'      => $request->input('quantity', 0),
            'brand'         => $request->input('brand'),
            'status'        => $request->input('status', 'funcional'),
            'updated_at'    => now(),
        ]);

        return response()->json(['message' => 'Record updated successfully.']);
    }

    public function destroy($id)
    {
        DB::table('laboratory_equipments')->where('id', $id)->delete();
        
        return response()->json(['message' => 'Record removed.']);
    }

    public function store(Request $request)
    {
        DB::table('laboratory_equipments')->insert([
            'internal_code' => $request->input('internal_code'),
            'name'          => $request->input('name'),
            'quantity'      => $request->input('quantity', 0),
            'brand'         => $request->input('brand'),
            'status'        => $request->input('status', 'funcional'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json(['message' => 'Equipment created successfully.']);
    }
}
