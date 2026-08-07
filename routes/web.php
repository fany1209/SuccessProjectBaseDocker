<?php

use App\Http\Controllers\TrailerController;
use App\Http\Controllers\TransportLineController;
use App\Http\Controllers\CliController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\XlsController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\TweakController;
use App\Http\Controllers\SupplierCertificateController;
use App\Http\Controllers\CertificateApiController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\Admin\ComplaintAdminController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\SoilAnalysisController;
use App\Http\Controllers\WeeklyPlanController;
use App\Http\Controllers\LabSampleController;
use App\Http\Controllers\LabChartController;
use App\Http\Controllers\LotRequestController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InsumoEntradaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\idController;
use App\Http\Controllers\ComparativeController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\SupplierPriceController; 
use App\Http\Controllers\FumigacionController;
use App\Http\Controllers\MinutaController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReagentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RhController;
use App\Http\Controllers\RecursosHumanosController;
use App\Http\Controllers\CuentasPorCobrarController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\PortalPasswordResetController;
use App\Http\Middleware\RedirectIfClientUnauthenticated;
use App\Http\Controllers\PortalUserController;

Route::get('/', function () {
    return view('home');
})->name('home');

 //sector and cards
    Route::get('/porcinos', function () {return view('home.cards-sectors.porcinos');})->name('porcinos.index');
    Route::get('/bovinos', function () {return view('home.cards-sectors.bovinos');})->name('bovinos.index');
    Route::get('/perros', function () {return view('home.cards-sectors.perros');})->name('perros.index');
    Route::get('/gatos', function () {return view('home.cards-sectors.gatos');})->name('gatos.index');

Route::get('/products', function () {
    return view('products');
})->name('products');

Route::get('/detail-product/{product_id}', function () {
    return view('detail-product');
})->name('detail-product');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactoController::class, 'store'])
    ->name('contact.store');

    // Rutas Públicas del Portal de Clientes
Route::get('/portal-clientes', [PortalAuthController::class, 'showLoginForm'])->name('portal.login');
Route::post('/portal-clientes', [PortalAuthController::class, 'login'])->name('portal.login.submit');
Route::post('/portal-logout', [PortalAuthController::class, 'logout'])->name('portal.logout');
Route::get('admin/portal-users/{portalUserId}/sales', [PortalUserController::class, 'getClientSales']);

// Rutas de restablecimiento de contraseña — Portal de Clientes
Route::get('/portal/forgot-password', [PortalPasswordResetController::class, 'showForgotForm'])
    ->name('portal.password.request');
