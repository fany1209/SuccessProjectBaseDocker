<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrailerRequest;
use App\Models\Trailer;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
    public function getTrailers(Request $request){
        $t_line = $request->input('t_line');
        $search = $request->input('search');
        $query = Trailer::select('trailer_id','type','unit_number','plate','color','transport_lines.name')->join('transport_lines','transport_lines.transport_line_id','=','trailers.transport_line_id');
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
        $trailer = $query->get();
        return response()->json(['trailers' => $trailer]);
    }

    public function store(StoreTrailerRequest $request){
        Trailer::create($request->only(['type','unit_number','plate','color','transport_line_id']));
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    public function destroy($id){
        $trailer = Trailer::find($id);
        if($trailer) {
            $trailer->delete();
            return response()->json(['success' => true, 'message' => 'Trailer deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Trailer not deleted'], 404);
        }
    }

    public function show($id){
        $trailer = Trailer::where('trailer_id',$id)->first();
        return response()->json(['trailer'=> $trailer->only(['trailer_id','type','unit_number','plate','color','transport_line_id'])]);
    }

    public function update(StoreTrailerRequest $request){
        Trailer::where('trailer_id',$request->trailer_id)->update($request->only(['type','unit_number','plate','color','transport_line_id']));
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }
}
