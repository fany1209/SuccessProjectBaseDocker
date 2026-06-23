<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReagentController extends Controller
{
  
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reagents = DB::table('reagent_inventory')
                ->orderBy('id', 'desc') 
                ->get();
                
            return response()->json(['data' => $reagents]);
        }

        return view('laboratory.table_react');
    }

    public function destroy($id)
    {
        DB::table('reagent_inventory')->where('id', $id)->delete();

        return response()->json([
            'message' => 'Record removed.'
        ]);
    }
    public function edit($id)
    {
        $reagent = DB::table('reagent_inventory')->where('id', $id)->first();
        return response()->json($reagent);
    }

    
   public function update(Request $request, $id)
    {
        $entries = $request->input('entries', 0);
        $exits = $request->input('exits', 0);
        $stock = $entries - $exits;

        DB::table('reagent_inventory')->where('id', $id)->update([
            'code'    => $request->input('code'),
            'name'    => $request->input('name'),
            'um'      => $request->input('um'),
            'brand'   => $request->input('brand'),
            'color'   => $request->input('color'),
            'entries' => $entries,
            'exits'   => $exits,
            'stock'   => $stock >= 0 ? $stock : 0, 
        ]);

        return response()->json(['message' => 'Record updated successfully.']);
    }
   
    public function store(Request $request)
    {
        $initialQuantity = $request->input('entries', 0);

        \Illuminate\Support\Facades\DB::table('reagent_inventory')->insert([
            'code'    => $request->input('code'),
            'name'    => $request->input('name'),
            'um'      => $request->input('um'),
            'brand'   => $request->input('brand'),
            'color'   => $request->input('color'),
            'entries' => $initialQuantity,
            'exits'   => 0, 
            'stock'   => $initialQuantity,
        ]);

        return response()->json(['message' => 'Reagent created successfully.']);
    }
}