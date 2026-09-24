<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Contact\ContactRepository;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function index(Request $request)
    {
        $contacts = $this->contactRepository->paginate($request->search);

        $open = false;
        $create = false;
        $contact = null;

        return view('admin.contacts', compact('contacts', 'open', 'create', 'contact'));
    }

    public function create()
    {
        $contacts = $this->contactRepository->paginate();
        $open = true;
        $create = true;
        $contact = null;

        return view('admin.contacts', compact('contacts', 'open', 'create', 'contact'));
    }

    public function edit($id)
    {
        $contacts = $this->contactRepository->paginate();
        $contact = $this->contactRepository->find((int) $id);

        if (!$contact) {
            return redirect()->route('admin.contacts.index')->with('error', 'Contacto no encontrado.');
        }

        $open = true;
        $create = false;

        return view('admin.contacts', compact('contacts', 'contact', 'open', 'create'));
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->safe()->except(['bot_check']);
        $this->contactRepository->create($data);

        return redirect()->route('admin.contacts.index')->with('success', 'Contact created successfully.');
    }

    public function update(UpdateContactRequest $request, $id)
    {
        $this->contactRepository->update((int) $id, $request->validated());

        return redirect()->route('admin.contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy($id)
    {
        $this->contactRepository->delete((int) $id);

        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function markAsRead($id)
    {
        $this->contactRepository->markAsRead((int) $id);

        return response()->json(['success' => true]);
    }
}
