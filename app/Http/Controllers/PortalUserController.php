<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PortalUserController extends Controller
{

    public function index()
    {
        $clientesSistemas = DB::table('customers')
            ->select('customer_id', 'name', 'customer_code')
            ->orderByRaw('TRIM(LOWER(name)) ASC')
            ->get();

        return view('portal-users', compact('clientesSistemas')); 
    }

    public function getPortalUsers(Request $request)
    {
        $query = DB::table('portal_users')
            ->join('customers', 'customers.customer_id', '=', 'portal_users.customer_id')
            ->select(
                'portal_users.id as portal_id',
                'portal_users.nombre_contacto',
                'portal_users.empresa',
                'portal_users.email',
                'portal_users.is_active',
                'customers.customer_code',
                'customers.name as customer_name'
            );

        if ($request->has('name') && $request->name != '') {
            $query->where('portal_users.empresa', 'like', '%' . $request->name . '%');
        }

        $users = $query->orderBy('portal_users.created_at', 'desc')->get();

        return response()->json(['users' => $users]);
    }
   
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'     => 'required|integer', 
            'nombre_contacto' => 'required|string|max:150',
            'empresa'         => 'required|string|max:200',
            'email'           => 'required|email|unique:portal_users,email', 
            'password'        => 'required|string|min:6|confirmed', 
        ]);

        try {
            DB::table('portal_users')->insert([
                'customer_id'     => $request->customer_id,
                'nombre_contacto' => $request->nombre_contacto,
                'empresa'         => $request->empresa,
                'email'           => $request->email,
                'password'        => Hash::make($request->password), 
                'is_active'       => $request->has('is_active') ? 1 : 0, 
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            return back()->with('success', '¡El usuario para el portal de clientes se ha creado con éxito!');

        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Hubo un problema al guardar: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = DB::table('portal_users')->where('id', $id)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id'     => 'required|integer',
            'nombre_contacto' => 'required|string|max:150',
            'empresa'         => 'required|string|max:200',
            'email'           => 'required|email|unique:portal_users,email,' . $id,
            'password'        => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'customer_id'     => $request->customer_id,
            'nombre_contacto' => $request->nombre_contacto,
            'empresa'         => $request->empresa,
            'email'           => $request->email,
            'is_active'       => $request->has('is_active') ? 1 : 0,
            'updated_at'      => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('portal_users')->where('id', $id)->update($updateData);

        return response()->json(['success' => true, 'message' => '¡Usuario actualizado con éxito!']);
    }

    public function destroy($id)
    {
        try {
            DB::table('portal_users')->where('id', $id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Usuario eliminado con éxito.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadDocs(Request $request, $saleId)
    {
        $request->validate([
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'xml_file' => 'nullable|file|mimes:xml,txt|max:5120',
            'coa_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('pdf_file')) {
            $oldDoc = DB::table('portal_documents')
                ->where('sale_id', $saleId)
                ->where('file_type', 'pdf')
                ->first();
            if ($oldDoc) {
                Storage::disk('public')->delete($oldDoc->file_path);
            }

            $pdfPath = $request->file('pdf_file')->store('portal_docs', 'public');
            
            DB::table('portal_documents')->updateOrInsert(
                ['sale_id' => $saleId, 'file_type' => 'pdf'],
                [
                    'file_name'  => $request->file('pdf_file')->getClientOriginalName(),
                    'file_path'  => $pdfPath,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        if ($request->hasFile('xml_file')) {
            $oldDoc = DB::table('portal_documents')
                ->where('sale_id', $saleId)
                ->where('file_type', 'xml')
                ->first();
            if ($oldDoc) {
                Storage::disk('public')->delete($oldDoc->file_path);
            }

            $xmlPath = $request->file('xml_file')->store('portal_docs', 'public');
            
            DB::table('portal_documents')->updateOrInsert(
                ['sale_id' => $saleId, 'file_type' => 'xml'],
                [
                    'file_name'  => $request->file('xml_file')->getClientOriginalName(),
                    'file_path'  => $xmlPath,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        if ($request->hasFile('coa_file')) {
            $oldDoc = DB::table('portal_documents')
                ->where('sale_id', $saleId)
                ->where('file_type', 'coa')
                ->first();
            if ($oldDoc) {
                Storage::disk('public')->delete($oldDoc->file_path);
            }

            $coaPath = $request->file('coa_file')->store('portal_docs', 'public');
            
            DB::table('portal_documents')->updateOrInsert(
                ['sale_id' => $saleId, 'file_type' => 'coa'],
                [
                    'file_name'  => $request->file('coa_file')->getClientOriginalName(),
                    'file_path'  => $coaPath,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        return response()->json(['success' => true, 'message' => '¡Documentos actualizados correctamente!']);
    }

    public function getClientSales($portalUserId)
    {
        $user = DB::table('portal_users')->where('id', $portalUserId)->first();

        if (!$user) {
            return response()->json(['sales' => []]);
        }

        $sales = DB::table('sales')
            ->where('customer_id', $user->customer_id)
            ->select('sale_id', 'folio', 'date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($sale) {
                $docs = DB::table('portal_documents')->where('sale_id', $sale->sale_id)->get();
                
                $pdfDoc = $docs->where('file_type', 'pdf')->first();
                $xmlDoc = $docs->where('file_type', 'xml')->first();
                $coaDoc = $docs->where('file_type', 'coa')->first();

                $sale->has_pdf = $pdfDoc ? true : false;
                $sale->pdf_path = $pdfDoc ? asset('storage/' . $pdfDoc->file_path) : null;
                $sale->pdf_name = $pdfDoc ? $pdfDoc->file_name : null;

                $sale->has_xml = $xmlDoc ? true : false;
                $sale->xml_path = $xmlDoc ? asset('storage/' . $xmlDoc->file_path) : null;
                $sale->xml_name = $xmlDoc ? $xmlDoc->file_name : null;

                $sale->has_coa = $coaDoc ? true : false;
                $sale->coa_path = $coaDoc ? asset('storage/' . $coaDoc->file_path) : null;
                $sale->coa_name = $coaDoc ? $coaDoc->file_name : null;
                return $sale;
            });

        return response()->json(['sales' => $sales]);
    }

    public function deleteDoc($saleId, $type)
    {
        if (!in_array($type, ['pdf', 'xml', 'coa'])) {
            return response()->json(['success' => false, 'error' => 'Tipo de archivo no válido.'], 400);
        }

        try {
            $doc = DB::table('portal_documents')
                ->where('sale_id', $saleId)
                ->where('file_type', $type)
                ->first();

            if ($doc) {
                Storage::disk('public')->delete($doc->file_path);

                DB::table('portal_documents')
                    ->where('sale_id', $saleId)
                    ->where('file_type', $type)
                    ->delete();

                return response()->json(['success' => true, 'message' => '¡Archivo eliminado correctamente!']);
            }

            return response()->json(['success' => false, 'error' => 'Archivo no encontrado.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}