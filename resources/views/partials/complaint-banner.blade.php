<style>[data-complaint-banner][hidden]{display:none!important}</style>

<div data-complaint-banner class="alert alert-warning py-1 px-3 d-flex align-items-center justify-content-between" role="alert" style="border-width:1px;">
  <div class="d-flex align-items-center gap-2 me-2 text-truncate">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
         class="bi bi-info-circle flex-shrink-0" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
      <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416zM8 5.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
    </svg>
    <div class="fs-5 text-truncate">
    ¡Cuéntanos tu experiencia! <span class="d-none d-sm-inline">Tu opinión es importante.</span>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2 flex-shrink-0">
    <a href="{{ route('complaints.create') }}"class="btn btn-warning text-white fw-semibold fs-5 py-1 px-3">Queremos escucharte</a>
    <button type="button" class="btn-close btn-close-sm ms-1" aria-label="Cerrar" data-complaint-close></button>
  </div>
</div>

<script>
  (function () {
    try { localStorage.removeItem('complaintBanner'); sessionStorage.removeItem('complaintBanner'); } catch(e) {}

    const el  = document.querySelector('[data-complaint-banner]');
    if (!el) return;

    el.querySelector('[data-complaint-close]')?.addEventListener('click', () => {
      el.hidden = true; 
    });
  })();
</script>
