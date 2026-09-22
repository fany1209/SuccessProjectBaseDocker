<?php

namespace App\Http\Repositories\ClimaLaboral;

use App\Models\ClimaLaboral;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClimaLaboralRepository
{
    protected ClimaLaboral $climaLaboral;

    public function __construct(ClimaLaboral $climaLaboral)
    {
        $this->climaLaboral = $climaLaboral;
    }

    public function all(): Collection
    {
        return $this->climaLaboral
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function hasUserAnswered(int $userId): bool
    {
        return $this->climaLaboral
            ->where('user_id', $userId)
            ->exists();
    }

    public function create(array $data): ClimaLaboral
    {
        return DB::transaction(function () use ($data) {
            return $this->climaLaboral->create($data);
        });
    }
}
