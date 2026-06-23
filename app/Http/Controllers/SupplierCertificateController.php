<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SupplierCertificateController extends Controller
{
        public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'product_id' => 'required|exists:products,product_id',
            'file' => 'required|mimes:pdf|max:2048',
        ]);

        $path = $request->file('file')->store('certificates', 'public');
        $fileId = DB::table('files')->insertGetId([
            'path' => $path,
            'product_id' => $request->product_id,
        ]);

        DB::table('supplier_certificates')->insert([
            'supplier_id' => $request->supplier_id,
            'file_id' => $fileId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Certificado guardado correctamente.');
    }

        public function index()
    {
        $certificates = DB::table('supplier_certificates as sc')
            ->join('suppliers as s', 'sc.supplier_id', '=', 's.supplier_id')
            ->join('files as f', 'sc.file_id', '=', 'f.file_id')
            ->join('products as p', 'f.product_id', '=', 'p.product_id')
            ->select(
                'sc.id as certificate_id',
                's.name as supplier_name',
                'p.name as product_name',
                'f.path as file_path',
                'sc.created_at'
            )
            ->orderBy('sc.created_at', 'desc')
            ->get();

        return response()->json(['certificates' => $certificates]);
    }

        public function destroy($id)
    {
        $certificate = DB::table('supplier_certificates')->where('id', $id)->first();
        if (!$certificate) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $file = DB::table('files')->where('file_id', $certificate->file_id)->first();
        if ($file) {
            \Storage::disk('public')->delete($file->path);
            DB::table('files')->where('file_id', $file->file_id)->delete();
        }

        DB::table('supplier_certificates')->where('id', $id)->delete();

        return response()->json(['message' => 'Eliminado correctamente']);
    }

}