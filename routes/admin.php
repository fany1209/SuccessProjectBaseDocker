<?php

use App\Http\Controllers\Admin\ConceptController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProspectsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SalesStatusController;
use App\Http\Controllers\Admin\SectorController;
use App\Http\Controllers\Admin\SuppliersController;
use App\Http\Controllers\Admin\TrailerController;
use App\Http\Controllers\Admin\TransportLineController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\WarehousesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComplaintAdminController;
use App\Http\Controllers\Admin\TweakAdminController;

//Admin
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'can:admin.dashboard',
])->group(function () {
    
    Route::resource('employees', EmployeeController::class);
    
    Route::resource('users', UserController::class);
    
    Route::resource('roles', RoleController::class);
    
    Route::resource('permissions', PermissionController::class);
    
    Route::resource('products', ProductsController::class);
    
    Route::resource('locations', LocationController::class);
    
    Route::resource('concepts', ConceptController::class);
    
    Route::resource('transport-lines', TransportLineController::class);
    
    Route::resource('trailers', TrailerController::class);
    
    Route::resource('vehicles', VehicleController::class);
    
    Route::resource('sectors', SectorController::class);
    
    Route::resource('suppliers', SuppliersController::class);
    
    Route::resource('customers', CustomersController::class);
    
    Route::resource('prospects', ProspectsController::class);
    
    Route::resource('contacts', ContactController::class);
    
    Route::resource('sales_status', SalesStatusController::class);
    
    Route::resource('sales', SaleController::class);
    
    Route::resource('warehouses', WarehousesController::class);

    Route::resource('tweaks', TweakAdminController::class)->only(['index']);

    Route::resource('categories/products', CategoryController::class)->names('categories.products');

    Route::prefix('users/{user}')->name('users.')->group(function () {
        Route::delete('photo', [UserController::class, 'removePhoto'])->name('removePhoto');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::delete('products/image/{id}', [ProductsController::class, 'deleteImage'])
        ->whereNumber('id')
        ->name('products.deleteImage');

    Route::delete('products/file/{id}', [ProductsController::class, 'deleteFile'])
        ->whereNumber('id')
        ->name('products.deleteFile');

    Route::get('warehouses/{warehouse}/modal', [WarehousesController::class, 'modalForm'])
        ->name('warehouses.modal');

    Route::post('warehouses/{warehouse}/generate-file', [WarehousesController::class, 'generateFile'])
        ->name('warehouses.generateFile');

    Route::get('complaints', [ComplaintAdminController::class, 'index'])->name('complaints.index');
    Route::delete('complaints/{complaint}', [ComplaintAdminController::class, 'destroy'])->name('complaints.destroy');

    // Marcar mensaje de contacto como leído
    Route::post('contacts/{contact}/mark-read', [ContactController::class, 'markAsRead'])->name('contacts.markRead');
});