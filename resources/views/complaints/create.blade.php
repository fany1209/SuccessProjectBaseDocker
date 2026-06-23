@extends('layouts.app')
@section('content')

  <body class="bg-light">
    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 m-0">Queremos escucharte</h1>
        <a href="{{ url('main-menu') }}" class="btn btn-outline-secondary btn-sm">Regresar</a>
      </div>

      <div class="alert alert-success py-2 px-3 mb-2">
        “Tu voz es importante para mejorar. Puedes compartir tus comentarios con total tranquilidad, 
        ya que esta encuesta es <strong>totalmente anónima</strong>.”
      </div>

      <div class="alert alert-warning py-2 px-3 mb-0">
        “Tu seguridad y confianza son muy importantes para nosotros. 
        Si has presenciado o vivido una situación inapropiada, puedes 
        <strong>presentar una denuncia de forma confidencial y anónima</strong>.”
      </div>
    </div>

    @if (session('ok'))
      <div class="alert alert-success">Tu experiencia se envió correctamente.</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Corrige los siguientes campos:</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" id="complaint-form">
          @csrf

          {{-- ====== FECHA ====== --}}
          <div class="row g-3 mb-3 justify-content-end">
            <div class="col-12 col-md-4">
              <label class="form-label">Fecha de llenado <span class="text-danger">*</span></label>
              <input type="date" name="fecha" class="form-control" value="{{ old('fecha') }}" required>
              @error('fecha') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
          </div>

        {{-- ====== TIPO DE SOLICITUD ====== --}}
        <div class="border rounded-3 p-3 mb-3">
            <h2 class="h6 fw-semibold mb-3">Tipo de solicitud</h2>
            @php $tipoOld = old('tipo'); @endphp
            <div class="row row-cols-1 row-cols-md-2 g-2">
                <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_peticion" value="peticion" {{ $tipoOld==='peticion' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="tipo_peticion">Petición</label>
                </div>
                </div>
                <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_queja" value="queja" {{ $tipoOld==='queja' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="tipo_queja">Queja</label>
                </div>
                </div>
                <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_reclamo" value="reclamo" {{ $tipoOld==='reclamo' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="tipo_reclamo">Reclamo</label>
                </div>
                </div>
                <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_sugerencia" value="sugerencia" {{ $tipoOld==='sugerencia' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="tipo_sugerencia">Sugerencia</label>
                </div>
                </div>
                <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_felicitacion" value="felicitacion" {{ $tipoOld==='felicitacion' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="tipo_felicitacion">Felicitación</label>
                </div>
                </div>
                <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_denuncia" value="denuncia" {{ $tipoOld==='denuncia' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="tipo_denuncia">Denuncia</label>
                </div>
                </div>
            </div>
            @error('tipo') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
        </div>

        @php $motivosOld = old('motivos', []); @endphp
        <div class="border rounded-3 p-3 mb-3">
          <h2 class="h6 fw-semibold mb-3">Motivo</h2>

          <div class="row row-cols-1 row-cols-md-2 g-2">
            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_trato" value="trato_personal"
                      {{ in_array('trato_personal', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_trato">Trato del personal</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_tiempos" value="tiempos_respuesta"
                      {{ in_array('tiempos_respuesta', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_tiempos">Tiempos de respuesta o atención</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_condiciones" value="condiciones_trabajo"
                      {{ in_array('condiciones_trabajo', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_condiciones">Condiciones de trabajo</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_procesos" value="procesos_internos"
                      {{ in_array('procesos_internos', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_procesos">Procesos internos (burocracia, lentitud, confusión)</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_comunicacion" value="comunicacion_interna"
                      {{ in_array('comunicacion_interna', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_comunicacion">Comunicación interna</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_instalaciones" value="instalaciones_equipo"
                      {{ in_array('instalaciones_equipo', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_instalaciones">Instalaciones</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_seguridad" value="seguridad_higiene"
                      {{ in_array('seguridad_higiene', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_seguridad">Seguridad e higiene</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_politicas" value="cumplimiento_politicas"
                      {{ in_array('cumplimiento_politicas', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_politicas">Cumplimiento de políticas</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_liderazgo" value="liderazgo_supervision"
                      {{ in_array('liderazgo_supervision', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_liderazgo">Liderazgo o supervisión</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_apoyo" value="falta_apoyo_recursos"
                      {{ in_array('falta_apoyo_recursos', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_apoyo">Falta de apoyo</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_discriminacion" value="discriminacion_mal_ambiente"
                      {{ in_array('discriminacion_mal_ambiente', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_discriminacion">Discriminación</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_sugerencia" value="sugerencia_mejora"
                      {{ in_array('sugerencia_mejora', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_sugerencia">Sugerencia de mejora</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_ambiente_laboral" value="ambiente_laboral"
                      {{ in_array('ambiente_laboral', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_ambiente_laboral">Ambiente laboral</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_equipo_trabajo" value="equipo_de_trabajo"
                      {{ in_array('equipo_de_trabajo', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_equipo_trabajo">Equipo de trabajo</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_cumplimiento_reglas" value="cumplimiento_reglas"
                      {{ in_array('cumplimiento_reglas', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_cumplimiento_reglas">Cumplimiento de reglas</label>
              </div>
            </div>

            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="motivos[]"
                      id="m_falta_recursos" value="falta_de_recursos"
                      {{ in_array('falta_de_recursos', $motivosOld) ? 'checked' : '' }}>
                <label class="form-check-label" for="m_falta_recursos">Falta de recursos</label>
              </div>
            </div>

            <div class="col-12">
              <div class="row g-2 align-items-center">
                <div class="col-auto">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                          id="motivo_otro_chk" name="motivos[]" value="otro"
                          {{ in_array('otro', $motivosOld) ? 'checked' : '' }}>
                    <label class="form-check-label" for="motivo_otro_chk">Otro</label>
                  </div>
                </div>
                <div class="col">
                  <input type="text" id="motivo_otro_input" name="motivo_otro" class="form-control"
                        placeholder="Especifique"
                        value="{{ old('motivo_otro') }}"
                        {{ in_array('otro', $motivosOld) ? '' : 'disabled' }}>
                </div>
              </div>
            </div>
          </div>

          @error('motivos') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
          @error('motivo_otro') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
        </div>

          <div class="border rounded-3 p-3 mb-3">
            <h2 class="h6 fw-semibold mb-2">Descripción</h2>
            <p class="text-muted small mb-2">* Describa su experiencia e indique la fecha en que sucedió *</p>
            <textarea name="descripcion" rows="5" class="form-control" required>{{ old('descripcion') }}</textarea>
            @error('descripcion') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4 pe-3">
            <button type="submit" class="btn btn-warning text-white">Envia tu experiencia</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const chkOtro = document.getElementById('motivo_otro_chk');
      const inpOtro = document.getElementById('motivo_otro_input');
      if (chkOtro && inpOtro) {
        chkOtro.addEventListener('change', function(){
          inpOtro.disabled = !this.checked;
          if (!this.checked) inpOtro.value = '';
        });
      }
    })();
  </script>
@endsection
