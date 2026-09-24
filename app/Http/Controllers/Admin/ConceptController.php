<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Concept\ConceptRepository;
use App\Http\Requests\Concept\StoreConceptRequest;
use App\Http\Requests\Concept\UpdateConceptRequest;
use Illuminate\Http\Request;

class ConceptController extends Controller
{
    private ConceptRepository $conceptRepository;

    public function __construct(ConceptRepository $conceptRepository)
    {
        $this->conceptRepository = $conceptRepository;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $concepts = $this->conceptRepository->paginate($search);

        $open = false;
        $create = true;
        $concept = null;

        return view('admin.concepts', compact(
            'concepts',
            'search',
            'open',
            'create',
            'concept'
        ));
    }

    public function create(Request $request)
    {
        $search = $request->get('search');
        $concepts = $this->conceptRepository->paginate($search);

        $open = true;
        $create = true;
        $concept = null;

        return view('admin.concepts', compact(
            'concepts',
            'search',
            'open',
            'create',
            'concept'
        ));
    }

    public function store(StoreConceptRequest $request)
    {
        $this->conceptRepository->create($request->validated());

        return redirect()->route('admin.concepts.index')->with('success', 'Concept created successfully.');
    }

    public function edit($id, Request $request)
    {
        $search = $request->get('search');
        $concept = $this->conceptRepository->find((int) $id);

        if (!$concept) {
            return redirect()->route('admin.concepts.index')->with('error', 'Concepto no encontrado.');
        }

        $concepts = $this->conceptRepository->paginate($search);

        $open = true;
        $create = false;

        return view('admin.concepts', compact(
            'concepts',
            'search',
            'open',
            'create',
            'concept'
        ));
    }

    public function update(UpdateConceptRequest $request, $id)
    {
        $this->conceptRepository->update((int) $id, $request->validated());

        return redirect()->route('admin.concepts.index')->with('success', 'Concept updated successfully.');
    }

    public function destroy($id)
    {
        $conceptId = (int) $id;

        if ($this->conceptRepository->hasClis($conceptId)) {
            return redirect()->route('admin.concepts.index')->with('error', 'No se puede eliminar el concepto porque tiene registros asociados.');
        }

        $this->conceptRepository->delete($conceptId);

        return redirect()->route('admin.concepts.index')->with('success', 'Concept deleted successfully.');
    }
}
