<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema; // <-- ¡Asegúrate de agregar esta línea arriba!   
use App\Models\LotRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
  
public function boot(): void
{
    // Solo ejecutar esto si NO estamos en la consola (Artisan)
    if (! app()->runningInConsole()) {
        $pendingRequests = \App\Models\LotRequest::where('status', 'pendiente')->count();
        \Illuminate\Support\Facades\View::share('pendingRequests', $pendingRequests);
    }
}
}
