<?php
namespace App\Helpers;
use Illuminate\Database\QueryException;

class DatabaseErrors{
    public static function handle(QueryException $e){
        $errorCode = $e->errorInfo[1] ?? null;
        switch($errorCode){
            case 1062:
                return self::duplicateKey($e);
            case 1451:
                return self::parentForeignKey($e);
            case 1452:
                return self::nonexistentForeignKey($e); 
            default:
                return response()->json([
                    'error' => 'Error at the database: ' . $e->getMessage()
                ], 500);
        }
    }

    private static function nonexistentForeignKey(QueryException $e){
        return response()->json([
            'error' => "An attempt is being made to link to non-existent data. (1452)"
        ], 409);
    }

    private static function parentForeignKey(QueryException $e){
        return response()->json([
            'error' => "This record cannot be deleted because it is linked to other data. (1451)"
        ], 409);
    }

    private static function duplicateKey(QueryException $e){
        preg_match("/Duplicate entry '(.+)' for key '(.+)'/", $e->getMessage(), $matches);
        $value = $matches[1] ?? 'Value unknow';
        $key = $matches[2] ?? 'Key unknow';

        //Por si el nombre es raro
        $map = [
            'customers_customer_code_unique' => 'Customer code',
            'suppliers_supplier_code_unique' => 'Supplier code',
            'inventory_batch_unique' => 'Warehouse Batch',
            'products_sku_unique' => "SKU's Product",
            'sales_purchase_order_unique' => "Sales Purchase",
            'sales_invoice_unique' => "Sales Invoice",
            'folio_unique' => "Folio",
            'purchases_requisitions_consecutive_unique' => 'Consecutive',
            'purchases_requisitions_purchase_order_unique' => 'Purchase Order',
        ];
        
        $keys = $map[$key] ?? $key;

        return response()->json([
            'error' => "The value '{$value}' alright exist in the '{$keys}'. (1062)"
        ], 409);
    }
}