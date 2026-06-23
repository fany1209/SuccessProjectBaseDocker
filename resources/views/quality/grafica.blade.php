<div class="flex gap-2 items-end mb-3">
  <div>
    <label class="text-sm">From</label>
    <input type="date" id="from-date" class="border rounded px-2 py-1">
  </div>
  <div>
    <label class="text-sm">To</label>
    <input type="date" id="to-date" class="border rounded px-2 py-1">
  </div>
  <button id="btn-refresh" class="px-3 py-2 bg-emerald-600 text-white rounded">Update</button>
</div>

<div id="donut-pdf-generations" style="height:360px"></div>

<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
  google.charts.load('current', { packages: ['corechart'] });

  $(function () {
    const $el      = $('#donut-pdf-generations');
    const endpoint = "{{ route('charts.pdf-generations') }}";

    google.charts.setOnLoadCallback(init);

    function init() {
      bindHandlers();
      fetchAndDraw(); 
    }

    function buildUrl() {
      const from = $('#from-date').val();
      const to   = $('#to-date').val();
      const url  = new URL(endpoint, window.location.origin);
      if (from) url.searchParams.set('from', from);
      if (to)   url.searchParams.set('to', to);
      return url.toString();
    }

    function fetchAndDraw() {
      $el.html('<div class="p-3 text-gray-600">Loading…</div>');

      $.getJSON(buildUrl())
        .done(function (resp) {
          const rows = (resp.data || []).slice(1);
          if (!rows.length) {
            $el.html('<div class="p-3 text-gray-600">No data to show</div>');
            return;
          }

          const data = google.visualization.arrayToDataTable(resp.data);
          const options = {
            title: 'PDF generados por tipo',
            pieHole: 0.55,
            legend: { position: 'right' },
            chartArea: { width: '85%', height: '85%' },
            tooltip: { text: 'percentage' },
            pieSliceText: 'value'
          };

          const chart = new google.visualization.PieChart($el[0]);
          chart.draw(data, options);
        })
        .fail(function () {
          $el.html('<div class="p-3 text-red-600">Error loading data</div>');
        });
    }

    function bindHandlers() {
      $('#btn-refresh').on('click', function (e) {
        e.preventDefault();
        fetchAndDraw();
      });

      const debouncedRefresh = debounce(fetchAndDraw, 300);
      $('#from-date, #to-date').on('change', debouncedRefresh);

      $(window).on('resize', throttle(fetchAndDraw, 200));
      $(document).on('submit', '#pdf4-form, #pdf8-form, #pdf10-form', function () {
        setTimeout(fetchAndDraw, 1200);
      });

      $(document).on('pdf:generated', fetchAndDraw);
    }

    function debounce(fn, wait) {
      let t;
      return function () {
        clearTimeout(t);
        t = setTimeout(fn, wait);
      };
    }
    function throttle(fn, wait) {
      let last = 0;
      return function () {
        const now = Date.now();
        if (now - last >= wait) {
          last = now;
          fn();
        }
      };
    }
  });
</script>
