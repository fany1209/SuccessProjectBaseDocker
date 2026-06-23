<x-modal id="edit-receptionq">
  <div class="p-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ===================== RESUMEN ===================== --}}
    <div class="lg:col-span-2">
      <input type="text" id="receptionq-summary-filter"
             class="w-full rounded-md border px-3 py-2 mb-2"
             placeholder="Filtrar campos (ej. batch, product, supplier, ...)" />

      <div class="border rounded">
        <div class="max-h-[60vh] overflow-auto">
          <table class="w-full text-sm" id="receptionq-summary-table">
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ===================== FORM ===================== --}}
    <div class="lg:col-span-1">
      <form id="edit-receptionq-form" method="POST">
        @csrf
        @method('PATCH')

        <input type="hidden" name="id" id="recepcionq-id">

        <div class="space-y-4">

          {{-- ===== OBJETIVO DE LA MUESTRA ===== --}}
          <div class="p-3 border rounded">
            <label class="block text-sm font-semibold mb-1">
              Objetivo de la muestra
            </label>

            <div class="flex flex-wrap gap-4 text-sm">
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="objetivo_muestra" value="inspeccion">
                Inspección
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="objetivo_muestra" value="retencion">
                Retención
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="objetivo_muestra" value="analisis">
                Análisis
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="objetivo_muestra" value="desarrollo">
                Desarrollo
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="objetivo_muestra" value="exposicion">
                Exposición
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="objetivo_muestra" value="otro">
                Otro
              </label>

              <input
                id="obj_otro_txt"
                type="text"
                name="objetivo_otro"
                class="border rounded px-2 py-1"
                placeholder="Especifique"
                style="min-width:220px"
                disabled
              >
            </div>
          </div>

          {{-- ===== DOCUMENTACIÓN ANEXA ===== --}}
          <div class="p-3 border rounded">
            <label class="block text-sm font-semibold mb-1">
              Documentación anexa
            </label>

            <div class="flex flex-wrap gap-4 text-sm">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="docs_ccf" id="docs_ccf" value="1">
                CCF
              </label>

              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="docs_ft" id="docs_ft" value="1">
                FT
              </label>

              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="docs_hs" id="docs_hs" value="1">
                HS
              </label>

              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="docs_otro" id="doc_otro_chk" value="1">
                Otro
              </label>

              <input
                id="doc_otro_txt"
                type="text"
                name="docs_otro_txt"
                class="border rounded px-2 py-1"
                placeholder="Especifique"
                style="min-width:220px"
                disabled
              >
            </div>
          </div>

          {{-- ===== OBSERVACIONES CALIDAD ===== --}}
          <div>
            <label class="block text-sm font-semibold">
              Observaciones calidad
            </label>
            <textarea id="observaciones_calidad"
                      name="observaciones"
                      rows="12"
                      class="w-full border rounded px-2 py-2"></textarea>
          </div>

        </div>

        <div class="flex items-center justify-end gap-2 mt-4">
          <button type="button" class="modal-close px-3 py-2 rounded border">
            Cancelar
          </button>
          <button type="submit"
                  class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded">
            Guardar
          </button>
        </div>

      </form>
    </div>

  </div>
</x-modal>


<script>
  window.__ROUTE_SHOW__    = @json(route('reception.show', ['id' => '__ID__']));
  window.__ROUTE_UPDATEQ__ = @json(route('reception.quality.updateq', ['id' => '__ID__']));
</script>
@push('js')
<script>
/* ===================== CSRF ===================== */
$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
});

/* ===================== FORMATOS ===================== */
function fmtDate(iso){ if(!iso) return '—'; const d=new Date(iso); return isNaN(d)?iso:d.toLocaleDateString(); }
function fmtBool(v){ return v ? 'Sí' : 'No'; }
function fmtStr(v){ return (v===null||v===undefined||String(v).trim()==='')?'—':String(v); }

function mapEnumOrigen(v){
  const m={proveedor:'Proveedor',produccion:'Producción',almacen:'Almacén',otro:'Otro'};
  return m[v] ?? fmtStr(v);
}
function mapEnumObjetivo(v){
  const m={inspeccion:'Inspección',retencion:'Retención',analisis:'Análisis',desarrollo:'Desarrollo',exposicion:'Exposición',otro:'Otro'};
  return m[v] ?? fmtStr(v);
}
function mapEnumUM(v){
  const m={g:'g',kg:'kg',l:'l',ml:'ml',otro:'Otro'};
  return m[v] ?? fmtStr(v);
}

