<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Reception\ReceptionRepository;
use App\Http\Requests\Reception\StoreReceptionRequest;
use App\Http\Requests\Reception\UpdateReceptionRequest;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReceptionController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ReceptionRepository $receptionRepo;

    public function __construct(UtilResponse $utilResponse, ReceptionRepository $receptionRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->receptionRepo = $receptionRepo;
    }

    /**
     * Retorna el listado de recepciones de muestras para DataTables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $data = $this->receptionRepo->datatable();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'data'    => $data,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error al consultar datatable de recepción de muestras', [
                'action'    => 'ReceptionController@datatable',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cargar recepciones de muestras', 500);
        }
    }

    /**
     * Retorna los lotes de inventario asociados al producto especificado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBatches(Request $request): JsonResponse
    {
        try {
            $productId = $request->input('product_id') ? (int) $request->input('product_id') : null;
            $batches = $this->receptionRepo->getBatchesForProduct($productId);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'batches' => $batches,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar lotes para recepción de muestras', [
                'action'     => 'ReceptionController@getBatches',
                'product_id' => $request->input('product_id'),
                'exception'  => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar lotes del producto', 500);
        }
    }

    /**
     * Retorna los detalles de una recepción de muestra para su visualización o edición.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $recepcion = $this->receptionRepo->getShowData((int) $id);

            return response()->json([
                'success'   => true,
                'flag'      => true,
                'recepcion' => $recepcion,
                'data'      => $recepcion,
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Recepción de muestra no encontrada', 404);
        } catch (\Throwable $e) {
            Log::error('Error al mostrar detalle de recepción de muestra', [
                'action'    => 'ReceptionController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos de la recepción', 500);
        }
    }

    /**
     * Almacena una nueva recepción de muestra resolviendo catálogos dinámicamente si es necesario.
     *
     * @param StoreReceptionRequest $request
     * @return JsonResponse
     */
    public function store(StoreReceptionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $rawProduct = $request->input('producto');
            $rawSupplier = $request->input('proveedor');

            $record = $this->receptionRepo->store($validated, $rawProduct, $rawSupplier);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Guardado correctamente',
                'id'      => $record->id,
                'data'    => $record,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'message' => $e->getMessage(),
                'errors'  => ['producto' => [$e->getMessage()]],
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error al guardar recepción de muestra', [
                'action'    => 'ReceptionController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al guardar la recepción de muestra', 500);
        }
    }

    /**
     * Actualiza la información general de la recepción de muestra.
     *
     * @param UpdateReceptionRequest $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function update(UpdateReceptionRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $record = $this->receptionRepo->update((int) $id, $validated);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Cambios guardados correctamente',
                'data'    => $record,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Recepción de muestra no encontrada', 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar recepción de muestra', [
                'action'    => 'ReceptionController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar la recepción', 500);
        }
    }

    /**
     * Actualiza los datos de calidad y concluye la inspección de la muestra (estatus = 1).
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function updateq(Request $request, $id): JsonResponse
    {
        try {
            $record = $this->receptionRepo->updateQuality((int) $id, $request->all());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Calidad actualizada correctamente',
                'data'    => $record,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Recepción de muestra no encontrada', 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar calidad en recepción de muestra', [
                'action'    => 'ReceptionController@updateq',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar la calidad', 500);
        }
    }

    /**
     * Genera y descarga el PDF correspondiente a la recepción de muestra (Formato 01).
     *
     * @param int|string $id
     * @return mixed
     */
    public function pdfShow($id)
    {
        try {
            $pdfInfo = $this->receptionRepo->getPdfData((int) $id);

            $pdf = Pdf::loadView('formats.laboratory.01', $pdfInfo['data'])->setPaper('letter');

            return $pdf->download('RecepcionMuestras_' . $pdfInfo['folio_muestra'] . '.pdf');
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Recepción de muestra no encontrada');
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de recepción de muestra', [
                'action'    => 'ReceptionController@pdfShow',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al generar el PDF', 500);
        }
    }

    /**
     * Elimina el registro de recepción de muestra.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->receptionRepo->delete((int) $id);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'message' => 'Eliminado correctamente.',
                ], 200);
            }

            return $this->utilResponse->errorResponse('Recepción de muestra no encontrada para eliminar.', 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar recepción de muestra', [
                'action'    => 'ReceptionController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error en el servidor al eliminar.', 500);
        }
    }

    /**
     * Consulta muestras con calidad pendiente para usuarios con rol 'Quality'.
     *
     * @return JsonResponse
     */
    public function checkPendingQuality(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('Quality')) {
                return response()->json(['pending' => []]);
            }

            $pending = $this->receptionRepo->getPendingQuality();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'pending' => $pending,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al verificar muestras con calidad pendiente', [
                'action'    => 'ReceptionController@checkPendingQuality',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['pending' => []]);
        }
    }
}