<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Prospect\ProspectRepository;
use App\Http\Requests\Prospect\ProspectStoreRequest;
use App\Http\Requests\Prospect\ProspectUpdateRequest;
use App\Http\Resources\Prospect\ProspectResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProspectController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ProspectRepository $prospectRepo;

    public function __construct(UtilResponse $utilResponse, ProspectRepository $prospectRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->prospectRepo = $prospectRepo;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->prospectRepo->getIndexData();

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    $data,
                    'Datos de prospectos obtenidos correctamente'
                );
            }

            return view('prospect', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading prospects index', [
                'action'    => 'ProspectController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar la vista de prospectos.');
        }
    }

    public function getProspects(Request $request): JsonResponse
    {
        try {
            $prospects = $this->prospectRepo->getProspects($request->all());
            return response()->json(['prospects' => $prospects]);
        } catch (\Throwable $e) {
            Log::error('Error in ProspectController@getProspects', [
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al consultar los prospectos.', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $prospect = $this->prospectRepo->find((int) $id);

            if (!$prospect) {
                return $this->utilResponse->errorResponse('Prospecto no encontrado', 404);
            }

            return response()->json([
                'prospect' => $prospect->only([
                    'prospect_id', 'sector_id', 'name', 'phone', 'email',
                    'rfc', 'state', 'city', 'district', 'address',
                ]),
                'data'     => new ProspectResource($prospect),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in ProspectController@show', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al obtener el prospecto.', 500);
        }
    }

    public function store(ProspectStoreRequest $request): JsonResponse
    {
        try {
            $prospect = $this->prospectRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfully made',
                'data'    => new ProspectResource($prospect),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in ProspectController@store', [
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al crear el prospecto.', 500);
        }
    }

    public function update(ProspectUpdateRequest $request, $id = null): JsonResponse
    {
        try {
            $prospectId = (int) ($id ?? $request->input('prospect_id'));
            $updated = $this->prospectRepo->update($prospectId, $request->validated());

            if (!$updated) {
                return $this->utilResponse->errorResponse('Prospecto no encontrado', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfully made',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in ProspectController@update', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al actualizar el prospecto.', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->prospectRepo->delete((int) $id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Prospect not deleted',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Prospect deleted',
            ], 200);
        } catch (DomainException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 409);
        } catch (\Throwable $e) {
            Log::error('Error in ProspectController@destroy', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al eliminar el prospecto.', 500);
        }
    }
}