/* ===================== RESUMEN COMPLETO (NO SE BORRA NADA) ===================== */
const FIELDS_SCHEMA = [
  ['id','ID',v=>fmtStr(v)],
  ['folio_muestra','Folio muestra',v=>fmtStr(v)],
  ['product_id','Producto',v=>fmtStr(v)],
  ['nombre_comercial','Nombre comercial',v=>fmtStr(v)],
  ['sku','SKU',v=>fmtStr(v)],
  ['batch','Lote',v=>fmtStr(v)],
  ['fecha_entrada','Fecha de entrada',v=>fmtDate(v)],
  ['fecha_caducidad','Fecha de caducidad',v=>fmtDate(v)],
  ['descripcion','Descripción',v=>fmtStr(v)],
  ['origen_muestra','Origen de la muestra',v=>mapEnumOrigen(v)],
  ['origen_otro','Origen — Otro',v=>fmtStr(v)],
  ['objetivo_muestra','Objetivo de la muestra',v=>mapEnumObjetivo(v)],
  ['objetivo_otro','Objetivo — Otro',v=>fmtStr(v)],
  ['cantidad','Cantidad',v=>fmtStr(v)],
  ['um','Unidad de medida',v=>mapEnumUM(v)],
  ['um_otro','UM — Otro',v=>fmtStr(v)],
  ['supplier_id','ID proveedor',v=>fmtStr(v)],
  ['docs_ccf','Doc. CCF',v=>fmtBool(!!v)],
  ['docs_ft','Doc. FT',v=>fmtBool(!!v)],
  ['docs_hs','Doc. HS',v=>fmtBool(!!v)],
  ['docs_otro','Doc. Otro',v=>fmtBool(!!v)],
  ['docs_otro_txt','Doc. Otro — Texto',v=>fmtStr(v)],
  ['observaciones','Observaciones (generales)',v=>fmtStr(v)],
  ['observaciones_laboratorio','Observaciones laboratorio',v=>fmtStr(v)],
  ['firma_entrega_nombre','Firma — Entrega',v=>fmtStr(v)],
  ['firma_recepcion_nombre','Firma — Recepción',v=>fmtStr(v)],
];

function renderAllFields(tbody, data){
  tbody.innerHTML='';
  for(const [k,l,f] of FIELDS_SCHEMA){
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td class="align-top border-b p-2 w-64 font-medium bg-gray-50">${l}</td>
      <td class="align-top border-b p-2">${f(data?.[k])}</td>`;
    tbody.appendChild(tr);
  }
}

/* ===================== HABILITAR "OTRO" ===================== */
$(document).on('change','input[name="objetivo_muestra"]',function(){
  if(this.value==='otro'){
    $('#obj_otro_txt').prop('disabled',false).focus();
  }else{
    $('#obj_otro_txt').prop('disabled',true).val('');
  }
});

/* ===================== ABRIR MODAL ===================== */
$(document).on('click','.edit-yellow-btn',async function(){
  const id=this.dataset.id;
  if(!id) return;

  const showUrl=window.__ROUTE_SHOW__.replace('__ID__',id);
  const updateUrl=window.__ROUTE_UPDATEQ__.replace('__ID__',id);

  const form=document.getElementById('edit-receptionq-form');
  form.reset();
  form.action=updateUrl;
  $('#recepcionq-id').val(id);

  const r=await fetch(showUrl,{headers:{Accept:'application/json'}});
  if(r.ok){
    const full=await r.json();
    const rec=full?.recepcion ?? full ?? {};

    renderAllFields(
      document.querySelector('#receptionq-summary-table tbody'),
      rec
    );

    // Precargar objetivo
    if(rec.objetivo_muestra){
      $(`input[name="objetivo_muestra"][value="${rec.objetivo_muestra}"]`)
        .prop('checked',true)
        .trigger('change');
    }

    $('#obj_otro_txt').val(rec.objetivo_otro ?? '');
    $('#observaciones_calidad').val(rec.observaciones ?? '');
  }

  openModalById?.('edit-receptionq');
});

/* ===================== SUBMIT ===================== */
$(document).on('submit','#edit-receptionq-form',function(e){
  e.preventDefault();

  const $f=$(this);
  const btn=$f.find('button[type="submit"]');
  const prev=btn.text();

  btn.prop('disabled',true).text('Guardando…');

  $.post($f.attr('action'),$f.serialize())
    .done(res=>{
      Swal.fire({icon:'success',title:'Guardado',text:res?.message||'Saved'});
      closeModalById?.('edit-receptionq');
      $('#reception-table').DataTable()?.ajax.reload(null,false);
    })
    .fail(xhr=>{
      Swal.fire({icon:'error',title:'Error',text:xhr.responseJSON?.message||'Error'});
    })
    .always(()=>{
      btn.prop('disabled',false).text(prev);
    });
});
</script>
@endpush
