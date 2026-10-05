<?php

namespace App\Http\Repositories\Prospect;

use App\Models\Prospect;
use App\Models\Sector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProspectRepository
{
    protected Prospect $prospect;
    protected Sector $sector;

    public function __construct(Prospect $prospect, Sector $sector)
    {
        $this->prospect = $prospect;
        $this->sector = $sector;
    }

    public function getIndexData(): array
    {
        return [
            'sectors' => $this->sector->orderBy('name', 'asc')->get(),
        ];
    }

    public function getProspects(array $filters = []): Collection
    {
        $sector = $filters['sector'] ?? null;
        $search = $filters['search'] ?? null;
        $city = $filters['city'] ?? null;
        $state = $filters['state'] ?? null;
        $name = $filters['name'] ?? null;
        $rfc = $filters['rfc'] ?? null;

        $query = DB::table('prospects')
            ->join('sectors', 'sectors.sector_id', '=', 'prospects.sector_id')
            ->select(
                'prospects.prospect_id',
                'sectors.name as sector',
                'prospects.name',
                'prospects.phone',
                'prospects.email',
                'prospects.rfc',
                DB::raw("CONCAT_WS(', ', NULLIF(prospects.address, ''), NULLIF(prospects.district, ''), NULLIF(prospects.city, ''), NULLIF(prospects.state, '')) as address")
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sectors.name', 'like', '%' . $search . '%')
                    ->orWhere('prospects.name', 'like', '%' . $search . '%')
                    ->orWhere('prospects.phone', 'like', '%' . $search . '%')
                    ->orWhere('prospects.email', 'like', '%' . $search . '%')
                    ->orWhere('prospects.rfc', 'like', '%' . $search . '%');
            });
        }

        if (!empty($sector)) {
            $query->where('prospects.sector_id', $sector);
        }

        if (!empty($city)) {
            $query->where('prospects.city', 'like', '%' . $city . '%');
        }

        if (!empty($state)) {
            $query->where('prospects.state', 'like', '%' . $state . '%');
        }

        if (!empty($name)) {
            $query->where('prospects.name', 'like', '%' . $name . '%');
        }

        if (!empty($rfc)) {
            $query->where('prospects.rfc', 'like', '%' . $rfc . '%');
        }

        $user = auth()->user();
        $canUpdate = $user ? $user->can('prospects.update') : false;
        $canDelete = $user ? $user->can('prospects.delete') : false;

        $prospects = $query->orderBy('prospects.prospect_id', 'desc')->get();

        return $prospects->map(function ($row) use ($canUpdate, $canDelete) {
            return [
                'prospect_id' => $row->prospect_id,
                'sector'      => $row->sector,
                'name'        => $row->name,
                'phone'       => $row->phone,
                'email'       => $row->email,
                'rfc'         => $row->rfc,
                'address'     => $row->address,
                'canUpdate'   => $canUpdate,
                'canDelete'   => $canDelete,
            ];
        });
    }

    public function find(int $id): ?Prospect
    {
        return $this->prospect->with('sector')->where('prospect_id', $id)->first();
    }

    public function create(array $data): Prospect
    {
        return DB::transaction(function () use ($data) {
            return $this->prospect->create([
                'sector_id' => $data['sector_id'],
                'name'      => $data['name'],
                'phone'     => $data['phone'] ?? null,
                'email'     => $data['email'] ?? null,
                'rfc'       => $data['rfc'] ?? null,
                'state'     => $data['state'] ?? null,
                'city'      => $data['city'] ?? null,
                'district'  => $data['district'] ?? null,
                'address'   => $data['address'] ?? null,
            ]);
        });
    }

    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $prospect = $this->prospect->where('prospect_id', $id)->lockForUpdate()->first();
            if (!$prospect) {
                return false;
            }

            return $prospect->update([
                'sector_id' => $data['sector_id'],
                'name'      => $data['name'],
                'phone'     => $data['phone'] ?? null,
                'email'     => $data['email'] ?? null,
                'rfc'       => $data['rfc'] ?? null,
                'state'     => $data['state'] ?? null,
                'city'      => $data['city'] ?? null,
                'district'  => $data['district'] ?? null,
                'address'   => $data['address'] ?? null,
            ]);
        });
    }

    public function hasRelatedRecords(int $id): bool
    {
        return DB::table('sales')->where('prospect_id', $id)->exists();
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $prospect = $this->prospect->where('prospect_id', $id)->lockForUpdate()->first();
            if (!$prospect) {
                return false;
            }

            if ($this->hasRelatedRecords($id)) {
                throw new \DomainException('No se puede eliminar el prospecto porque tiene ventas asociadas.');
            }

            return (bool) $prospect->delete();
        });
    }
}
