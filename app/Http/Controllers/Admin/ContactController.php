<?php
/*
Controlador: ContactController
13/08/25
stefany
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(10);

        $open = false;
        $create = false;
        $contact = null;

        return view('admin.contacts', compact('contacts', 'open', 'create', 'contact'));
    }

    public function create()
    {
        $contacts = Contact::paginate(10);
        $open = true;
        $create = true;
        $contact = null;

        return view('admin.contacts', compact('contacts', 'open', 'create', 'contact'));
    }

    public function edit($id)
    {
        $contacts = Contact::paginate(10);
        $contact = Contact::findOrFail($id);
        $open = true;
        $create = false;

        return view('admin.contacts', compact('contacts', 'contact', 'open', 'create'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'required|string|max:15',
            'message' => 'required|string|max:255',
        ]);

        Contact::create($request->all());

        return redirect()->route('admin.contacts.index')->with('success', 'Contact created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'required|string|max:15',
            'message' => 'required|string|max:255',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->update($request->all());

        return redirect()->route('admin.contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted successfully.');
    }
}
