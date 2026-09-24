<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contact\ContactRepository;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Resources\Contact\ContactResource;
use App\Traits\UtilResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactoController extends Controller
{
    private UtilResponse $utilResponse;
    private ContactRepository $contactRepository;

    public function __construct(UtilResponse $utilResponse, ContactRepository $contactRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->contactRepository = $contactRepository;
    }

    public function store(StoreContactRequest $request)
    {
        // Honeypot check: Si bot_check tiene valor, descartar silenciosamente.
        if ($request->filled('bot_check')) {
            Log::info('Spam bot detectado en formulario de contacto', [
                'ip' => $request->ip(),
                'payload' => $request->except(['bot_check']),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(null, 'Mensaje enviado exitosamente.');
            }

            return redirect()->back()->with('success', 'Mensaje enviado exitosamente.');
        }

        try {
            $data = $request->safe()->except(['bot_check']);
            $contact = $this->contactRepository->create($data);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ContactResource($contact),
                    'Mensaje enviado exitosamente.',
                    201
                );
            }

            return redirect()->back()->with('success', 'Mensaje enviado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error procesando formulario de contacto', [
                'error' => $e->getMessage(),
                'payload' => $request->except(['bot_check']),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error al enviar el mensaje. Por favor intente nuevamente.', 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al enviar el mensaje.')->withInput();
        }
    }
}
