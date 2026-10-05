<?php

namespace App\Http\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderRepository
{
    protected Order $order;
    protected OrderItem $orderItem;
    protected Product $product;
    protected User $user;

    public function __construct(
        Order $order,
        OrderItem $orderItem,
        Product $product,
        User $user
    ) {
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->product = $product;
        $this->user = $user;
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        if (is_dir($base)) {
            return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
        }

        return public_path($subpath);
    }

    public function getIndexData(): array
    {
        return [
            'productos' => $this->product->orderBy('name', 'asc')->get(),
        ];
    }

    public function getData($user)
    {
        $query = $this->order->with(['user:id,name,email', 'items']);

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Sales') && !$user->hasRole('Admin')) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function find(int $id): ?Order
    {
        return $this->order->with(['user:id,name,email', 'items'])->find($id);
    }

    public function isAuthorized(Order $order, $user): bool
    {
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('Sales') && !$user->hasRole('Admin') && (int) $order->user_id !== (int) $user->id) {
            return false;
        }

        return true;
    }

    public function store(array $data, ?UploadedFile $pdfFile, $currentUser): Order
    {
        return DB::transaction(function () use ($data, $pdfFile, $currentUser) {
            $itemsData = [];

            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (!empty($item['producto'])) {
                        $itemsData[] = [
                            'producto' => $item['producto'],
                            'cantidad' => $item['cantidad'] ?? null,
                        ];
                    }
                }
            } elseif (!empty($data['producto'])) {
                $itemsData[] = [
                    'producto' => $data['producto'],
                    'cantidad' => $data['cantidad'] ?? null,
                ];
            }

            if (!empty($itemsData)) {
                $data['producto'] = implode(', ', array_column($itemsData, 'producto'));
                $data['cantidad'] = implode(', ', array_filter(array_column($itemsData, 'cantidad')));
            }

            if (isset($data['documentacion_requerida']) && is_array($data['documentacion_requerida'])) {
                $data['documentacion_requerida'] = implode(', ', $data['documentacion_requerida']);
            }

            if ($pdfFile && $pdfFile->isValid()) {
                $dest = $this->getPublicHtmlPath('orders_pdf');
                if (!file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }
                $fileName = time() . '_' . Str::uuid() . '.pdf';
                $pdfFile->move($dest, $fileName);
                $data['pdf_path'] = $fileName;
            }

            unset($data['items'], $data['pdf_file']);
            $data['user_id'] = $currentUser ? $currentUser->id : 1;

            $order = $this->order->create($data);

            if (!empty($itemsData)) {
                $order->items()->createMany($itemsData);
            }

            try {
                $usersToNotify = $this->user->role(['Admin', 'Quality', 'Warehouse'])->get();
                if ($usersToNotify->isNotEmpty()) {
                    Notification::send($usersToNotify, new NewOrderNotification($order));
                }
            } catch (\Throwable $ignored) {
            }

            return $order->load(['user:id,name,email', 'items']);
        });
    }

    public function update(int $id, array $data, ?UploadedFile $pdfFile, $currentUser): bool
    {
        return DB::transaction(function () use ($id, $data, $pdfFile, $currentUser) {
            $order = $this->order->where('id', $id)->lockForUpdate()->first();
            if (!$order) {
                return false;
            }

            if (!$this->isAuthorized($order, $currentUser)) {
                throw new DomainException('No autorizado', 403);
            }

            $itemsData = [];
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (!empty($item['producto'])) {
                        $itemsData[] = [
                            'producto' => $item['producto'],
                            'cantidad' => $item['cantidad'] ?? null,
                        ];
                    }
                }
            } elseif (!empty($data['producto'])) {
                $itemsData[] = [
                    'producto' => $data['producto'],
                    'cantidad' => $data['cantidad'] ?? null,
                ];
            }

            if (!empty($itemsData)) {
                $data['producto'] = implode(', ', array_column($itemsData, 'producto'));
                $data['cantidad'] = implode(', ', array_filter(array_column($itemsData, 'cantidad')));
            }

            if (isset($data['documentacion_requerida'])) {
                $data['documentacion_requerida'] = is_array($data['documentacion_requerida'])
                    ? implode(', ', $data['documentacion_requerida'])
                    : $data['documentacion_requerida'];
            }

            if ($pdfFile && $pdfFile->isValid()) {
                if ($order->pdf_path) {
                    $oldPath = $this->getPublicHtmlPath('orders_pdf/' . $order->pdf_path);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $dest = $this->getPublicHtmlPath('orders_pdf');
                if (!file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }
                $fileName = time() . '_' . Str::uuid() . '.pdf';
                $pdfFile->move($dest, $fileName);
                $data['pdf_path'] = $fileName;
            }

            unset($data['items'], $data['pdf_file']);

            $order->update($data);

            if (!empty($itemsData)) {
                $order->items()->delete();
                $order->items()->createMany($itemsData);
            }

            return true;
        });
    }

    public function updateStatus(int $id, array $statusData, $currentUser): bool
    {
        return DB::transaction(function () use ($id, $statusData, $currentUser) {
            $order = $this->order->where('id', $id)->lockForUpdate()->first();
            if (!$order) {
                return false;
            }

            $fieldsToUpdate = [];

            if (array_key_exists('estatus_almacen', $statusData) && !is_null($statusData['estatus_almacen'])) {
                if (!$currentUser || (!method_exists($currentUser, 'hasRole') || (!$currentUser->hasRole('Warehouse') && !$currentUser->hasRole('Admin')))) {
                    throw new DomainException('No autorizado para modificar estatus de almacén', 403);
                }
                $fieldsToUpdate['estatus_almacen'] = $statusData['estatus_almacen'];
            }

            if (array_key_exists('estatus_calidad', $statusData) && !is_null($statusData['estatus_calidad'])) {
                if (!$currentUser || (!method_exists($currentUser, 'hasRole') || (!$currentUser->hasRole('Quality') && !$currentUser->hasRole('Admin')))) {
                    throw new DomainException('No autorizado para modificar estatus de calidad', 403);
                }
                $fieldsToUpdate['estatus_calidad'] = $statusData['estatus_calidad'];
            }

            if (array_key_exists('estatus_administrativo', $statusData) && !is_null($statusData['estatus_administrativo'])) {
                if (!$currentUser || (!method_exists($currentUser, 'hasRole') || !$currentUser->hasRole('Admin'))) {
                    throw new DomainException('No autorizado para modificar estatus administrativo', 403);
                }
                $fieldsToUpdate['estatus_administrativo'] = $statusData['estatus_administrativo'];
            }

            if (empty($fieldsToUpdate)) {
                throw new DomainException('No se especificó ningún campo de estatus válido para actualizar', 422);
            }

            return (bool) $order->update($fieldsToUpdate);
        });
    }

    public function delete(int $id, $currentUser): bool
    {
        return DB::transaction(function () use ($id, $currentUser) {
            $order = $this->order->where('id', $id)->lockForUpdate()->first();
            if (!$order) {
                return false;
            }

            if (!$this->isAuthorized($order, $currentUser)) {
                throw new DomainException('No autorizado', 403);
            }

            if ($order->pdf_path) {
                $path = $this->getPublicHtmlPath('orders_pdf/' . $order->pdf_path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }

            $order->items()->delete();

            return (bool) $order->delete();
        });
    }
}
