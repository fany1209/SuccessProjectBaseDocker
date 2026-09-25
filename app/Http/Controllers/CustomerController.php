<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Customer\CustomerRepository;
use App\Http\Requests\Customer\CustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerController extends Controller
{
    protected UtilResponse $utilResponse;
    protected CustomerRepository $customerRepo;

    public function __construct(UtilResponse $utilResponse, CustomerRepository $customerRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->customerRepo = $customerRepo;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->customerRepo->getIndexData();

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    $data,
                    'Datos de clientes obtenidos correctamente'
                );
            }

            return view('customers', $data);
        } catch (Throwable $e) {
            Log::error('Error al cargar índice de clientes', [
                'action' => 'CustomerController@index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar clientes', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar los clientes.');
        }
    }

    public function getCustomers(Request $request): JsonResponse
    {
        try {
            $customers = $this->customerRepo->getFilteredCustomers($request->all());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Clientes obtenidos correctamente',
                'data' => $customers,
                'customers' => $customers,
            ]);
        } catch (Throwable $e) {
            Log::error('Error al filtrar clientes', [
                'action' => 'CustomerController@getCustomers',
                'user_id' => auth()->id(),
                'filters' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al cargar el listado de clientes',
                'error' => 'Error al cargar el listado de clientes',
                'data' => [],
                'customers' => [],
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $customer = $this->customerRepo->find((int) $id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'Cliente no encontrado',
                    'error' => 'Cliente no encontrado',
                    'data' => null,
                ], 404);
            }

            $rawCustomerData = $customer->only([
                'customer_id',
                'customer_code',
                'sector_id',
                'name',
                'phone',
                'email',
                'rfc',
                'postal_code',
                'state',
                'city',
                'district',
                'address',
                'country',
                'vendedor',
                'contact',
                'delivery_address',
            ]);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Cliente obtenido correctamente',
                'data' => new CustomerResource($customer),
                'customer' => $rawCustomerData,
            ]);
        } catch (Throwable $e) {
            Log::error('Error al consultar cliente', [
                'action' => 'CustomerController@show',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al consultar el cliente',
                'error' => 'Error al consultar el cliente',
                'data' => null,
            ], 500);
        }
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        try {
            $userName = auth()->user()?->name;
            $customer = $this->customerRepo->create($request->validated(), $userName);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 201,
                'message' => 'Customer created successfully',
                'customer_code' => $customer->customer_code,
                'data' => new CustomerResource($customer),
            ], 201);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 422,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => null,
            ], 422);
        } catch (Throwable $e) {
            Log::error('Error al registrar cliente', [
                'action' => 'CustomerController@store',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error inesperado: ' . $e->getMessage(),
                'error' => 'Error inesperado: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function update(CustomerRequest $request, $id = null): JsonResponse
    {
        try {
            $targetId = $id ?? $request->customer_id ?? $request->route('customer');
            $customer = $this->customerRepo->update((int) $targetId, $request->validated());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Customer updated successfully',
                'new_code' => $customer->customer_code,
                'customer_code' => $customer->customer_code,
                'data' => new CustomerResource($customer),
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 422,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => null,
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'Cliente no encontrado',
                'error' => 'Cliente no encontrado',
                'data' => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al actualizar cliente', [
                'action' => 'CustomerController@update',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'error' => 'Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->customerRepo->delete((int) $id);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Customer deleted',
                'data' => [],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'Customer not deleted',
                'error' => 'Cliente no encontrado',
                'data' => [],
            ], 404);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 422,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => [],
            ], 422);
        } catch (Throwable $e) {
            Log::error('Error al eliminar cliente', [
                'action' => 'CustomerController@destroy',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al eliminar cliente',
                'error' => 'Error inesperado al eliminar cliente',
                'data' => [],
            ], 500);
        }
    }
}
