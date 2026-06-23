<?php
/*
Transport lines
Controlador Transport lines
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
*/
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTLineRequest;
use App\Models\TransportLine;

class TransportLineController extends Controller
{
    public function getTransportLines(){
        $transport_lines = TransportLine::all();
        return response()->json(['transport_lines' => $transport_lines]);
    }

    public function store(StoreTLineRequest $request){
        TransportLine::create($request->only(['name']));
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    public function destroy($id){
        $transport_line = TransportLine::find($id);
        if($transport_line) {
            $transport_line->delete();
            return response()->json(['success' => true, 'message' => 'Transport line deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Transport line not deleted'], 404);
        }
    }

    public function show($id){
        $transport_line = TransportLine::where('transport_line_id',$id)->first();
        return response()->json(['transport_line'=> $transport_line->only(['transport_line_id','name'])]);
    }

    public function update(StoreTLineRequest $request){
        TransportLine::where('transport_line_id',$request->transport_line_id)->update($request->only(['name']));
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }
}
