<?php

namespace App\Http\Repositories\Contact;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ContactRepository
{
    protected Contact $model;

    public function __construct(Contact $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderByDesc('created_at')->get();
    }

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->when($search, function ($query, $search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('created_at')
        ->paginate($perPage);
    }

    public function find(int $id): ?Contact
    {
        return $this->model->find($id);
    }

    public function create(array $data): Contact
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(int $id, array $data): ?Contact
    {
        return DB::transaction(function () use ($id, $data) {
            $contact = $this->model->where('contact_id', $id)->lockForUpdate()->first();

            if (!$contact) {
                return null;
            }

            $contact->update($data);

            return $contact;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $contact = $this->model->where('contact_id', $id)->lockForUpdate()->first();

            if (!$contact) {
                return false;
            }

            return (bool) $contact->delete();
        });
    }

    public function markAsRead(int $id): ?Contact
    {
        return DB::transaction(function () use ($id) {
            $contact = $this->model->where('contact_id', $id)->lockForUpdate()->first();

            if (!$contact) {
                return null;
            }

            $contact->update(['read_at' => now()]);

            return $contact;
        });
    }
}
