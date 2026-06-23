<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerRetentionAlert;
use Illuminate\Support\Facades\DB;

class CheckCustomerRetentionAlerts extends Command
{
    protected $signature = 'sales:check-retention';

    protected $description = 'Verifica si hay clientes frecuentes que han dejado de comprar en el último mes.';

    public function handle()
    {
        $this->info('Iniciando verificación de retención de clientes...');

        $now = Carbon::now();
        // Definimos las 3 ventanas de 30 días
        $startPeriod1 = $now->copy()->subDays(30)->startOfDay(); // Hace 30 días hasta hoy (sin compras)
        $startPeriod2 = $now->copy()->subDays(60)->startOfDay(); // Hace 60 días hasta hace 30 días (con compras)
        $startPeriod3 = $now->copy()->subDays(90)->startOfDay(); // Hace 90 días hasta hace 60 días (con compras)

        $customers = Customer::all();
        $salesUsers = User::role('Sales')->get();

        if ($salesUsers->isEmpty()) {
            $this->error('No se encontraron usuarios con el rol "Sales". Cancelando.');
            return 1;
        }

        $alertsSent = 0;

        foreach ($customers as $customer) {
            // Contamos las ventas en cada periodo
            $salesPeriod1 = Sale::where('customer_id', $customer->customer_id)
                ->where('date', '>=', $startPeriod1->toDateString())
                ->count();

            $salesPeriod2 = Sale::where('customer_id', $customer->customer_id)
                ->whereBetween('date', [$startPeriod2->toDateString(), $startPeriod1->copy()->subDay()->toDateString()])
                ->count();

            $salesPeriod3 = Sale::where('customer_id', $customer->customer_id)
                ->whereBetween('date', [$startPeriod3->toDateString(), $startPeriod2->copy()->subDay()->toDateString()])
                ->count();

            // Condición: 2 meses anteriores CON compras (consecutivos), último mes SIN compras
            if ($salesPeriod1 == 0 && $salesPeriod2 > 0 && $salesPeriod3 > 0) {
                
                // Evitamos enviar alertas duplicadas
                // Checamos si ya mandamos esta alerta en los últimos 30 días para no hacer spam si se corre diario
                $recentAlert = DB::table('notifications')
                    ->where('type', 'App\Notifications\CustomerRetentionAlert')
                    ->where('created_at', '>=', $now->copy()->subDays(30))
                    ->where('data', 'like', '%"customer_id":' . $customer->customer_id . '%')
                    ->exists();

                if (!$recentAlert) {
                    $lastSale = Sale::where('customer_id', $customer->customer_id)
                        ->orderBy('date', 'desc')
                        ->first();

                    $lastSaleDate = $lastSale ? $lastSale->date : 'Desconocida';

                    // Enviar notificación a todos los de Ventas
                    Notification::send($salesUsers, new CustomerRetentionAlert($customer, $lastSaleDate));
                    
                    $this->line('Alerta generada para el cliente: ' . $customer->name);
                    $alertsSent++;
                }
            }
        }

        $this->info("Verificación completada. Alertas enviadas: {$alertsSent}");

        return 0;
    }
}
