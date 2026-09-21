<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $materials = DB::table('material_lab')
                ->orderBy('id', 'desc') 
                ->get();
                
            return response()->json(['data' => $materials]);
        }

        return view('laboratory.table_materials'); 
    }

    public function edit($id)
    {
        $material = DB::table('material_lab')->where('id', $id)->first();
        return response()->json($material);
    }

    public function update(Request $request, $id)
    {
        $entries = $request->input('entries', 0);
        $exits = $request->input('exits', 0);
        $stock = $entries - $exits;

        DB::table('material_lab')->where('id', $id)->update([
            'name'      => $request->input('name'),
            'um'        => $request->input('um'),
            'brand'     => $request->input('brand'),
            'entries'   => $entries,
            'exits'     => $exits,
            'stock'     => $stock >= 0 ? $stock : 0,
            'comments'  => $request->input('comments'),
        ]);

        return response()->json(['message' => 'Record updated successfully.']);
    }

    public function destroy($id)
    {
        DB::table('material_lab')->where('id', $id)->delete();
        
        return response()->json(['message' => 'Record removed.']);
    }

    public function store(Request $request)
    {
        $initialQuantity = $request->input('entries', 0);

        \Illuminate\Support\Facades\DB::table('material_lab')->insert([
            'name'      => $request->input('name'),
            'um'        => $request->input('um'),
            'brand'     => $request->input('brand'),
            'entries'   => $initialQuantity,
            'exits'     => 0,
            'stock'     => $initialQuantity,
            'comments'  => $request->input('comments'),
        ]);

        return response()->json(['message' => 'Material created successfully.']);
    }
}