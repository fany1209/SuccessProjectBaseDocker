@extends('adminlte::page')

@section('title', 'Complaints')

@section('content')
@section('content_header')
  <h1 class="badge badge-success">Complaints</h1>
@stop

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th style="width:110px;">Fecha</th>
        <th style="width:120px;">Tipo</th>
        <th>Motivos</th>
        <th>Descripción</th>
        <th style="width:120px;">Creado</th>
        <th style="width:120px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($complaints as $c)
        @php
          $motivos = is_array($c->motivos) ? $c->motivos : (json_decode($c->motivos ?? '[]', true) ?: []);
          $badges  = collect($motivos)->map(fn($m) =>
            '<span class="badge bg-secondary me-1 mb-1">'.e(str_replace('_',' ', $m)).'</span>'
          )->implode(' ');
        @endphp
        <tr>
          <td>{{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') }}</td>
          <td><span class="badge bg-info text-dark">{{ ucfirst($c->tipo) }}</span></td>
          <td>{!! $badges ?: '—' !!}</td>
          <td class="text-break" style="white-space:pre-wrap;">{!! nl2br(e($c->descripcion)) !!}</td>
          <td>{{ optional($c->created_at)->format('d/m/Y H:i') }}</td>
          <td>
            <form action="{{ route('complaints.destroy', $c->id) }}" method="POST" class="d-inline delete-form">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm" type="submit" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">Sin resultados</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-2">
    {{ $complaints->withQueryString()->links() }}
  </div>
</div>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
  $('.delete-form').on('submit', function(e){
    e.preventDefault();
    const form = this;
    Swal.fire({
      title: '¿Eliminar registro?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((r)=>{ if(r.isConfirmed) form.submit(); });
  });
});
</script>
@stop