Route::post('/portal/forgot-password', [PortalPasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')  // máx. 5 intentos por minuto por IP
    ->name('portal.password.email');
Route::get('/portal/reset-password/{token}', [PortalPasswordResetController::class, 'showResetForm'])
    ->name('portal.password.reset');
Route::post('/portal/reset-password', [PortalPasswordResetController::class, 'resetPassword'])
    ->name('portal.password.update');

Route::middleware(RedirectIfClientUnauthenticated::class)->get('/portal/venta/{id}', [PortalAuthController::class, 'verDetalle'])->name('portal.ver-detalle');
Route::middleware(RedirectIfClientUnauthenticated::class)->get('/portal/dashboard', [PortalAuthController::class, 'dashboard'])->name('portal.dashboard');
Route::middleware(RedirectIfClientUnauthenticated::class)->get('/portal/descargar-pdf/{id}', [PortalAuthController::class, 'descargarPdf'])->name('portal.descargar-pdf');
Route::middleware(RedirectIfClientUnauthenticated::class)->post('/portal/change-password', [PortalPasswordResetController::class, 'changePassword'])->name('portal.password.change');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    //Home
    Route::get('/main-menu', function () {
        return view('main-menu');
    })->name('main-menu');

    //Profile
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
        
        Route::post('/sessions/logout', [ProfileController::class, 'logoutOtherSessions'])
            ->name('other-browser-sessions.destroy');

        Route::post('/profile/photo/remove', [ProfileController::class, 'removePhoto'])
            ->name('profile-photo.destroy');

        Route::post('/profile/update-info', [ProfileController::class, 'updateProfileInfo'])
            ->name('profile.update-info');

        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])
            ->name('profile.update-password');

        Route::post('/profile/delete', [ProfileController::class, 'deleteUser'])
            ->name('profile.delete');
    });

    //Inventory
    Route::resource('/inventory', InventoryController::class)->middleware('can:inventory.show');
    Route::get('/getInventoryAvailable', [InventoryController::class, 'getInventoryAvailable'])->middleware('can:inventory.show')->name('inventory.getInventoryAvailable');
    Route::put('/updateDate', [InventoryController::class, 'updateDate'])->middleware('can:inventory.show')->name('inventory.updateDate');
    Route::put('/updateQuarantine', [InventoryController::class, 'updateQuarantine'])->middleware('can:inventory.show')->name('inventory.updateQuarantine');
    Route::post('/makeTransaction', [InventoryController::class, 'makeTransaction'])->middleware('can:inventory.show')->name('inventory.makeTransaction');
    Route::post('/addQuarantine', [InventoryController::class, 'addQuarantine'])->middleware('can:inventory.show')->name('inventory.addQuarantine');
    Route::put('/updateComment/{type}/{id}', [InventoryController::class, 'updateComment'])->middleware('can:inventory.show')->name('inventory.updateComment');

    //Inputs
    Route::resource('inputs',InputController::class)->middleware('can:inventory.show');

    //Outputs
    Route::resource('outputs', OutputController::class)->middleware('can:inventory.show');

    //Warehouse
    Route::get('/warehouse', [WarehouseController::class, 'index'])->middleware('can:warehouse.show')->name('warehouse');
    Route::get('/getWarehouse', [WarehouseController::class, 'getWarehouse'])->middleware('can:warehouse.show')->name('warehouse.getWarehouse');
    Route::get('/getInfoLocation', [WarehouseController::class, 'getInfoLocation'])->middleware('can:warehouse.show')->name('warehouse.getInfoLocation');
    Route::post('/temperature', [WarehouseController::class, 'temperature'])->middleware('can:warehouse.show')->name('warehouse.temperature');

    //Cli
    Route::resource('cli',CliController::class)->middleware('can:warehouse.show');

    //Logistic
    Route::resource('logistic',LogisticController::class)->middleware('can:warehouse.show');
    Route::get('/charts', [LogisticController::class, 'charts'])->middleware('can:warehouse.show')->name('logistic.charts');

    //Transport Line
    Route::resource('transportLine',TransportLineController::class)->middleware('can:warehouse.show');
    Route::get('/getTransportLines', [TransportLineController::class, 'getTransportLines'])->middleware('can:warehouse.show')->name('transportLine.getTransportLines');

    //Operator
    Route::resource('operator',OperatorController::class)->middleware('can:warehouse.show');
    Route::get('/getOperators', [OperatorController::class, 'getOperators'])->middleware('can:warehouse.show')->name('operator.getOperators');

    //Vehicle
    Route::resource('vehicle',VehicleController::class)->middleware('can:warehouse.show');
    Route::get('/getVehicles', [VehicleController::class, 'getVehicles'])->middleware('can:warehouse.show')->name('vehicle.getVehicles');

    //Trailer
    Route::resource('trailer',TrailerController::class)->middleware('can:warehouse.show');
    Route::get('/getTrailers', [TrailerController::class, 'getTrailers'])->middleware('can:warehouse.show')->name('trailer.getTrailers');

    //Tweaks
    Route::resource('tweaks',TweakController::class)->middleware('can:inventory.show');

    //Customers
    Route::resource('customers',CustomerController::class)->middleware('can:customers.show');
    Route::get('/getCustomers', [CustomerController::class, 'getCustomers'])->middleware('can:customers.show')->name('customers.getCustomers');

    //Purchases
    Route::resource('purchases',PurchaseController::class)->middleware('can:purchases.show');
    Route::get('/getPurchasesCharts', [PurchaseController::class, 'getPurchasesCharts'])->middleware('can:purchases.show')->name('purchases.charts');
    Route::post('/gSupSelectCrit', [PurchaseController::class, 'gSupSelectCrit'])->name('suppliers.gSupSelectCrit');
    Route::post('/gSupplierEvaluation', [PurchaseController::class, 'gSupplierEvaluation'])->name('suppliers.gSupplierEvaluation');
    Route::post('/gPurchaseOrder', [PurchaseController::class, 'gPurchaseOrder'])->name('purchases.gPurchaseOrder');

    // Purchase Orders
    Route::get('/purchases/orders', [PurchasesController::class, 'orders'])->name('purchases.orders');
    Route::get('/purchases/get-orders', [PurchasesController::class, 'getPurchaseOrders']) ->name('purchases.getPurchaseOrders');
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/order-pdf/{id}', [PurchaseController::class, 'streamPdf'])->name('purchases.streamPdf');
    Route::get('/purchases/orders/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.editOrder');
    Route::put('/purchases/orders/{id}', [PurchaseController::class, 'update'])->name('purchases.updateOrder');
    Route::delete('/purchases/orders/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroyOrder');
    Route::get('/requisitions/purchase-order-folios', [RequisitionController::class, 'getPurchaseOrderFolios'])->name('requisitions.getPurchaseOrderFolios');

    //Requisitions
    Route::resource('requisitions',RequisitionController::class)->middleware('can:purchases.requisitions.create');
    Route::get('/getRequisitions', [RequisitionController::class, 'getRequisitions'])->middleware('can:purchases.requisitions.show')->name('requisitions.getRequisitions');
    Route::get('/getYourRequisitions', [RequisitionController::class, 'getYourRequisitions'])->middleware('can:purchases.requisitions.create')->name('requisitions.getYourRequisitions');
    Route::post('/checkRequisition', [RequisitionController::class, 'checkRequisition'])->middleware('can:purchases.requisitions.show')->name('requisitions.checkRequisition');
    Route::delete('/deleteProductRequisition', [RequisitionController::class, 'deleteProductRequisition'])->middleware('can:purchases.requisitions.update')->name('requisitions.deleteProductRequisition');

    Route::get('/get-comparative-folios', [RequisitionController::class, 'getComparativeFolios']);
    Route::get('/get-comparative-products/{folio}', [RequisitionController::class, 'getComparativeProducts']);
    
    // purchases comparative
    Route::post('/comparative/store', [ComparativeController::class, 'store'])->name('comparative.store');
    Route::get('/comparative', [ComparativeController::class, 'index'])->name('comparative.index');
    Route::delete('/purchases/comparative/{folio}', [ComparativeController::class, 'destroyByFolio'])->name('purchases.comparative.destroy');
    Route::get('/purchases/comparative-pdf/{folio}', [ComparativeController::class, 'generatePDF'])->name('purchases.comparative.pdf');
    Route::put('/purchases/comparative/update-all', [ComparativeController::class, 'updateAll'])->name('purchases.comparative.update.all');

    Route::get('suppliers-get-directory', [DirectoryController::class, 'getDirectory'])->name('suppliers.getDirectory');
    Route::resource('purchases/supplier-directory', DirectoryController::class)
    ->names('supplier_directory')
    ->parameters(['supplier-directory' => 'code']);

    //Propects
    Route::resource('prospects', ProspectController::class)->middleware('can:prospects.show');
    Route::get('/getProspects', [ProspectController::class, 'getProspects'])->middleware('can:prospects.show')->name('prospects.getProspects');

    //Suppliers
    Route::resource('/suppliers',SupplierController::class)->middleware('can:suppliers.show');
    Route::get('/getSuppliers', [SupplierController::class, 'getSuppliers'])->middleware('can:suppliers.show')->name('suppliers.getSuppliers');

    //Supplier Certificate
    Route::post('/supplier-certificates', [SupplierCertificateController::class, 'store'])->name('supplier_certificates.store');
    Route::get('/quality/certificates/index', [SupplierCertificateController::class, 'index'])->name('supplier_certificates.index');
    Route::delete('/supplier-certificates/{id}', [SupplierCertificateController::class, 'destroy'])->name('supplier_certificates.destroy');

    //Catalog
    Route::resource('catalogs',ProductController::class)->middleware('can:products.show');
    Route::get('/getProducts', [ProductController::class, 'getProducts'])->middleware('can:products.show')->name('catalog.getProducts');
    Route::delete('/deleteImage', [ProductController::class, 'deleteImage'])->middleware('can:products.show')->name('catalog.deleteImage');
    Route::delete('/deleteFile', [ProductController::class, 'deleteFile'])->middleware('can:products.show')->name('catalog.deleteFile');
    
    //Sales
    Route::resource('sales', SalesController::class)->middleware('can:sales.show');
    Route::get('/getSales', [SalesController::class, 'getSales'])->middleware('can:sales.show')->name('sales.getSales');
    Route::delete('/deleteDetail', [SalesController::class, 'deleteDetail'])->middleware('can:sales.show')->name('sales.deleteDetail');
    Route::get('/sales-chart-data', [SalesController::class, 'getRemisionesChartData'])->name('sales.chartData');
    Route::patch('/sales/{sale}/update-status', [SalesController::class, 'updateStatus'])->name('sales.updateStatus');
    Route::get('/sales/{id}/almacen', [SalesController::class, 'almacenDetail'])->name('sales.almacen_detail');
    Route::post('/sales/{id}/almacen/action', [SalesController::class, 'almacenAction'])->name('sales.almacen_action');
    Route::get('/almacen-notifications/unread', [SalesController::class, 'unreadNotifications'])->name('almacen_notifications.unread');
    Route::post('/almacen-notifications/mark-read/{id}', [SalesController::class, 'markNotificationAsRead'])->name('almacen_notifications.mark_read');
    Route::get('/sales/{id}/almacen/lots', [SalesController::class, 'getSaleLots'])->name('sales.almacen_lots');
    Route::get('/almacen/postponed-reminders', [SalesController::class, 'postponedReminders'])->name('almacen.postponed_reminders');
    
    //quote
    Route::resource('quotes', QuoteController::class)->names([
    'index' => 'quotes',]);
    Route::get('quotes/{id}/pdf', [QuoteController::class, 'generatePdf'])->name('quotes.pdf');
    Route::patch('/quotes/{id}/update-status', [QuoteController::class, 'updateStatus'])->name('quotes.updateStatus');
    
    //quality
    Route::middleware('can:quality.show')->group(function () {
    Route::get('/charts/pdf-generations', [QualityController::class, 'pdfGenerationsChartData'])->name('charts.pdf-generations');
    Route::post('/quality/almacen/store', [QualityController::class, 'almacenStore'])->name('quality.almacen.store');
    Route::delete('/quality/inspections/{id}', [QualityController::class, 'destroy'])->name('quality.inspections.destroy');
    Route::get('quality/warehouse-inspection', [QualityController::class, 'getWarehouseInspection'])->name('quality.getWarehouseInspection');
    Route::get('/quality/check-pending-warehouse', [QualityController::class, 'checkPending'])->name('quality.checkPendingWarehouseInspections');
    Route::post('/quality/update-inspection', [QualityController::class, 'update'])->name('quality.updateInspection');
    Route::patch('/quality/warehouse/{id}/updatew', [QualityController::class, 'updatew'])->name('quality.warehouse.updatew');
    Route::delete('/eliminarObs', [QualityController::class, 'eliminarObs'])->name('quality.eliminarObs');
    Route::get('/getInspections', [QualityController::class, 'getInspections'])->name('quality.getInspections');
    Route::get('/getInspection', [QualityController::class, 'getInspection'])->name('quality.getInspection');
    Route::get('/getDataq', [QualityController::class, 'getData'])->name('quality.getDataq');
    Route::get('/quality', [QualityController::class, 'index'])->name('quality.index');
    Route::get('/quality/pdf', [QualityController::class, 'ver'])->name('quality.pdf');
    Route::get('/quality/products', [QualityController::class, 'getProducts'])->name('quality.getProducts');
    Route::post('/quality', [QualityController::class, 'store'])->name('quality.store');

    Route::get('/incidencias', [QualityController::class, 'incidencias'])->name('quality.incidencias');
    Route::post('/quality/pdf2', [QualityController::class, 'pdf2'])->name('quality.pdf2');
    Route::post('/quality/pdf3', [QualityController::class, 'pdf3'])->name('quality.pdf3');
    Route::post('/quality/pd4', [QualityController::class, 'pdf4'])->name('quality.pdf4');
    Route::get('/quality/pdf5/form', [QualityController::class, 'pdf5Form'])->name('quality.pdf5.form');
    Route::post('/quality/pdf6', [QualityController::class, 'pdf6'])->name('quality.pdf6');
    Route::post('/quality/pdf7', [QualityController::class, 'pdf7'])->name('quality.pdf7');
    Route::post('/quality/pdf8', [QualityController::class, 'pdf8'])->name('quality.pdf8');
    Route::post('/quality/pdf9', [QualityController::class, 'pdf9'])->name('quality.pdf9');
    Route::post('/quality/pdf10', [QualityController::class, 'pdf10'])->name('quality.pdf10');
    Route::post('/quality/pdf11', [QualityController::class, 'pdf11'])->name('quality.pdf11');
    Route::post('/quality/pdf12', [QualityController::class, 'pdf12'])->name('quality.pdf12');
    Route::get('/quality/inspection-w/view',[QualityController::class, 'getInspectionWView'])->name('quality.getInspectionWView');

    });

    //certificates_quality
    Route::get('/certificados/json', [CertificateApiController::class, 'index'])->name('certificados.json');
    Route::delete('/certificados/{id}', [CertificateApiController::class, 'destroy'])->name('certificados.destroy');
    Route::get('/certificados/export', [CertificateApiController::class, 'export'])->name('certificados.export');

    //complaints
    Route::resource('complaints', ComplaintController::class)->only(['create','store']);

    //laboratory
    Route::middleware('can:laboratory.show')->group(function () {
    Route::get('/laboratory', [LaboratoryController::class, 'index'])->name('laboratory');
    Route::get('/laboratory/formats/01/preview', [LaboratoryController::class, 'previewFormat01'])->name('laboratory.format01.preview');
    Route::get('/laboratory/formats/01/pdf', [LaboratoryController::class, 'pdfFormat01'])->name('laboratory.format01.pdf');
    Route::get('/laboratory', [LaboratoryController::class, 'index'])->name('laboratory.index');
    Route::get('/reception', [ReceptionController::class, 'datatable'])->name('reception');
    Route::get('/reception/batches', [ReceptionController::class, 'getBatches'])->name('getReceptionBatches');
    Route::get('/reception/{id}', [ReceptionController::class, 'show'])->name('reception.show');
    Route::patch('/pdf1/{id}', [ReceptionController::class, 'update'])->name('pdf1.update');
    Route::delete('/laboratory/reception/{id}', [ReceptionController::class, 'destroy'])->name('laboratory.reception.destroy');
    Route::get('/laboratory/reception/{id}/pdf', [ReceptionController::class, 'pdfShow'])->name('laboratory.reception.pdf');
    Route::patch('/reception/{id}/quality', [ReceptionController::class, 'updateq'])->whereNumber('id')->name('reception.quality.updateq');
    Route::post('/laboratory/reception/store', [ReceptionController::class, 'store'])->name('laboratory.reception.store');
    Route::get('/quality/check-pending-quality', [ReceptionController::class, 'checkPendingQuality'])->name('quality.checkPendingQuality');
    Route::get('/soil-analyses', [SoilAnalysisController::class, 'index'])->name('soil.analyses');
    Route::get('/soil-analyses/json', [SoilAnalysisController::class, 'datatable'])->name('soil.analyses.json');
    Route::get('/soil-analyses/{id}/pdf', [SoilAnalysisController::class, 'pdfShow'])->whereNumber('id')->name('soil.analyses.pdf');
    Route::delete('/soil-analyses/{id}', [SoilAnalysisController::class, 'destroy'])->whereNumber('id')->name('soil.analyses.delete');
    Route::prefix('weekly-plans')->group(function () {
        Route::get('/',             [WeeklyPlanController::class, 'index'])->name('weekly.plans');
        Route::get('/json',         [WeeklyPlanController::class, 'datatable'])->name('weekly.plans.json');
        Route::get('/{id}/pdf',     [WeeklyPlanController::class, 'pdf'])->whereNumber('id')->name('weekly.plans.pdf');
        Route::delete('/{id}',      [WeeklyPlanController::class, 'destroy'])->whereNumber('id')->name('weekly.plans.delete');
    });
    Route::get('/laboratory/monitoring/csv', [LaboratoryController::class, 'exportMonitoringCsv'])->name('laboratory.monitoring.csv');
    Route::get('/laboratory-samples/export', [LaboratoryController::class, 'exportCsv'])->name('laboratory_samples.export');

    Route::post('/laboratory/pdf1', [LaboratoryController::class, 'pdf1'])->name('laboratory.pdf1');
    Route::post('/laboratory/pdf3', [LaboratoryController::class, 'pdf3'])->name('laboratory.pdf3');
    Route::post('/laboratory-samples/inv', [LaboratoryController::class, 'inv'])->name('laboratory_samples.inv');
    Route::post('/laboratory/pdf5', [LaboratoryController::class, 'pdf5'])->name('laboratory.pdf5');
    Route::post('/laboratory/pdf6', [LaboratoryController::class, 'pdf6'])->name('laboratory.pdf6');
    Route::post('/laboratory/pdf7', [LaboratoryController::class, 'pdf7'])->name('laboratory.pdf7');
    Route::post('/laboratory/pdf9', [LaboratoryController::class, 'pdf9'])->name('laboratory.pdf9');
    Route::post('/laboratory/pdf10', [LaboratoryController::class, 'pdf10'])->name('laboratory.pdf10');
    Route::post('/laboratory/pdf11', [LaboratoryController::class, 'pdf11'])->name('laboratory.pdf11');
    Route::post('/laboratory/pdf12', [LaboratoryController::class, 'pdf12'])->name('laboratory.pdf12');
    Route::post('/laboratory/pdf14', [LaboratoryController::class, 'pdf14'])->name('laboratory.pdf14');
    Route::post('/laboratory/pdf01pr', [LaboratoryController::class, 'pdf01pr'])->name('laboratory.pdf01pr');

    Route::get('/get-muestras', [LaboratoryController::class, 'getMuestras'])->name('laboratory.getMuestras');
    Route::get('/muestras/reimprimir/{id}', [LaboratoryController::class, 'reimprimirPdf'])->name('laboratory.reimprimirPdf');
    Route::delete('/muestras/{id}', [LaboratoryController::class, 'destroyMuestra'])->name('laboratory.destroyMuestra');
    Route::put('/muestras/{id}', [LaboratoryController::class, 'updateMuestra'])->name('laboratory.updateMuestra');
    });
        Route::post('/laboratory/pdf2', [LaboratoryController::class, 'pdf2'])->name('laboratory.pdf2');
        Route::post('/laboratory/store2', [LaboratoryController::class, 'store2'])->name('laboratory.store2');
        Route::get('/laboratory/customer-requests-json', [LaboratoryController::class, 'getCustomerRequestsJson'])->name('laboratory.customer_requests.json');
        Route::delete('/laboratory/customer-request/{id}', [LaboratoryController::class, 'destroyCustomerRequest']);
        Route::get('/laboratory/customer-request/{id}/pdf', [LaboratoryController::class, 'reprintPdf2']);
        Route::get('/laboratory/customer-request/{id}', [LaboratoryController::class, 'showCustomerRequest']);

        Route::post('/laboratory/customer-request/{id}', [LaboratoryController::class, 'updateCustomerRequest'])->name('laboratory.update2');
        Route::patch('/laboratory/customer-request/{id}/status', [LaboratoryController::class, 'updateStatus'])->name('laboratory.update_status');
        Route::get('/laboratory/customer-samples', [LaboratoryController::class, 'muestrasIndex'])->name('muestras.index');

        Route::get('/laboratory/muestras', [LaboratoryController::class, 'indexMuestras'])->name('laboratory.muestras.index');


