<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;  
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
            rescue(fn() => LotRequest::where('status', 'pendiente')->count(), 0, false)
        );
    }
}
