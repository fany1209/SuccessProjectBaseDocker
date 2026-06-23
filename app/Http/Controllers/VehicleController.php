<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function getVehicles(Request $request){
        $t_line = $request->input('t_line');
        $search = $request->input('search');
        $query = Vehicle::select('vehicle_id','type','unit_number','plate','color','transport_lines.name')->join('transport_lines','transport_lines.transport_line_id','=','vehicles.transport_line_id');
        if(!empty($query)){
            $query->where(function ($q) use ($search) {
                $q->where('transport_lines.name', 'like', '%' . $search . '%')
                ->orWhere('plate', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('unit_number', 'like', '%' . $search . '%')
                ->orWhere('color', 'like', '%' . $search . '%');
            });
        }
        if (!empty($t_line)) {
            $query->where('transport_lines.transport_line_id', $t_line);
        }
        $vehicle = $query->get();
        return response()->json(['vehicles' => $vehicle]);
    }

    public function store(StoreVehicleRequest $request){
        Vehicle::create($request->only(['type','unit_number','plate','color','transport_line_id']));
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    public function destroy($id){
        $vehicle = Vehicle::find($id);
        if($vehicle) {
            $vehicle->delete();
            return response()->json(['success' => true, 'message' => 'Vehicle deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Vehicle not deleted'], 404);
        }
    }

    public function show($id){
        $vehicle = Vehicle::where('vehicle_id',$id)->first();
        return response()->json(['vehicle'=> $vehicle->only(['vehicle_id','type','unit_number','plate','color','transport_line_id'])]);
    }

    public function update(StoreVehicleRequest $request){
        Vehicle::where('vehicle_id',$request->vehicle_id)->update($request->only(['type','unit_number','plate','color','transport_line_id']));
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }
}
