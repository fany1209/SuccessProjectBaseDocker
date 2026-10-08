<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PortalUser\PortalUserRepository;
use App\Http\Requests\PortalUser\PortalUserStoreRequest;
use App\Http\Requests\PortalUser\PortalUserUpdateRequest;
use App\Http\Requests\PortalUser\PortalUserUploadDocsRequest;
use App\Http\Resources\PortalUser\PortalUserResource;
use App\Http\Resources\PortalUser\PortalUserSaleResource;
use App\Traits\UtilResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PortalUserController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PortalUserRepository $portalUserRepo;

    public function __construct(UtilResponse $utilResponse, PortalUserRepository $portalUserRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->portalUserRepo = $portalUserRepo;
    }

    public function index(): View
    {
        $clientesSistemas = $this->portalUserRepo->getSystemCustomers();

        return view('portal-users', compact('clientesSistemas'));
    }

    public function getPortalUsers(Request $request): JsonResponse
    {
        try {
            $users = $this->portalUserRepo->getPortalUsers($request->query('name'));
            $resolved = PortalUserResource::collection($users)->resolve();

            return response()->json(['users' => $resolved]);
        } catch (Throwable $e) {
            Log::error('Error al obtener usuarios del portal', [
                'action'  => 'PortalUserController@getPortalUsers',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['users' => []], 500);
        }
    }

    public function store(PortalUserStoreRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $user = $this->portalUserRepo->store($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 201,
                    'message' => '¡El usuario para el portal de clientes se ha creado con éxito!',
                    'data'    => new PortalUserResource($user),
                ], 201);
            }

            return back()->with('success', '¡El usuario para el portal de clientes se ha creado con éxito!');
        } catch (Throwable $e) {
            Log::error('Error al registrar usuario del portal', [
                'action'  => 'PortalUserController@store',
                'payload' => $request->safe()->except(['password', 'password_confirmation']),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Hubo un problema al guardar el usuario.',
                    'error'   => 'Error interno al guardar usuario.',
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'Hubo un problema al guardar el usuario.']);
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            $user = $this->portalUserRepo->findById((int) $id);

            return response()->json(['user' => new PortalUserResource($user)]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        } catch (Throwable $e) {
            Log::error('Error al consultar usuario del portal', [
                'action'  => 'PortalUserController@edit',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al consultar el usuario.'], 500);
        }
    }

    public function update(PortalUserUpdateRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->portalUserRepo->update((int) $id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => '¡Usuario actualizado con éxito!',
                'data'    => new PortalUserResource($user),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Usuario no encontrado',
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al actualizar usuario del portal', [
                'action'  => 'PortalUserController@update',
                'id'      => $id,
                'payload' => $request->safe()->except(['password', 'password_confirmation']),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Hubo un problema al actualizar el usuario.',
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->portalUserRepo->delete((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado con éxito.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Usuario no encontrado',
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al eliminar usuario del portal', [
                'action'  => 'PortalUserController@destroy',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Hubo un problema al eliminar el usuario.',
            ], 500);
        }
    }

    public function uploadDocs(PortalUserUploadDocsRequest $request, $saleId): JsonResponse
    {
        try {
            $this->portalUserRepo->uploadDocs(
                (int) $saleId,
                $request->file('pdf_file'),
                $request->file('xml_file'),
                $request->file('coa_file')
            );

            return response()->json([
                'success' => true,
                'message' => '¡Documentos actualizados correctamente!',
            ]);
        } catch (Throwable $e) {
            Log::error('Error al subir documentos de venta en portal', [
                'action'  => 'PortalUserController@uploadDocs',
                'sale_id' => $saleId,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Error interno al procesar los documentos.',
            ], 500);
        }
    }

    public function getClientSales($portalUserId): JsonResponse
    {
        try {
            $sales = $this->portalUserRepo->getClientSales((int) $portalUserId);
            $resolved = PortalUserSaleResource::collection($sales)->resolve();

            return response()->json(['sales' => $resolved]);
        } catch (Throwable $e) {
            Log::error('Error al consultar ventas del cliente en portal', [
                'action'         => 'PortalUserController@getClientSales',
                'portal_user_id' => $portalUserId,
                'user_id'        => auth()->id(),
                'error'          => $e->getMessage(),
            ]);

            return response()->json(['sales' => []], 500);
        }
    }

    public function deleteDoc($saleId, $type): JsonResponse
    {
        if (!in_array($type, ['pdf', 'xml', 'coa'], true)) {
            return response()->json([
                'success' => false,
                'error'   => 'Tipo de archivo no válido.',
            ], 400);
        }

        try {
            $deleted = $this->portalUserRepo->deleteDoc((int) $saleId, $type);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Archivo eliminado correctamente!',
                ]);
            }

            return response()->json([
                'success' => false,
                'error'   => 'Archivo no encontrado.',
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al eliminar documento de venta en portal', [
                'action'  => 'PortalUserController@deleteDoc',
                'sale_id' => $saleId,
                'type'    => $type,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Hubo un problema al eliminar el archivo.',
            ], 500);
        }
    }
}