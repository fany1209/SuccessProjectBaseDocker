<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fumigacion;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendFumigacionReminder extends Command
{
    // Este es el nombre interno del comando
    protected $signature = 'fumigacion:remind';

    protected $description = 'Revisa si hay fumigaciones en 5 minutos y manda correo';

    public function handle()
    {
        // 1. Buscamos la hora de "dentro de 5 minutos"
        $ahoraMasCinco = Carbon::now()->addMinutes(2)->format('Y-m-d H:i');

        // 2. Buscamos en la base de datos
        $pendientes = Fumigacion::where('estado', 'Pendiente')
            ->whereRaw("DATE_FORMAT(fecha_programada, '%Y-%m-%d %H:%i') = ?", [$ahoraMasCinco])
            ->get();

        if ($pendientes->isEmpty()) {
            $this->info("No hay fumigaciones programadas para las: " . $ahoraMasCinco);
            return;
        }

        foreach ($pendientes as $item) {
            $destinatario = 'fanyhernandez247@gmail.com'; 

            Mail::raw("Recordatorio: Tienes una fumigación programada con {$item->proveedor} a las {$item->fecha_programada}. Método: {$item->metodo_aplicacion}.", function ($message) use ($destinatario) {
                $message->to($destinatario)
                        ->subject('⚠️ AVISO: Fumigación en 5 minutos');
            });

            Log::info("Correo de recordatorio enviado a {$destinatario} para el proveedor {$item->proveedor}");
            $this->info("Recordatorio enviado con éxito.");
        }
    }
}