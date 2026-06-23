<?php
/*
contacto
21/07/25
stefany 
Fecha de actualización: 17/12/2025
Actualizado por Jacob
*/
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:200',
        'phone' => 'required|string|max:15',
        'message' => 'required|string|max:255',
        'email' => 'required|string|max:2255',
    ]);

    Contact::create($validated);

    return redirect()->back()->with('success', 'Mensaje enviado exitosamente.');}

}