Route::prefix('laboratory/reagents')->group(function () {
    Route::get('/', [ReagentController::class, 'index'])->name('reagents.index');
    Route::delete('/{id}', [ReagentController::class, 'destroy'])->name('reagents.destroy');
    Route::get('/{id}/edit', [ReagentController::class, 'edit'])->name('reagents.edit');
    Route::put('/{id}', [ReagentController::class, 'update'])->name('reagents.update');
    Route::post('/', [ReagentController::class, 'store'])->name('reagents.store');
});  


Route::prefix('laboratory/materials')->group(function () {
    Route::get('/', [MaterialController::class, 'index'])->name('materials.index');
    Route::delete('/{id}', [MaterialController::class, 'destroy'])->name('materials.destroy');
    Route::get('/{id}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/{id}', [MaterialController::class, 'update'])->name('materials.update');
    Route::post('/', [MaterialController::class, 'store'])->name('materials.store');
});
    //orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders-json', [OrderController::class, 'getData'])->name('orders.json');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}/edit', [OrderController::class, 'edit']);
    Route::post('/orders/{id}/update', [OrderController::class, 'update']);

    //lab samples
    Route::get('/lab-samples', [LabSampleController::class, 'index'])->name('lab.samples.index');
    Route::get('/lab-samples/datatable', [LabSampleController::class, 'datatable'])->name('lab.samples.datatable');
    Route::delete('/laboratory/lab-samples/{id}', [LabSampleController::class, 'destroy'])->name('lab.samples.destroy');
    Route::get('/lab-samples/inv_samples', function () {
        return view('formats.laboratory.inv_samples');})->name('lab.samples.inv_samples');
    Route::get('/lab/grafica', [LabChartController::class, 'index'])->name('lab.charts.index');
    Route::get('/lab/grafica/counts', [LabChartController::class, 'counts'])->name('lab.charts.counts');

    Route::get('/laboratory/lab-samples/{id}', [LabSampleController::class, 'showLabSample'])
    ->name('lab.samples.show');

    Route::put('/laboratory/lab-samples/{id}', [LabSampleController::class, 'updateLabSample'])
    ->name('lab.samples.update');


    //sales clientes grafica
    Route::get('/ventas-grafica-comparativa', function () {
        return view('sales.grafica2'); 
    })->name('sales.grafica2');

    Route::get('/api/datos-grafica-cliente', [SalesController::class, 'getClienteData'])->name('api.clienteData');

    //peticiones de lotes
    Route::post('/lot-requests', [LotRequestController::class, 'store'])->name('lot.request.store');
    Route::get('/lot-requests/check', [LotRequestController::class, 'checkPending'])->name('lot.request.check');
    Route::patch('/lot-requests/{id}', [LotRequestController::class, 'updateStatus'])->name('lot.request.update');
    Route::get('/lot-requests', [LotRequestController::class, 'index'])->name('lot.request.index');
    Route::get('/lot-requests/datatable', [LotRequestController::class, 'datatable'])->name('lot.request.datatable');

    // sales new
    Route::get('/purchases/po/demo', [PurchaseController::class, 'demoPdf'])->name('purchases.po.demo');
    Route::get('/purchases/suppliers/criteria/demo', [PurchaseController::class, 'demo'])->name('suppliers.criteria.demo');
    Route::get('/purchases/suppliers/evaluation/demo6', [PurchaseController::class, 'demo6'])->name('suppliers.evaluation.demo6');

    Route::get('/image/{name}', [ImageController::class, 'mostrar']);
    Route::get('/profile-photo/{name}', [ImageController::class, 'profilePhoto']);

    //finance
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/datatable', [FinanceController::class, 'datatable'])->name('finance.datatable');
    Route::patch('/finance/{id}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');
    Route::post('/finance', [FinanceController::class, 'store'])->name('finance.store');

    // Historial de Compras (antes de finance/{id} para evitar conflicto de rutas)
    Route::get('/finance/historial-compras/dashboard', [FinanceController::class, 'getDashboardData'])->middleware('can:finance.show')->name('historial-compras.dashboard');
    Route::get('/finance/historial-compras', [FinanceController::class, 'purchaseHistory'])->middleware('can:finance.show')->name('historial-compras.index');
    Route::get('/finance/historial-compras/{customerId}', [FinanceController::class, 'getPurchaseHistoryData'])->middleware('can:finance.show')->name('historial-compras.data');

    Route::get('/finance/{id}', [FinanceController::class, 'show'])->name('finance.show');
    Route::delete('/finance/{id}', [FinanceController::class, 'destroy'])->name('finance.destroy');
    Route::patch('/finance/{id}', [FinanceController::class, 'update'])->name('finance.update');

    //insumos entradas
    Route::resource('insumos_entradas', InsumoEntradaController::class);

    //pagos y facturas 
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    
    Route::prefix('facturas')->group(function () {
    Route::get('/', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/datatable', [FacturaController::class, 'datatable'])->name('facturas.datatable');
    Route::post('/', [FacturaController::class, 'store'])->name('facturas.store');
    Route::get('/{id}', [FacturaController::class, 'show'])->name('facturas.show');
    Route::patch('/{id}', [FacturaController::class, 'update'])->name('facturas.update');
    Route::delete('/{id}', [FacturaController::class, 'destroy'])->name('facturas.destroy');
    });

    Route::prefix('cuentas-por-cobrar')->group(function () {
        Route::get('/', [CuentasPorCobrarController::class, 'index'])->name('cuentas-por-cobrar.index');
        Route::get('/dashboard', [CuentasPorCobrarController::class, 'dashboard'])->name('cuentas-por-cobrar.dashboard');
        Route::get('/clientes', [CuentasPorCobrarController::class, 'clientes'])->name('cuentas-por-cobrar.clientes');
        
        Route::get('/datatable', [CuentasPorCobrarController::class, 'datatable'])->name('cuentas-por-cobrar.datatable');
        Route::get('/export-excel', [CuentasPorCobrarController::class, 'exportExcel'])->name('cuentas-por-cobrar.export-excel');
        Route::post('/{id}/update', [CuentasPorCobrarController::class, 'update'])->name('cuentas-por-cobrar.update');
        Route::post('/{id}/cancel', [CuentasPorCobrarController::class, 'cancel'])->name('cuentas-por-cobrar.cancel');
        
        Route::get('/{id}/payments', [CuentasPorCobrarController::class, 'getPayments'])->name('cuentas-por-cobrar.payments.list');
        Route::post('/{id}/payments', [CuentasPorCobrarController::class, 'addPayment'])->name('cuentas-por-cobrar.payments.add');
    });

    Route::prefix('cuentas-por-pagar')->group(function () {
        Route::get('/', [\App\Http\Controllers\CuentasPorPagarController::class, 'index'])->name('cuentas-por-pagar.index');
        Route::get('/dashboard', [\App\Http\Controllers\CuentasPorPagarController::class, 'dashboard'])->name('cuentas-por-pagar.dashboard');
        Route::get('/facturas', [\App\Http\Controllers\CuentasPorPagarController::class, 'facturas'])->name('cuentas-por-pagar.facturas');
        Route::get('/datatable', [\App\Http\Controllers\CuentasPorPagarController::class, 'datatable'])->name('cuentas-por-pagar.datatable');
        Route::get('/export-excel', [\App\Http\Controllers\CuentasPorPagarController::class, 'exportExcel'])->name('cuentas-por-pagar.export-excel');
        Route::post('/{id}/update', [\App\Http\Controllers\CuentasPorPagarController::class, 'update'])->name('cuentas-por-pagar.update');
        Route::post('/{id}/cancel', [\App\Http\Controllers\CuentasPorPagarController::class, 'cancel'])->name('cuentas-por-pagar.cancel');
        Route::get('/{id}/payments', [\App\Http\Controllers\CuentasPorPagarController::class, 'getPayments'])->name('cuentas-por-pagar.payments.list');
        Route::post('/{id}/payments', [\App\Http\Controllers\CuentasPorPagarController::class, 'addPayment'])->name('cuentas-por-pagar.payments.add');
        Route::post('/{id}/documents', [\App\Http\Controllers\CuentasPorPagarController::class, 'uploadDocuments'])->name('cuentas-por-pagar.documents.upload');
    });

    //production y i+d
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/i+d', [idController::class, 'index'])->name('i+d.index');

    //finance - supplier prices
    Route::get('/precios', [SupplierPriceController::class, 'index'])->name('precios.index');
    Route::post('/precios', [SupplierPriceController::class, 'store'])->name('precios.store');
    Route::post('/precios/update/{id}', [SupplierPriceController::class, 'update'])->name('precios.update');
    Route::post('/precios/delete/{id}', [SupplierPriceController::class, 'destroy'])->name('precios.destroy');
    Route::get('/precios/grafica', [SupplierPriceController::class, 'grafica'])->name('precios.grafica');

    //Fumigaciones
    Route::get('/fumigaciones', [FumigacionController::class, 'index'])->name('fumigaciones.index');
    Route::post('/fumigaciones/store', [FumigacionController::class, 'store'])->name('fumigaciones.store');
    Route::post('/fumigaciones/update/{id}', [FumigacionController::class, 'update'])->name('fumigaciones.update');
    Route::post('/fumigaciones/delete/{id}', [FumigacionController::class, 'destroy'])->name('fumigaciones.destroy');

    // Minutas
    Route::get('/minutas', [MinutaController::class, 'index'])->name('minutas.index');
    Route::get('/get-minutas', [MinutaController::class, 'getMinutas'])->name('minutas.getSuppliers');
    Route::post('/minutas', [MinutaController::class, 'store'])->name('minutas.store'); 
    Route::get('/minutas/{id}', [MinutaController::class, 'show']);                    
    Route::post('/minutas/{id}', [MinutaController::class, 'update']);                 
    Route::delete('/minutas/{id}', [MinutaController::class, 'destroy']);
    Route::get('/minutas/{id}/pdf', [MinutaController::class, 'downloadPDF'])->name('minutas.pdf');

    // Tasks 
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::patch('/tasks/{task}/update-status', [App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'can:admin.dashboard',
    ])->group(function () {
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });

    // Ruta para marcar todas las notificaciones como leídas
    Route::middleware(['auth'])->group(function () {
        Route::post('/notifications/mark-all-read', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        })->name('notifications.markRead');
    });

    // rh
    Route::get('/rh', function () {
        return view('rh');
    })->name('rh.index');

    Route::get('/rh/asistencia', [RhController::class, 'index'])->name('attendance.index');
    Route::post('/rh/asistencia/upload', [RhController::class, 'uploadCsv'])->name('attendance.upload');

    
    // La ruta de la vista principal
    Route::get('/recursos-humanos', [RecursosHumanosController::class, 'index'])->name('expediente.index');

    Route::post('/recursos-humanos/expediente', [RecursosHumanosController::class, 'store'])->name('rh.expediente.store');
    Route::post('/recursos-humanos/expediente/pdf', [RecursosHumanosController::class, 'generarPdfExpediente'])->name('rh.expediente.pdf');
    Route::post('/rh/descripcion-puesto/pdf', [RecursosHumanosController::class, 'descripcionPuestoPdf'])->name('rh.descripcion_puesto.pdf');
    Route::post('/rh/entrevista-terminacion/pdf', [RecursosHumanosController::class, 'entrevistaTerminacionPdf'])->name('rh.entrevista_terminacion.pdf');
    Route::post('/rh/vacaciones/generar-pdf', [RecursosHumanosController::class, 'generarPdfVacaciones'])->name('rh.vacaciones.pdf');
    Route::post('/rh/dnc/generar-pdf', [RecursosHumanosController::class, 'generarPdfDnc'])->name('rh.dnc.pdf');
    Route::post('/rh/practicantes/generar-pdf', [RecursosHumanosController::class, 'generarExpedientePracticantePdf'])->name('rh.practicantes.pdf');
    Route::post('/rh/evaluacion-desempeno/pdf', [RecursosHumanosController::class, 'evaluacionDesempenoPdf'])->name('rh.evaluacion_desempeno.pdf');
    Route::post('/rh/solicitud-personal/pdf', [RecursosHumanosController::class, 'solicitudPersonalPdf'])->name('rh.solicitud_personal.pdf');
    Route::post('/rh/convenio-instituciones/pdf', [RecursosHumanosController::class, 'convenioInstitucionesPdf'])->name('rh.convenio_instituciones.pdf');

    // Clima Laboral
    Route::post('/rh/clima-laboral', [\App\Http\Controllers\ClimaLaboralController::class, 'store'])->name('rh.clima_laboral.store');
    Route::get('/rh/clima-laboral/resultados', [\App\Http\Controllers\ClimaLaboralController::class, 'index'])->name('rh.clima_laboral.resultados');

    // Cursos
    Route::post('/rh/cursos', [\App\Http\Controllers\RecursosHumanosController::class, 'storeCurso'])->name('rh.cursos.store');
    Route::get('/rh/cursos/resultados', [\App\Http\Controllers\RecursosHumanosController::class, 'indexCursos'])->name('rh.cursos.resultados');

    //portal users
    Route::get('admin/get-json-portal-users', [PortalUserController::class, 'getPortalUsers'])
    ->name('admin.portal-users.getPortalUsers');

    Route::resource('admin/portal-users', PortalUserController::class)
        ->except(['show'])
        ->names('admin.portal-users');

    Route::post('admin/sales/{saleId}/upload-docs', [PortalUserController::class, 'uploadDocs'])
        ->name('admin.sales.uploadDocs');

    Route::delete('admin/sales/{saleId}/delete-doc/{type}', [PortalUserController::class, 'deleteDoc'])
        ->name('admin.sales.deleteDoc');

    //documents
    Route::get('/delivery-note/{sale_id}', [PdfController::class, 'makeDeliveryNotePDF'])
        ->name('delivery-note');

    Route::get('/open-file/{fileName}', [FileController::class, 'openFile'])
        ->name('openFile');
        
    Route::get('/download-pdf/{movType}/{movId}', [PdfController::class, 'downloadPDF'])
        ->name('makePDF');

    Route::get('/make-pdf', [PDFController::class, 'make'])
        ->name('makePDF');

    Route::get('/download-temperature-pdf/{week_a}/{week_b}/{year}/{warehouse_id}', [PdfController::class, 'makeTemperaturePDF'])
        ->name('temperature-pdf');

    Route::get('requisition-format/{requisition_id}', [PdfController::class, 'makeRequisitionPDF'])
        ->name('requisition.download');
    
    Route::get('/export-inventory', [XlsController::class, 'inventoryXls'])
        ->name('export-inventory');

    Route::get('/reports-excel-inventory', [XlsController::class, 'reports'])
        ->name('reports');
    /*
    Route::get('/download-quote/{quote_id}', [PdfController::class, 'makeQuotePDF'])
        ->name('quote');
    */
        Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'can:admin.dashboard',
])->group(function () {
    Route::get('complaints', [ComplaintAdminController::class, 'index'])->name('complaints.index');
    Route::delete('complaints/{complaint}', [ComplaintAdminController::class, 'destroy'])->name('complaints.destroy');
});
});
