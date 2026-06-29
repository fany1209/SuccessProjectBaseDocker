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
  
    public function boot()
        {
            View::share(
                'pendingLotRequests',
                LotRequest::where('status', 'pendiente')->count()
            );
        }
}
