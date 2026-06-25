<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequisitionRequest;
use App\Models\PurchaseRequisition;
use App\Models\RequisitionProduct;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequisitionController extends Controller
{
    public function getRequisitions(Request $request){
        $search_requisitions = $request->input('search_requisitions');
        $requisitions_check  = $request->input('requisitions_check');
        $query = DB::table('purchases_requisitions');

        if (!empty($search_requisitions)) {
            $query->where(function ($q) use ($search_requisitions) {
                $q->where('applicant', 'like', '%' . $search_requisitions . '%')
                  ->orWhere('department', 'like', '%' . $search_requisitions . '%')
                  ->orWhere('consecutive', 'like', '%' . $search_requisitions . '%')
                  ->orWhere('purchase_order', 'like', '%' . $search_requisitions . '%');
            });
        }

        if ((int)$requisitions_check === 1) {
            $query->whereNotNull('consecutive')->whereNotNull('purchase_order');
        } else {
            $query->whereNull('consecutive')->whereNull('purchase_order');
        }

        $requisitions = $query->get();
        $ids = $requisitions->pluck('id')->filter()->values()->all();

        $insumoMap = [];
        if (!empty($ids)) {
            $insumoRows = DB::table('requisition_products')
                ->select('pr_id', 'insumo', DB::raw('COUNT(*) as c'))
                ->whereIn('pr_id', $ids)
                ->groupBy('pr_id', 'insumo')
                ->get();

            foreach ($insumoRows as $r) {
                $pid = $r->pr_id;
                if (!isset($insumoMap[$pid])) $insumoMap[$pid] = [];
                $insumoMap[$pid][strtolower((string)$r->insumo)] = (int)$r->c;
            }
        }

        $requisitionsWithPermissions = $requisitions->map(function ($row) use ($insumoMap) {
            $pid = $row->id;

            $summary = '—';
            if (isset($insumoMap[$pid])) {
                $hasDirecto   = !empty($insumoMap[$pid]['directo']);
                $hasIndirecto = !empty($insumoMap[$pid]['indirecto']);

                if ($hasDirecto && $hasIndirecto) $summary = 'mixto';
                elseif ($hasDirecto) $summary = 'directo';
                elseif ($hasIndirecto) $summary = 'indirecto';
            }

            return[
                'id'            => $row->id,
                'consecutive'   => $row->consecutive,
                'purchase_order'=> $row->purchase_order,
                'applicant'     => $row->applicant,
                'department'    => $row->department,
                'insumo_resumen'=> $summary, 
                'data_sheet'    => $row->data_sheet,
                'safety_sheet'  => $row->safety_sheet,
                'canUpdate'     => auth()->user()->can('purchases.requisitions.update'),
                'canDelete'     => auth()->user()->can('purchases.requisitions.delete'),
            ];
        });

        return response()->json(['requisitions'=>$requisitionsWithPermissions]);
    }

    public function getYourRequisitions()
    {
        $query = DB::table('purchases_requisitions')
            ->leftJoin('requisition_products', 'requisition_products.pr_id', '=', 'purchases_requisitions.id')
            ->select(
                'purchases_requisitions.id',
                DB::raw("CONCAT(DAYNAME(purchases_requisitions.updated_at), ', ', DAY(purchases_requisitions.updated_at), ' ', MONTHNAME(purchases_requisitions.updated_at), ' ', YEAR(purchases_requisitions.updated_at)) as date_formatted"),
                DB::raw('COUNT(requisition_products.id) as products'),
                'purchases_requisitions.data_sheet',
                'purchases_requisitions.safety_sheet',
                'purchases_requisitions.consecutive',   
                'purchases_requisitions.purchase_order'  
            )
            ->groupBy(
                'purchases_requisitions.id',
                'purchases_requisitions.updated_at',
                'purchases_requisitions.data_sheet',
                'purchases_requisitions.safety_sheet',
                'purchases_requisitions.consecutive',
                'purchases_requisitions.purchase_order'
            )
            ->where('purchases_requisitions.applicant', auth()->user()->name);

        $requisitions = $query->get();
        $ids = $requisitions->pluck('id')->filter()->values()->all();
        $insumoMap = [];

        if (!empty($ids)) {
            $insumoRows = DB::table('requisition_products')
                ->select('pr_id', 'insumo', DB::raw('COUNT(*) as c'))
                ->whereIn('pr_id', $ids)
                ->groupBy('pr_id', 'insumo')
                ->get();

            foreach ($insumoRows as $r) {
                $pid = $r->pr_id;
                if (!isset($insumoMap[$pid])) $insumoMap[$pid] = [];
                $insumoMap[$pid][strtolower((string)$r->insumo)] = (int)$r->c;
            }
        }

        $requisitionsWithPermissions = $requisitions->map(function ($row) use ($insumoMap) {
            $pid = $row->id;
            $summary = '—';

            if (isset($insumoMap[$pid])) {
                $hasDirecto   = !empty($insumoMap[$pid]['directo']);
                $hasIndirecto = !empty($insumoMap[$pid]['indirecto']);

                if ($hasDirecto && $hasIndirecto) $summary = 'mixto';
                elseif ($hasDirecto) $summary = 'directo';
                elseif ($hasIndirecto) $summary = 'indirecto';
            }

            $isLocked = !is_null($row->consecutive) || !is_null($row->purchase_order);

            return [
                'id'             => $row->id,
                'date_formatted' => $row->date_formatted,
                'products'       => $row->products,
                'insumo_resumen' => $summary,
                'data_sheet'     => $row->data_sheet,
                'safety_sheet'   => $row->safety_sheet,
                'canUpdate'      => !$isLocked && auth()->user()->can('purchases.requisitions.update'),
                'canDelete'      => !$isLocked && auth()->user()->can('purchases.requisitions.delete'),
                'status'         => $isLocked ? 'Revisada / Procesada' : 'Pendiente'
            ];
        });

        return response()->json(['requisitions' => $requisitionsWithPermissions]);
    }

    public function deleteProductRequisition(Request $request){
        $product = RequisitionProduct::find($request->id);

        if($product) {
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not deleted'], 404);
        }
    }

    public function checkRequisition(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:purchases_requisitions,id',
                'consecutive' => 'required|string|max:30',
                'purchase_order' => 'required|string|max:50',
            ]);

            DB::transaction(function () use ($validated) {
                PurchaseRequisition::where('id', $validated['id'])
                    ->update([
                        'consecutive' => $validated['consecutive'],
                        'purchase_order' => $validated['purchase_order'],
                    ]);
            });

            return response()->json([
                'message' => 'Operation successfully completed'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy($id){
        try{
            DB::transaction(function () use ($id){
                $requisition = PurchaseRequisition::find($id);

                if($requisition) {
                    $requisition->delete();
                    return response()->json(['success' => true, 'message' => 'Requisition deleted']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Requisition not deleted'], 404);
                }
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function show($id)
    {
        $requisition = PurchaseRequisition::where('id', $id)->first();
        $products = RequisitionProduct::where('pr_id', $id)->get();
        $products->transform(function ($product) {

            if (preg_match('/^(\d+(\.\d+)?)\s*([a-zA-Z]+)?$/', $product->quantity, $matches)) {
                $product->qty_number = $matches[1];    
                $product->qty_unit   = $matches[3] ?? ''; 
            } else {
                $product->qty_number = $product->quantity;
                $product->qty_unit   = '';
            }
            return $product;
        });

        return response()->json(['requisition' => $requisition, 'products' => $products]);
    }

    public function update(StoreRequisitionRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $ids          = $request->input('id', []);
                $descriptions = $request->input('description', []);
                $suppliers    = $request->input('supplier', []);
                $urls         = $request->input('url', []);
                $uses         = $request->input('use', []);
                $quantities   = $request->input('quantity', []);
                $units        = $request->input('unit', []);
                $image_urls   = $request->input('image_url', []);
                $insumos      = $request->input('insumo', []);
                
                $reqId = $request->req_id;
                
                $keptIds = array_filter($ids, function ($val) {
                    return $val !== 'null' && !empty($val);
                });

                RequisitionProduct::where('pr_id', $reqId)
                    ->whereNotIn('id', $keptIds)
                    ->delete();

                $data_requisition = [
                    'applicant'      => $request->applicant,
                    'department'     => $request->department,
                    'data_sheet'     => $request->data_sheet,
                    'safety_sheet'   => $request->safety_sheet,
                    'comparative_id' => $request->input('comparative_id')
                ];

                PurchaseRequisition::where('id', $reqId)->update($data_requisition);

                foreach ($ids as $index => $id) {
                    $auxQty = '' . ($quantities[$index] ?? '') . ($units[$index] ?? '');

                    $productData = [
                        'description' => $descriptions[$index] ?? null,
                        'supplier'    => $suppliers[$index] ?? null,
                        'url'         => $urls[$index] ?? null,
                        'use'         => $uses[$index] ?? null,
                        'quantity'    => $auxQty,
                        'image_url'   => $image_urls[$index] ?? null,
                        'insumo'      => $insumos[$index] ?? 'directo',
                    ];

                    if ($id == 'null' || empty($id)) {
                        $productData['pr_id'] = $reqId;
                        RequisitionProduct::create($productData);
                    } else {
                        RequisitionProduct::where('id', $id)->update($productData);
                    }
                }

                return response()->json(['message' => 'Operation successfully completed'], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(StoreRequisitionRequest $request)
    {

        try {
            return DB::transaction(function () use ($request) {
                $descriptions = $request->input('description', []);
                $suppliers    = $request->input('supplier', []);
                $urls         = $request->input('url', []);
                $uses         = $request->input('use', []);
                $quantities   = $request->input('quantity', []);
                $units        = $request->input('unit', []);
                $image_urls   = $request->input('image_url', []);
                $insumos      = $request->input('insumo', []);
                $data_requisition = [
                    'applicant'      => $request->applicant,
                    'department'     => $request->department,
                    'data_sheet'     => $request->data_sheet,
                    'safety_sheet'   => $request->safety_sheet,
                    'comparative_id' => $request->input('comparative_id'),
                    'consecutive'    => $request->input('consecutive') 
                ];

                $requisition = PurchaseRequisition::create($data_requisition);

                foreach ($descriptions as $index => $description) {
                    $auxQty = '' . ($quantities[$index] ?? '') . ($units[$index] ?? '');

                    $productData = [
                        'description' => $description,
                        'supplier'    => $suppliers[$index] ?? null,
                        'url'         => $urls[$index] ?? null,
                        'use'         => $uses[$index] ?? null,
                        'quantity'    => $auxQty,
                        'image_url'   => $image_urls[$index] ?? null,
                        'pr_id'       => $requisition->id, 
                        'insumo'      => $insumos[$index] ?? 'directo',
                    ];

                    RequisitionProduct::create($productData);
                }

                return response()->json([
                    'message' => 'Operation successfully completed',
                    'id' => $requisition->id
                ], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPurchaseOrderFolios()
    {
        $folios = DB::table('purchase_orders')->select('id')->get();
        return response()->json($folios);
    }

    public function getComparativeFolios()
    {
        try {
            $folios = DB::table('comparative')
                        ->where('user_id', auth()->id())
                        ->select(DB::raw('MAX(id) as id'), 'folio') 
                        ->groupBy('folio')
                        ->get();

            return response()->json($folios);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getComparativeProducts($id)
    {
        try {
            $comparative = DB::table('comparative')->where('id', $id)->first();
            if (!$comparative) {
                return response()->json([]);
            }
            $products = DB::table('comparative')
                        ->where('folio', $comparative->folio)
                        ->get();

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}