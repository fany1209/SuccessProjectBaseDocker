<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Output\OutputRepository;
use App\Http\Requests\Output\OutputRequest;
use App\Http\Resources\Output\OutputResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class OutputController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var OutputRepository
     */
    protected OutputRepository $outputRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param OutputRepository $outputRepo
     */
    public function __construct(UtilResponse $utilResponse, OutputRepository $outputRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->outputRepo = $outputRepo;
    }

    /**
     * Listado general de salidas de almacén.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $outputs = $this->outputRepo->all($request->only(['customer_id', 'search']));
            $collection = OutputResource::collection($outputs);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Salidas de almacén obtenidas correctamente.',
                'data'    => $collection,
                'outputs' => $collection,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al listar salidas en OutputController@index', [
                'action'    => 'OutputController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar las salidas de almacén.', 500);
        }
    }

    /**
     * Obtiene el detalle de una salida y sus productos para edición modal.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $output = $this->outputRepo->find($id);

            if (!$output) {
                return $this->utilResponse->errorResponse('Salida de almacén no encontrada.', 404);
            }

            $products = $this->outputRepo->getProductsForOutput($id);
            $outputResource = (new OutputResource($output))->resolve();

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'code'     => 200,
                'message'  => 'Salida de almacén obtenida correctamente.',
                'output'   => $outputResource,
                'products' => $products,
                'data'     => [
                    'output'   => $outputResource,
                    'products' => $products,
                ],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar salida en OutputController@show', [
                'action'    => 'OutputController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos de la salida.', 500);
        }
    }

    /**
     * Registra una nueva salida de almacén y sus partidas de productos en transacción ACID.
     *
     * @param OutputRequest $request
     * @return JsonResponse
     */
    public function store(OutputRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $headerData = $this->extractHeaderData($validated);
            $items = $this->extractItemsData($validated);

            $output = $this->outputRepo->create($headerData, $items);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfuly make it',
                'data'    => new OutputResource($output),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar salida de almacén en OutputController@store', [
                'action'    => 'OutputController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar la salida de almacén.', 500);
        }
    }

    /**
     * Actualiza una salida de almacén y sus productos asociados con bloqueo pesimista.
     *
     * @param OutputRequest $request
     * @param int|string|null $id
     * @return JsonResponse
     */
    public function update(OutputRequest $request, $id = null): JsonResponse
    {
        try {
            $outputId = $id ?? $request->input('id') ?? $request->input('output_id');

            if (!$outputId) {
                return $this->utilResponse->errorResponse('ID de salida no proporcionado.', 400);
            }

            $validated = $request->validated();
            $headerData = $this->extractHeaderData($validated);
            $items = $this->extractItemsData($validated);

            $output = $this->outputRepo->update($outputId, $headerData, $items);

            if (!$output) {
                return $this->utilResponse->errorResponse('Salida de almacén no encontrada.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfuly make it',
                'data'    => new OutputResource($output),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar salida en OutputController@update', [
                'action'    => 'OutputController@update',
                'id'        => $id ?? $request->input('id'),
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar la salida de almacén.', 500);
        }
    }

    /**
     * Elimina una salida de almacén y sus partidas con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->outputRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Output not found',
                    'data'    => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Output deleted successfully',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar salida en OutputController@destroy', [
                'action'    => 'OutputController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar la salida de almacén.', 500);
        }
    }

    /**
     * Extrae los campos de cabecera de la salida.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function extractHeaderData(array $data): array
    {
        return [
            'operator'             => $data['operator'],
            'license_number'       => $data['license_number'],
            'security_seal'        => $data['security_seal'],
            'security_seal_number' => $data['security_seal_number'] ?? null,
            'unit_plates'          => $data['unit_plates'],
            'trailer_plates'       => $data['trailer_plates'] ?? null,
            'comments'             => $data['comments'] ?? null,
            'customer_id'          => $data['customer_id'],
            'transport_line_id'    => $data['transport_line_id'],
            'vendedor'             => $data['vendedor'],
        ];
    }

    /**
     * Extrae y mapea las partidas de productos asociadas.
     *
     * @param array<string, mixed> $data
     * @return array<int, array<string, mixed>>
     */
    protected function extractItemsData(array $data): array
    {
        $items = [];
        $productIds = $data['product_id'] ?? [];
        $quantities = $data['quantity'] ?? [];
        $batches = $data['label_batch'] ?? [];

        foreach ($productIds as $index => $prodId) {
            $items[] = [
                'product_id'  => (int) $prodId,
                'quantity'    => (float) ($quantities[$index] ?? 0.0),
                'label_batch' => $batches[$index] ?? null,
            ];
        }

        return $items;
    }
}
