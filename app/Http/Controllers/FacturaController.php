<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Factura\FacturaRepository;
use App\Http\Requests\Factura\FacturaRequest;
use App\Http\Resources\Factura\FacturaResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacturaController extends Controller
{
    protected UtilResponse $utilResponse;
    protected FacturaRepository $facturaRepo;

    public function __construct(UtilResponse $utilResponse, FacturaRepository $facturaRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->facturaRepo = $facturaRepo;
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax() || $request->expectsJson()) {
                return $this->datatable();
            }

            return view('finance.facturas.index');
        } catch (Throwable $e) {
            Log::error('Error al cargar vista de facturas', [
                'action'  => 'FacturaController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al consultar las facturas.',
                    'error'   => 'Error interno al consultar las facturas.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar la vista de facturas.');
        }
    }

    public function datatable(): JsonResponse
    {
        try {
            $facturas = $this->facturaRepo->getDatatableData();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Facturas obtenidas correctamente',
                'data'    => $facturas,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al obtener datos para datatable de facturas', [
                'action'  => 'FacturaController@datatable',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Error al cargar el listado de facturas.',
                'error'   => 'Error interno al consultar las facturas.',
                'data'    => [],
            ], 500);
        }
    }

    public function store(FacturaRequest $request): JsonResponse
    {
        try {
            $factura = $this->facturaRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Documento guardado correctamente',
                'data'    => new FacturaResource($factura),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al guardar documento en FacturaController@store', [
                'action'  => 'FacturaController@store',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error al guardar el documento.',
                'error'   => 'Error interno al guardar el documento.',
                'data'    => null,
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 400,
                'message' => 'Identificador de factura inválido',
                'error'   => 'Identificador de factura inválido',
                'data'    => null,
            ], 400);
        }

        try {
            $factura = $this->facturaRepo->find((int) $id);

            if (!$factura) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Factura no encontrada',
                    'error'   => 'Factura no encontrada',
                    'data'    => null,
                ], 404);
            }

            $detallesData = $factura->detalles->map(function ($d) {
                return [
                    'producto'                 => $d->producto,
                    'clave_sat'                => $d->clave_sat,
                    'unidad'                   => $d->unidad,
                    'cantidad'                 => $d->cantidad,
                    'precio_unitario'          => $d->precio_unitario,
                    'precio'                   => $d->precio,
                    'descuento'                => $d->descuento,
                    'iva_porcentaje'           => $d->iva_porcentaje,
                    'otro_impuesto'            => $d->otro_impuesto_porcentaje,
                    'otro_impuesto_porcentaje' => $d->otro_impuesto_porcentaje,
                    'traslado'                 => $d->traslado,
                    'ilc'                      => $d->ilc,
                    'retencion'                => $d->retencion,
                    'isr'                      => $d->isr,
                ];
            });

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'code'     => 200,
                'message'  => 'Factura obtenida correctamente',
                'factura'  => $factura,
                'detalles' => $detallesData,
                'data'     => new FacturaResource($factura),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar factura en FacturaController@show', [
                'action'  => 'FacturaController@show',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Error al obtener la factura',
                'error'   => 'Error interno al consultar la factura.',
                'data'    => null,
            ], 500);
        }
    }

    public function update(FacturaRequest $request, $id): JsonResponse
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 400,
                'message' => 'Identificador de factura inválido',
                'error'   => 'Identificador de factura inválido',
                'data'    => null,
            ], 400);
        }

        try {
            $factura = $this->facturaRepo->update((int) $id, $request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Documento actualizado correctamente',
                'data'    => new FacturaResource($factura),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 404,
                'message' => 'Factura no encontrada',
                'error'   => 'Factura no encontrada',
                'data'    => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al actualizar factura en FacturaController@update', [
                'action'  => 'FacturaController@update',
                'id'      => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error al actualizar el documento.',
                'error'   => 'Error interno al actualizar el documento.',
                'data'    => null,
            ], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 400,
                'message' => 'Identificador de factura inválido.',
                'error'   => 'Identificador de factura inválido.',
                'data'    => [],
            ], 400);
        }

        try {
            $factura = $this->facturaRepo->find((int) $id);
            if (!$factura) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Factura no encontrada.',
                    'error'   => 'Factura no encontrada.',
                    'data'    => [],
                ], 404);
            }

            $force = $request->boolean('force');
            $this->facturaRepo->delete((int) $id, $force);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Documento eliminado correctamente.',
                'data'    => [],
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success'      => false,
                'flag'         => false,
                'code'         => 422,
                'has_payments' => true,
                'message'      => $e->getMessage(),
                'error'        => $e->getMessage(),
                'data'         => [],
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 404,
                'message' => 'Factura no encontrada.',
                'error'   => 'Factura no encontrada.',
                'data'    => [],
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al eliminar factura en FacturaController@destroy', [
                'action'  => 'FacturaController@destroy',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error al eliminar el documento.',
                'error'   => 'Error interno al eliminar el documento.',
                'data'    => [],
            ], 500);
        }
    }
}