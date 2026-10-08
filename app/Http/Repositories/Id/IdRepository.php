<?php

namespace App\Http\Repositories\Id;

class IdRepository
{
    public function getModuleStatus(): array
    {
        return [
            'name'        => 'Investigación y Desarrollo (I+D)',
            'status'      => 'under_construction',
            'initialized' => true,
        ];
    }
}
