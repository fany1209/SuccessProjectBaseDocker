{{-- contacts
13/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Contacts')

@section('content_header')
    <h1 class="badge badge-success">Contacts</h1>
@stop

@section('content')
<div>
    {{-- Formulario de creación/edición --}}
    @if ($open)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Contact' : 'Edit Contact' }}</b></h5>
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create 
            ? route('admin.contacts.store') 
            : (isset($contact) ? route('admin.contacts.update', $contact->contact_id) : '#') }}">
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $contact->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $contact->phone ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" name="message" required>{{ old('message', $contact->message ?? '') }}</textarea>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Tabla de contactos --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Since</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $c)
                <tr class="{{ $c->read_at ? '' : 'table-warning' }}">
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->message }}</td>
                    <td>{{ $c->created_at ?? '-' }}</td>
                    <td>
                        @if($c->read_at)
                            <span class="badge badge-success"><i class="fas fa-check"></i> Leído</span>
                        @else
                            <label class="d-flex align-items-center gap-1 mb-0" style="cursor:pointer;">
                                <input type="checkbox" class="mark-read-table" data-id="{{ $c->contact_id }}">
                                <span class="text-muted" style="font-size:11px;">Marcar leído</span>
                            </label>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.contacts.edit', $c->contact_id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.contacts.destroy', $c->contact_id) }}" method="POST" class="d-inline delete-contact-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm ml-1"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-2">
            {{ $contacts->appends(request()->except('page'))->links('bootstrap-links') }}
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    @if(session('success'))
        Swal.fire({icon: 'success', title: 'Success!', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false});
    @endif

    @if ($errors->any())
        let errorMessages = `@foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach`;
        Swal.fire({icon: 'error', title: 'Form errors', html: errorMessages});
    @endif

    $('.delete-contact-form').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) { form.submit(); }
        });
    });

    // Marcar contacto como leído desde la tabla
    $('.mark-read-table').on('change', function() {
        if (!this.checked) return;
        const checkbox = $(this);
        const contactId = checkbox.data('id');
        checkbox.prop('disabled', true);
        
        $.ajax({
            url: '/admin/contacts/' + contactId + '/mark-read',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function() {
                const row = checkbox.closest('tr');
                row.removeClass('table-warning');
                checkbox.closest('td').html('<span class="badge badge-success"><i class="fas fa-check"></i> Leído</span>');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Marcado como leído', showConfirmButton: false, timer: 1500 });
            }
        });
    });
});
</script>
@stop
