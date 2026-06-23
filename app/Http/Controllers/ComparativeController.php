<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class ComparativeController extends Controller
{
    public function index()
    {
        $insumos_raw = DB::table('comparative')->orderBy('id', 'desc')->get();
        $grupos = $insumos_raw->groupBy('folio');
        $your_requisitions = DB::table('purchases_requisitions')
            ->where('applicant', auth()->user()->name)
            ->count();

        $requisitions_count = DB::table('purchases_requisitions')->count();
        
        $requisitions_no_check = DB::table('purchases_requisitions')
            ->whereNull('consecutive')
            ->count();

        return view('purchases.index', compact(
            'grupos', 
            'your_requisitions', 
            'requisitions_count', 
            'requisitions_no_check'
        ));
    }

    public function store(Request $request)
    {
        // LOG 1: Ver qué estamos recibiendo exactamente del frontend
        Log::info('--- INICIANDO GUARDADO DE COMPARATIVA ---');
        Log::info('Payload recibido:', $request->all());

        $fecha = now()->format('d-m-Y'); 
        $prefijo = "COMP-" . $fecha;

        $ultimoRegistro = DB::table('comparative')
            ->where('folio', 'like', $prefijo . '%')
            ->distinct()
            ->count('folio');

        $consecutivo = str_pad($ultimoRegistro + 1, 3, '0', STR_PAD_LEFT);
        $folio = $prefijo . "-" . $consecutivo;

        $userId = auth()->id();

        try {
            DB::beginTransaction();

            // Verificamos que 'insumo' sea un arreglo para que el foreach no falle
            if (!is_array($request->insumo)) {
                throw new \Exception("El campo insumos no tiene el formato correcto.");
            }

            foreach ($request->insumo as $key => $value) {
                // LOG 2: Imprimir la llave actual que se está iterando (0, 1, 2, 3...)
                Log::info("Preparando inserción para el item [{$key}]:", ['insumo_value' => $value]);

                // Armamos el arreglo antes de insertarlo para poder loguearlo y le 
                // agregamos fallbacks (?? null) a cantidad, proveedor, precio_total e imagen
                // para evitar el error "Undefined array key"
                $dataInsert = [
                    'folio'            => $folio, 
                    'user_id'          => $userId,
                    'insumo'           => $request->insumo[$key] ?? null,
                    'cantidad'         => $request->cantidad[$key] ?? null,
                    'proveedor'        => $request->proveedor[$key] ?? null,
                    'precio_unt'       => $request->precio_unt[$key] ?? 0,
                    'precio_total'     => $request->precio_total[$key] ?? 0,
                    'imagen'           => $request->imagen[$key] ?? null,
                    'descripcion'      => $request->descripcion[$key] ?? null,
                    'comentarios'      => $request->comentarios[$key] ?? null,
                    'entrega_estimada' => $request->entrega_estimada[$key] ?? null,
                    'link'             => $request->link[$key] ?? null,
                ];

                // LOG 3: Ver los datos exactos que se van a insertar en la BD
                Log::info("Datos listos para BD del item [{$key}]:", $dataInsert);

                DB::table('comparative')->insert($dataInsert);
            }

            DB::commit();
            Log::info("Comparativa {$folio} guardada EXITOSAMENTE.");
            return redirect()->back()->with('success', "Comparativa guardada con Folio: $folio");

        } catch (\Exception $e) {
            DB::rollBack();
            
            // LOG 4: Imprimir el error exacto con el archivo y la línea donde ocurrió
            Log::error("ERROR CRÍTICO AL GUARDAR COMPARATIVA: " . $e->getMessage());
            Log::error("Línea del error: " . $e->getLine() . " en el archivo: " . $e->getFile());
            
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroyByFolio($folio)
    {
        try {
            $deleted = DB::table('comparative')->where('folio', $folio)->delete();

            if ($deleted) {
                return response()->json(['success' => true], 200);
            }

            return response()->json([
                'success' => false, 
                'message' => 'El folio no existe.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function generatePDF($folio)
    {
        $productos = DB::table('comparative')
            ->where('folio', $folio)
            ->get();

        if ($productos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron datos.');
        }

        $pdf = Pdf::loadView('formats.purchases.03', compact('productos', 'folio'));
        
        $pdf->getDomPDF()->set_option("isRemoteEnabled", true);
        $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);

        return $pdf->setPaper('letter', 'landscape')->stream("Comparativa_{$folio}.pdf");
    }

    public function updateAll(Request $request)
    {
        if (!$request->has('insumo') || empty($request->insumo)) {
            return redirect()->back()->with('error', 'No se recibieron datos para actualizar.');
        }

        try {
            DB::beginTransaction();
            $idsRecibidos = array_filter($request->id ?? []);
            $folio = null;
            if (count($idsRecibidos) > 0) {
                $primerItem = DB::table('comparative')->where('id', reset($idsRecibidos))->first();
                $folio = $primerItem ? $primerItem->folio : null;
            }
            if ($folio) {
                DB::table('comparative')
                    ->where('folio', $folio)
                    ->whereNotIn('id', $idsRecibidos)
                    ->delete();
            }

            $userId = auth()->id();

            foreach ($request->insumo as $key => $insumoValor) {
                
                $id = $request->id[$key] ?? null;
                
                $cantidad = (isset($request->cantidad[$key]) && $request->cantidad[$key] > 0) ? $request->cantidad[$key] : 1;
                $precio_total = $request->precio_total[$key] ?? 0;
                $precio_unitario = $precio_total / $cantidad;

                $data = [
                    'insumo'           => $request->insumo[$key] ?? null,
                    'cantidad'         => $cantidad,
                    'proveedor'        => $request->proveedor[$key] ?? null,
                    'precio_total'     => $precio_total,
                    'precio_unt'       => $precio_unitario,
                    'imagen'           => $request->imagen[$key] ?? null,
                    'descripcion'      => $request->descripcion[$key] ?? null,
                    'comentarios'      => $request->comentarios[$key] ?? null,
                    'entrega_estimada' => $request->entrega_estimada[$key] ?? null,
                    'link'             => $request->link[$key] ?? null,
                ];

                if (!empty($id)) {
                    DB::table('comparative')->where('id', $id)->update($data);
                } 
                else {
                    if ($folio) { 
                        $data['folio']   = $folio;
                        $data['user_id'] = $userId;
                        DB::table('comparative')->insert($data);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', '¡La comparativa ha sido actualizada correctamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Hubo un problema al guardar los cambios: ' . $e->getMessage());
        }
    }
}