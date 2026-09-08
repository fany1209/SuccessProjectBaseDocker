<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 
use App\Models\User;
use App\Models\Product;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $productos = Product::orderBy('name', 'asc')->get();
        return view('orders', compact('productos'));
    }

    public function getData()
    {
        $user = auth()->user();
        $query = Order::with(['user:id,name,email', 'items']);

        // Los usuarios con rol Sales (que no sean Admin) solo pueden ver los pedidos que ellos mismos hayan ingresado
        if ($user->hasRole('Sales') && !$user->hasRole('Admin')) {
            $query->where('user_id', $user->id);
        }

        return response()->json([
            'data' => $query->orderBy('id', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'año'      => 'required|integer',
                'semana'   => 'required|integer',
                'empresa'  => 'required|string|max:255',
                'po'       => 'nullable|string|max:100',
                'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
                'items'    => 'nullable|array',
                'items.*.producto' => 'nullable|string|max:255',
                'items.*.cantidad' => 'nullable|string|max:100',
            ]);

            $data = $request->except(['items']);
            $data['user_id'] = auth()->id();

            // Soportar items múltiples o fallback a campo individual
            $itemsData = [];
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['producto'])) {
                        $itemsData[] = [
                            'producto' => $item['producto'],
                            'cantidad' => $item['cantidad'] ?? null,
                        ];
                    }
                }
            } elseif ($request->filled('producto')) {
                $itemsData[] = [
                    'producto' => $request->input('producto'),
                    'cantidad' => $request->input('cantidad'),
                ];
            }

            // Resumen de producto/cantidad para compatibilidad con vistas legacy
            if (!empty($itemsData)) {
                $data['producto'] = implode(', ', array_column($itemsData, 'producto'));
                $data['cantidad'] = implode(', ', array_filter(array_column($itemsData, 'cantidad')));
            }

            if ($request->has('documentacion_requerida')) {
                $data['documentacion_requerida'] = implode(', ', $request->input('documentacion_requerida'));
            }

            if ($request->hasFile('pdf_file')) {
                $file = $request->file('pdf_file');
                $destination = public_path('orders_pdf');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $fileName = time() . '_' . Str::uuid() . '.pdf';
                $file->move($destination, $fileName);
                $data['pdf_path'] = $fileName;
            }

            $order = DB::transaction(function() use ($data, $itemsData) {
                $order = Order::create($data);
                if (!empty($itemsData)) {
                    $order->items()->createMany($itemsData);
                }
                return $order;
            });

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
        $order = Order::with(['user:id,name,email', 'items'])->findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('Sales') && !$user->hasRole('Admin') && $order->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'año'      => 'nullable|integer',
                'semana'   => 'nullable|integer',
                'empresa'  => 'nullable|string|max:255',
                'po'       => 'nullable|string|max:100',
                'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
                'items'    => 'nullable|array',
                'items.*.producto' => 'nullable|string|max:255',
                'items.*.cantidad' => 'nullable|string|max:100',
            ]);

            $order = Order::findOrFail($id);
            $user = auth()->user();

            if ($user->hasRole('Sales') && !$user->hasRole('Admin') && $order->user_id !== $user->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $data = $request->except(['items']);

            // Soportar items múltiples o fallback
            $itemsData = [];
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['producto'])) {
                        $itemsData[] = [
                            'producto' => $item['producto'],
                            'cantidad' => $item['cantidad'] ?? null,
                        ];
                    }
                }
            } elseif ($request->filled('producto')) {
                $itemsData[] = [
                    'producto' => $request->input('producto'),
                    'cantidad' => $request->input('cantidad'),
                ];
            }

            if (!empty($itemsData)) {
                $data['producto'] = implode(', ', array_column($itemsData, 'producto'));
                $data['cantidad'] = implode(', ', array_filter(array_column($itemsData, 'cantidad')));
            }

            if ($request->has('documentacion_requerida')) {
                $data['documentacion_requerida'] = implode(', ', $request->input('documentacion_requerida'));
            } else {
                $data['documentacion_requerida'] = null;
            }

            if ($request->hasFile('pdf_file')) {
                if ($order->pdf_path && file_exists(public_path('orders_pdf/' . $order->pdf_path))) {
                    @unlink(public_path('orders_pdf/' . $order->pdf_path));
                }

                $file = $request->file('pdf_file');
                $destination = public_path('orders_pdf');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $fileName = time() . '_' . Str::uuid() . '.pdf';
                $file->move($destination, $fileName);
                $data['pdf_path'] = $fileName;
            }

            DB::transaction(function() use ($order, $data, $itemsData) {
                $order->update($data);
                if (!empty($itemsData)) {
                    $order->items()->delete();
                    $order->items()->createMany($itemsData);
                }
            });

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
            $user = auth()->user();

            if ($user->hasRole('Sales') && !$user->hasRole('Admin') && $order->user_id !== $user->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            
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