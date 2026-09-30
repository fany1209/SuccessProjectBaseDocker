<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TransportLine\TransportLineRepository;
use App\Http\Requests\TransportLine\TransportLineRequest;
use App\Http\Resources\TransportLine\TransportLineResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransportLineController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var TransportLineRepository
     */
    protected TransportLineRepository $transportLineRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param TransportLineRepository $transportLineRepo
     */
    public function __construct(UtilResponse $utilResponse, TransportLineRepository $transportLineRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->transportLineRepo = $transportLineRepo;
    }

    /**
     * Listado general de líneas de transporte para DataTables o API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->getTransportLines($request);
    }

    /**
     * Endpoint compatible con DataTables en la vista logística.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTransportLines(Request $request): JsonResponse
    {
        try {
            $lines = $this->transportLineRepo->all($request->only(['search']));
            $collection = TransportLineResource::collection($lines);

            return response()->json([
                'success'         => true,
                'flag'            => true,
                'code'            => 200,
                'message'         => 'Líneas de transporte obtenidas correctamente.',
                'data'            => $collection,
                'transport_lines' => $collection,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al listar líneas de transporte en TransportLineController@getTransportLines', [
                'action'    => 'TransportLineController@getTransportLines',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar las líneas de transporte.', 500);
        }
    }

    /**
     * Obtiene una línea de transporte por su ID para su edición modal.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $line = $this->transportLineRepo->find($id);

            if (!$line) {
                return $this->utilResponse->errorResponse('Línea de transporte no encontrada.', 404);
            }

            $resource = (new TransportLineResource($line))->resolve();

            return response()->json([
                'success'        => true,
                'flag'           => true,
                'code'           => 200,
                'message'        => 'Línea de transporte obtenida correctamente.',
                'data'           => $resource,
                'transport_line' => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar línea de transporte en TransportLineController@show', [
                'action'    => 'TransportLineController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos de la línea de transporte.', 500);
        }
    }

    /**
     * Registra una nueva línea de transporte en transacción ACID.
     *
     * @param TransportLineRequest $request
     * @return JsonResponse
     */
    public function store(TransportLineRequest $request): JsonResponse
    {
        try {
            $line = $this->transportLineRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfuly make it',
                'data'    => new TransportLineResource($line),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar línea de transporte en TransportLineController@store', [
                'action'    => 'TransportLineController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar la línea de transporte.', 500);
        }
    }

    /**
     * Actualiza una línea de transporte con bloqueo pesimista.
     *
     * @param TransportLineRequest $request
     * @param int|string|null $id
     * @return JsonResponse
     */
    public function update(TransportLineRequest $request, $id = null): JsonResponse
    {
        try {
            $lineId = $id ?? $request->input('transport_line_id');

            if (!$lineId) {
                return $this->utilResponse->errorResponse('ID de línea de transporte no proporcionado.', 400);
            }

            $line = $this->transportLineRepo->update($lineId, $request->validated());

            if (!$line) {
                return $this->utilResponse->errorResponse('Línea de transporte no encontrada.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfuly make it',
                'data'    => new TransportLineResource($line),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar línea de transporte en TransportLineController@update', [
                'action'    => 'TransportLineController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar la línea de transporte.', 500);
        }
    }

    /**
     * Elimina una línea de transporte con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->transportLineRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Transport line not deleted',
                    'data'    => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Transport line deleted',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar línea de transporte en TransportLineController@destroy', [
                'action'    => 'TransportLineController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar la línea de transporte.', 500);
        }
    }
}
