<?php
/*
suppliers
28/07/25
stefany 
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
*/
namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Requests\StoreSupplierRequest;
use App\Models\Supplier;
use App\Models\Sector;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    
    public function index(){
        $sectors = Sector::all();
        $total_suppliers = Supplier::count();
        return view('suppliers', compact('sectors', 'total_suppliers'));
    }

    public function getSuppliers(Request $request)
    {
        $sector = $request->input('sector');
        $search = $request->input('search');

        $query = DB::table('suppliers')
            ->join('sectors', 'sectors.sector_id', '=', 'suppliers.sector_id')
            ->select(
                'suppliers.supplier_id',
                'sectors.name as sector',
                'suppliers.supplier_code as code',
                'suppliers.name',
                'suppliers.contact', 
                'suppliers.phone',
                'suppliers.email',
                'suppliers.rfc',
                DB::raw("CONCAT(IFNULL(CONCAT(suppliers.address, ' '), ''), IFNULL(CONCAT(suppliers.district, ', '), ''), IFNULL(CONCAT(suppliers.city, ', '), ''), IFNULL(CONCAT(suppliers.state, ''), '')) as address")
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sectors.name', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.supplier_code', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.name', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.contact', 'like', '%' . $search . '%') 
                    ->orWhere('suppliers.phone', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.email', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.rfc', 'like', '%' . $search . '%');
            });
        }

        if (!empty($sector)) {
            $query->where('suppliers.sector_id', $sector);
        }
        $query->orderBy('suppliers.supplier_id', 'desc');

        $suppliers = $query->get();

        $suppliersWithPermissions = $suppliers->map(function ($row) {
            return [
                'supplier_id' => $row->supplier_id,
                'sector'      => $row->sector,
                'code'        => $row->code,
                'name'        => $row->name,
                'contact'     => $row->contact, 
                'phone'       => $row->phone,
                'email'       => $row->email,
                'rfc'         => $row->rfc,
                'address'     => $row->address,
                'canUpdate'   => auth()->user()->can('suppliers.update'),
                'canDelete'   => auth()->user()->can('suppliers.delete'),
            ];
        });

        return response()->json(['suppliers' => $suppliersWithPermissions]);
    }

    public function destroy($id){
        try{
            DB::transaction(function () use ($id){
                $supplier = Supplier::find($id);
                if($supplier) {
                    $supplier->delete();
                    return response()->json(['success' => true, 'message' => 'Supplier deleted']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Supplier not deleted'], 404);
                }
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function show($id) 
    {
        $supplier = Supplier::where('supplier_id', $id)->first();
        
        return response()->json([
            'supplier' => $supplier->only([
                'supplier_id',
                'supplier_code',
                'sector_id',
                'name',
                'contact', 
                'phone',
                'email',
                'rfc',
                'postal_code',
                'state',
                'city',
                'district',
                'address',
                'country'
            ])
        ]);
    }

    public function update(StoreSupplierRequest $request) {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->only([
                    'sector_id', 
                    'name', 
                    'contact', 
                    'phone', 
                    'email', 
                    'rfc', 
                    'state', 
                    'city', 
                    'district', 
                    'address'
                ]);

                Supplier::where('supplier_id', $request->supplier_id)->update($data);

                return response()->json(['message' => 'Operation successfully make it'], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        }
    }

    public function store(StoreSupplierRequest $request) {
        try {
            return DB::transaction(function () use ($request) {
                $count = Supplier::count();
                $data = $request->all();
                $next = $count + 1;

                $sector = Sector::find($data['sector_id']);
                $prefix = 'SP';
                
                if ($sector) {
                    $sectorName = strtolower(trim($sector->name));
                    switch ($sectorName) {
                        case 'pecuario':
                            $prefix = 'SPP';
                            break;
                        case 'agro':
                            $prefix = 'SPA';
                            break;
                        case 'food':
                            $prefix = 'SPCH';
                            break;
                        case 'petfood':
                            $prefix = 'SPPF';
                            break;
                        case 'envases':
                            $prefix = 'SPE';
                            break;
                        case 'industrial':
                            $prefix = 'SPI';
                            break;
                        case 'otro':
                            $prefix = 'SPO';
                            break;
                        default:
                            $prefix = 'SP';
                            break;
                    }
                }

                $data['supplier_code'] = $prefix . $next;

                Supplier::create($data);

                return response()->json(['message' => 'Operation successfully make it'], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        }
    }
}
