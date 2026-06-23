<?php
/*
Customers
Controlador Customers
Fecha de creación: xx-xx-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 08-01-2026
*/
namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $sectors = Sector::all();

        $total_customers = DB::table('customers')
            ->join('sectors', 'sectors.sector_id', '=', 'customers.sector_id')
            ->count();

        $customers_per_sector = DB::table('sectors')
            ->leftJoin('customers', 'customers.sector_id', '=', 'sectors.sector_id')
            ->select('sectors.sector_id', 'sectors.code')
            ->selectRaw("
                COALESCE(
                    MAX(
                        CAST(
                            SUBSTRING(customers.customer_code, 3 + CHAR_LENGTH(sectors.code)) AS UNSIGNED
                        )
                    ),
                0) AS last_number
            ")
            ->groupBy('sectors.sector_id', 'sectors.code')
            ->get();

        $sellers = User::select('users.id as seller_number', 'users.name')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', ['Sales', 'Admin'])
            ->get();

        return view('customers', compact('sectors', 'total_customers', 'customers_per_sector', 'sellers'));
    }

    public function getCustomers(Request $request)
    {
        $sector = $request->input('sector');
        $search = $request->input('search');
        $city = $request->input('city');
        $state = $request->input('state');
        $vendedor = $request->input('vendedor');
        $name = $request->input('name');
        $rfc = $request->input('rfc');
        $contact = $request->input('contact');

        $query = DB::table('customers')
            ->join('sectors', 'sectors.sector_id', '=', 'customers.sector_id')
            ->select(
                'customers.customer_id',
                'sectors.name as sector',
                'customers.customer_code as code',
                'customers.name',
                'customers.phone',
                'customers.email',
                'customers.rfc',
                'customers.vendedor',
                'customers.contact',          
                'customers.delivery_address', 
                DB::raw("concat(customers.address,', ',customers.district,', ',customers.city,', ',customers.state,', ',customers.country,' ',customers.postal_code) as address")
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sectors.name', 'like', '%' . $search . '%')
                    ->orWhere('customers.customer_code', 'like', '%' . $search . '%')
                    ->orWhere('customers.name', 'like', '%' . $search . '%')
                    ->orWhere('customers.phone', 'like', '%' . $search . '%')
                    ->orWhere('customers.email', 'like', '%' . $search . '%')
                    ->orWhere('customers.rfc', 'like', '%' . $search . '%')
                    ->orWhere('customers.vendedor', 'like', '%' . $search . '%')
                    ->orWhere('customers.contact', 'like', '%' . $search . '%')          
                    ->orWhere('customers.delivery_address', 'like', '%' . $search . '%'); 
            });
        }

        if (!empty($sector)) {
            $query->where('customers.sector_id', $sector);
        }
        if (!empty($city)) {
            $query->where('customers.city', 'like', '%' . $city . '%');
        }
        if (!empty($state)) {
            $query->where('customers.state', 'like', '%' . $state . '%');
        }
        if (!empty($vendedor)) {
            $query->where('customers.vendedor', 'like', '%' . $vendedor . '%');
        }
        if (!empty($name)) {
            $query->where('customers.name', 'like', '%' . $name . '%');
        }
        if (!empty($rfc)) {
            $query->where('customers.rfc', 'like', '%' . $rfc . '%');
        }
        if (!empty($contact)) {
            $query->where('customers.contact', 'like', '%' . $contact . '%');
        }
        
        $query->orderBy('customers.customer_id', 'desc');

        $customers = $query->get();

        $customersWithPermissions = $customers->map(function ($row) {
            return [
                'customer_id'      => $row->customer_id,
                'sector'           => $row->sector,
                'code'             => $row->code,
                'name'             => $row->name,
                'phone'            => $row->phone,
                'email'            => $row->email,
                'rfc'              => $row->rfc,
                'vendedor'         => $row->vendedor,
                'contact'          => $row->contact,          
                'delivery_address' => $row->delivery_address, 
                'address'          => $row->address,
                'canUpdate'        => auth()->user()->can('customers.update'),
                'canDelete'        => auth()->user()->can('customers.delete'),
            ];
        });

        return response()->json(['customers' => $customersWithPermissions]);
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $customer = Customer::find($id);

                if ($customer) {
                    $customer->delete();
                    return response()->json(['success' => true, 'message' => 'Customer deleted']);
                }

                return response()->json(['success' => false, 'message' => 'Customer not deleted'], 404);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        }
    }

    public function show($id)
    {
        $customer = Customer::where('customer_id', $id)->first();

        return response()->json([
            'customer' => $customer->only([
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
                'contact',          // Agregado
                'delivery_address', // Agregado
            ])
        ]);
    }

    public function update(StoreCustomerRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $customer = Customer::findOrFail($request->customer_id);

                $data = $request->only([
                    'sector_id', 'name', 'phone', 'email', 'rfc',
                    'postal_code', 'state', 'city', 'district', 'address', 'country',
                    'vendedor', 'contact', 'delivery_address'
                ]);

                if ($customer->sector_id != $request->sector_id) {
                    
                    $sector = Sector::where('sector_id', $request->sector_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (empty($sector->code)) {
                        return response()->json(['error' => 'El nuevo sector no tiene un código (code) asignado.'], 422);
                    }

                    $prefix = 'SC' . $sector->code;
                    $prefixLen = strlen($prefix);

                    $last = Customer::where('sector_id', $sector->sector_id)
                        ->where('customer_code', 'like', $prefix . '%')
                        ->selectRaw("MAX(CAST(SUBSTRING(customer_code, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
                        ->value('max_num');

                    $next = ((int) $last) + 1;
                    
                    $data['customer_code'] = $prefix . $next;
                }

                $customer->update($data);

                return response()->json([
                    'message' => 'Customer updated successfully',
                    'new_code' => $data['customer_code'] ?? $customer->customer_code
                ], 200); 
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $data = $request->validated();
                $data['vendedor'] = auth()->user()->name ?? 'Sistema';
                $sector = Sector::where('sector_id', $data['sector_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (empty($sector->code)) {
                    return response()->json([
                        'error' => 'El sector seleccionado no tiene código (code).'
                    ], 422);
                }

                $prefix = 'SC' . $sector->code;
                $prefixLen = strlen($prefix);

                $last = Customer::where('sector_id', $sector->sector_id)
                    ->where('customer_code', 'like', $prefix . '%')
                    ->selectRaw("MAX(CAST(SUBSTRING(customer_code, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
                    ->value('max_num');

                $next = ((int) $last) + 1;
                $data['customer_code'] = $prefix . $next;

                Customer::create($data);

                return response()->json([
                    'message' => 'Customer created successfully',
                    'customer_code' => $data['customer_code']
                ], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }
}
