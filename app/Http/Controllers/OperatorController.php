<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperatorRequest;
use App\Models\Operator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorController extends Controller
{
    public function getOperators(){
        $operator = Operator::all();
        return response()->json(['operators' => $operator]);
    }

    public function store(StoreOperatorRequest $request){
        try{
            DB::transaction(function () use ($request){
                Operator::create($request->only(['name','license']));
                return response()->json(['message' => 'Operation successfuly make it'], 201);
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function destroy($id){
        try{
            DB::transaction(function () use ($request){
                $operator = Operator::find($id);
                if($operator) {
                    $operator->delete();
                    return response()->json(['success' => true, 'message' => 'Operator deleted']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Operator not deleted'], 404);
                }
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function show($id){
        $operator = Operator::where('operator_id',$id)->first();
        return response()->json(['operator'=> $operator->only(['operator_id','name','license'])]);
    }

    public function update(StoreOperatorRequest $request){
        try{
            DB::transaction(function () use ($request){
                Operator::where('operator_id',$request->operator_id)->update($request->only(['name','license']));
                return response()->json(['message' => 'Operation successfuly make it'], 201);
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }
}
