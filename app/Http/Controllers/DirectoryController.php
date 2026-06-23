<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;      
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Importante para las fechas

class DirectoryController extends Controller
{
    public function getDirectory()
    {
        try {
            $suppliers = DB::table('supplier_directory')
                ->select(
                    'Code_supplier',
                    'Name',
                    'Product',
                    'Address',
                    'Phone',
                    'Email',
                    'RFC',
                    'Contact',
                    'created_at', // Agregado
                    'updated_at'  // Agregado
                )
                ->orderBy('Name', 'asc')
                ->get();

            return response()->json([
                'suppliers' => $suppliers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los proveedores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Name'          => 'required|string|max:255',
                'Code_supplier' => 'required|integer|unique:supplier_directory,Code_supplier',
                'Product'       => 'nullable|string|max:255',
                'Address'       => 'nullable|string',
                'Phone'         => 'nullable|string|max:20',
                'Email'         => 'nullable|email|max:100',
                'RFC'           => 'nullable|string|max:15',
                'Contact'       => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $now = Carbon::now(); // Fecha y hora actual

            DB::table('supplier_directory')->insert([
                'Name'          => $request->Name,
                'Code_supplier' => $request->Code_supplier,
                'Product'       => $request->Product,
                'Address'       => $request->Address,
                'Phone'         => $request->Phone,
                'Email'         => $request->Email,
                'RFC'           => $request->RFC,
                'Contact'       => $request->Contact,
                'created_at'    => $now, // Agregado
                'updated_at'    => $now  // Agregado
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Proveedor guardado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $code)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Name'          => 'required|string|max:255',
                'Code_supplier' => 'required|integer', 
                'Product'       => 'nullable|string|max:255',
                'Address'       => 'nullable|string',
                'Phone'         => 'nullable|string|max:20',
                'Email'         => 'nullable|email|max:100',
                'RFC'           => 'nullable|string|max:15',
                'Contact'       => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::table('supplier_directory')
                ->where('Code_supplier', $request->original_code) 
                ->update([
                    'Name'          => $request->Name,
                    'Code_supplier' => $request->Code_supplier,
                    'Product'       => $request->Product,
                    'Address'       => $request->Address,
                    'Phone'         => $request->Phone,
                    'Email'         => $request->Email,
                    'RFC'           => $request->RFC,
                    'Contact'       => $request->Contact,
                    'updated_at'    => Carbon::now() // Agregado: Actualiza solo la fecha de edición
                ]);

            return response()->json([
                'success' => true, 
                'message' => 'Proveedor actualizado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($code)
    {
        try {
            $deleted = DB::table('supplier_directory')
                ->where('Code_supplier', $code)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Proveedor eliminado correctamente.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró el proveedor especificado.'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}