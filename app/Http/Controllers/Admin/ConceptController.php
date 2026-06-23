<?php
/*
controlador
ConceptController
12/08/25
stefany
*/ 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use Illuminate\Http\Request;

class ConceptController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $concepts = Concept::when($search, function($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->paginate(10);

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

        $concepts = Concept::when($search, function($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->paginate(10);

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

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Concept::create(['name' => $request->name]);

        return redirect()->route('admin.concepts.index')->with('success', 'Concept created successfully.');
    }

    public function edit($id, Request $request)
    {
        $search = $request->get('search');

        $concept = Concept::findOrFail($id);

        $concepts = Concept::when($search, function($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->paginate(10);

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

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $concept = Concept::findOrFail($id);
        $concept->update(['name' => $request->name]);

        return redirect()->route('admin.concepts.index')->with('success', 'Concept updated successfully.');
    }

    public function destroy($id)
    {
        $concept = Concept::findOrFail($id);
        $concept->delete();

        return redirect()->route('admin.concepts.index')->with('success', 'Concept deleted successfully.');
    }
}
