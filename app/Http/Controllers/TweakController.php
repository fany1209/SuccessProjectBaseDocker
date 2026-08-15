<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTweakRequest;
use App\Models\Inventory;
use App\Models\Tweak;
use Illuminate\Http\Request;

class TweakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTweakRequest $request)
    {
        $new_stock = 0;
        $data = $request->only(['type','quantity','comments','inventory_id']);
        $data['user_id'] = auth()->id();
        Tweak::create($data);
        $stock = Inventory::select('stock')->where('inventory_id',$request->inventory_id)->first();
        if($request->type == 'Input'){
            $new_stock = $stock->stock + $request->quantity;
        }else{
            $new_stock = $stock->stock - $request->quantity;
        }
        Inventory::where('inventory_id',$request->inventory_id)->update(['stock'=>$new_stock]);
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
