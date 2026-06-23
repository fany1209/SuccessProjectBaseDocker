<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 
use App\Models\User;
use App\Models\Product;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    public function index()
    {
        $productos = Product::orderBy('name', 'asc')->get();
        return view('orders', compact('productos'));
    }

    public function getData()
    {
        return response()->json([
            'data' => Order::all()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'año'     => 'required|integer',
                'semana'  => 'required|integer',
                'empresa' => 'required|string|max:255',
                'po'      => 'nullable|string|max:100',
                'pdf_file' => 'nullable|mimes:pdf|max:10240',
            ]);

            $data = $request->all();

            if ($request->has('documentacion_requerida')) {
                $data['documentacion_requerida'] = implode(', ', $request->input('documentacion_requerida'));
            }

            if ($request->hasFile('pdf_file')) {
                $file = $request->file('pdf_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('orders_pdf'), $fileName);
                $data['pdf_path'] = $fileName;
            }

            $order = Order::create($data);

            $usersToNotify = User::role(['Admin', 'Quality', 'Warehouse'])->get();
            if ($usersToNotify->count() > 0) {
                Notification::send($usersToNotify, new NewOrderNotification($order));
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Pedido registrado y notificado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al guardar pedido: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id) 
    {
        return response()->json(Order::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $data = $request->all();

            if ($request->has('documentacion_requerida')) {
                $data['documentacion_requerida'] = implode(', ', $request->input('documentacion_requerida'));
            } else {
                $data['documentacion_requerida'] = null;
            }

            if ($request->hasFile('pdf_file')) {
                if ($order->pdf_path && file_exists(public_path('orders_pdf/' . $order->pdf_path))) {
                    unlink(public_path('orders_pdf/' . $order->pdf_path));
                }

                $file = $request->file('pdf_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('orders_pdf'), $fileName);
                $data['pdf_path'] = $fileName;
            }

            $order->update($data);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error("Error en Update: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth()->user();

            if ($request->has('estatus_almacen') && !$user->hasRole('Warehouse')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            if ($request->has('estatus_calidad') && !$user->hasRole('Quality')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            if ($request->has('estatus_administrativo') && !$user->hasRole('Admin')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $order->update($request->all());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            if ($order->pdf_path && file_exists(public_path('orders_pdf/' . $order->pdf_path))) {
                unlink(public_path('orders_pdf/' . $order->pdf_path));
            }

            $order->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}