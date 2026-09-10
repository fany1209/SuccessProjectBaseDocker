<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Models\Output;
use App\Models\ProductOutputs;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class OutputController extends Controller
{
public function show($id)
{
    $output = Output::select(
            'outputs.customer_id',
            'outputs.vendedor',
            'security_seal',
            'security_seal_number',
            'outputs.transport_line_id',
            'operator',
            'license_number',
            'unit_plates',
            'trailer_plates',
            'comments',
            'customers.name as cName',
            'transport_lines.name as tName'
        )
        ->where('output_id', $id)
        ->join('customers', 'customers.customer_id', '=', 'outputs.customer_id')
        ->leftJoin('transport_lines', 'transport_lines.transport_line_id', '=', 'outputs.transport_line_id')
        ->first();

    $products = ProductOutputs::where('output_id', $id)
        ->join('products', 'products.product_id', '=', 'product_outputs.product_id')
        ->get();

    return response()->json(['output' => $output, 'products' => $products]);
}


public function update(Request $request)
{
    try {

        $quanties = $request->input('quantity', []);
        if (is_array($quanties)) {
            $quanties = array_map(function ($q) {
                if (is_string($q)) {
                    $q = trim($q);
                    $q = str_replace(',', '.', $q);
                }
                return $q;
            }, $quanties);

            $request->merge(['quantity' => $quanties]);
        }

        $request->merge([
            'security_seal' => $request->filled('security_seal_number') ? 1 : 0
        ]);

        $request->validate([
            'operator' => 'required|string|max:200',
            'license_number' => 'required|string|max:50',
            'security_seal' => 'required|integer|in:0,1',
            'security_seal_number' => 'nullable|string|max:50',
            'unit_plates' => 'required|string|max:20',
            'trailer_plates' => 'nullable|string|max:20',
            'comments' => 'nullable|string|max:300',
            'customer_id' => 'required|integer',
            'transport_line_id' => 'required|integer',
            'vendedor' => 'required|string|max:200',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:0.001',
            'label_batch' => 'required|array|min:1',
            'label_batch.*' => 'required|string|max:50',
        ]);

        $updateData = $request->only([
            'operator',
            'license_number',
            'security_seal',
            'security_seal_number',
            'unit_plates',
            'trailer_plates',
            'comments',
            'customer_id',
            'transport_line_id',
            'vendedor', 
        ]);

        DB::table('outputs')->where('output_id', $request->id)->update($updateData);

        $products = $request->input('product_id', []);

        foreach ($products as $index => $productId) {
            $qty = isset($request->quantity[$index]) ? (float) $request->quantity[$index] : 0;

            ProductOutputs::where('output_id', $request->id)
                ->where('product_id', $productId)
                ->update([
                    'quantity' => $qty,
                    'label_batch' => $request->label_batch[$index] ?? null
                ]);
        }

        return response()->json(['message' => 'Operation successfuly make it'], 201);

    } catch (QueryException $e) {
        return DatabaseErrors::handle($e);
    }
}


}
