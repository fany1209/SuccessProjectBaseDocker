<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\PendingPaymentAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckPendingPaymentsAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check-pending-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for sales with pending payments that have exceeded their credit term and notify relevant roles.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sales = Sale::where('payment_status', 'PENDING')
            ->where('sale_type', 'Credit')
            ->whereNotNull('customer_id') // only customers, prospects shouldn't be here
            ->with('customer')
            ->get();

        $lateSales = [];
        $today = Carbon::today();

        foreach ($sales as $sale) {
            $termStr = $sale->term ?? '30';
            preg_match('/\d+/', $termStr, $matches);
            $days = !empty($matches) ? (int)$matches[0] : 30; // default 30 days if no number found

            $dueDate = Carbon::parse($sale->date)->addDays($days);

            if ($today->gt($dueDate)) {
                $lateSales[] = [
                    'folio' => $sale->folio,
                    'customer' => $sale->customer ? $sale->customer->name : 'Unknown',
                    'due_date' => $dueDate->format('Y-m-d'),
                    'days_late' => $dueDate->diffInDays($today),
                    'sale_id' => $sale->sale_id
                ];
            }
        }

        if (count($lateSales) > 0) {
            // Get users with roles: Sales, Finance, Human Resources, Admin
            $users = User::role(['Sales', 'Finance', 'Human Resources', 'Admin'])->get(); 
            
            foreach ($users as $user) {
                // Delete old pending payment alerts so they don't stack up
                $user->notifications()->where('data->type', 'pending_payments_alert')->delete();
                $user->notify(new PendingPaymentAlert($lateSales));
            }

            $this->info(count($lateSales) . ' late pending payments found. Notifications sent.');
        } else {
            // If no late sales, delete existing alerts
            $users = User::all();
            foreach($users as $user){
                $user->notifications()->where('data->type', 'pending_payments_alert')->delete();
            }
            $this->info('No late pending payments found.');
        }
    }
}
